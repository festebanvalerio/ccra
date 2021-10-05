<?php
include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/DetallePedidoData.php";
include "PHPExcel-1.8/Classes/PHPExcel/IOFactory.php";

$inputFileName = "rptProductosEliminados.xlsx";

$sede = "";
if (isset($_GET["sede"])) {
    $sede = $_GET["sede"];
}

$fechaInicio = "";
if (isset($_GET["fechaInicio"])) {
    $fechaInicio = $_GET["fechaInicio"];
}

$fechaFin = "";
if (isset($_GET["fechaFin"])) {
    $fechaFin = $_GET["fechaFin"];
}

$lstDetallePedido = DetallePedidoData::getAllProductoEliminado($sede, $fechaInicio, $fechaFin);

// Read the existing excel file
$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$inicio = 2;
if (count($lstDetallePedido) > 0) {
    $item = 1;
    foreach ($lstDetallePedido as $objDetallePedido) {
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A' . $inicio, $item++)
            ->setCellValue('B' . $inicio, date("d/m/Y H:i", strtotime($objDetallePedido->fecha_creacion)))
            ->setCellValue('C' . $inicio, date("d/m/Y H:i", strtotime($objDetallePedido->fecha_actualizacion)))
            ->setCellValue('D' . $inicio, str_pad($objDetallePedido->id, 8, "0", STR_PAD_LEFT))
            ->setCellValue('E' . $inicio, $objDetallePedido->producto)
            ->setCellValue('F' . $inicio, number_format($objDetallePedido->cantidad, 2, ".", ""))
            ->setCellValue('G' . $inicio, number_format($objDetallePedido->precio_venta, 2, ".", ""))
            ->setCellValue('H' . $inicio, number_format($objDetallePedido->total, 2, ".", ""))
            ->setCellValue('I' . $inicio, $objDetallePedido->estado);

        $inicio ++;
    }
}
$objPHPExcel->setActiveSheetIndex(0);

for ($i = 'A'; $i != $objPHPExcel->getActiveSheet()->getHighestColumn(); $i ++) {
    $objPHPExcel->getActiveSheet()
        ->getColumnDimension($i)
        ->setAutoSize(true);
}

$outuputFileName = "rptGenerado" . date('YmdHis') . ".xlsx";

// Generate an updated excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $outuputFileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

?>