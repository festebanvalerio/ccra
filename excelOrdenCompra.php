<?php
include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/DetalleOrdenCompraData.php";
include "PHPExcel-1.8/Classes/PHPExcel/IOFactory.php";

$inputFileName = "rptOrdenCompra.xlsx";

$sede = "";
if (isset($_GET["sede"])) {
    $sede = $_GET["sede"];
}

$almacen = "";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}

$fechaInicio = "";
if (isset($_GET["fechainicio"])) {
    $fechaInicio = $_GET["fechainicio"];
}

$fechaFin = "";
if (isset($_GET["fechafin"])) {
    $fechaFin = $_GET["fechafin"];
}

$estado = "";
if (isset($_GET["estado"])) {
    $estado = $_GET["estado"];
}

$lstDetalleOrdenCompra = DetalleOrdenCompraData::getAllByReport($sede, $almacen, $fechaInicio, $fechaFin, $estado);

// Read the existing excel file
$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$inicio = 2;
if (count($lstDetalleOrdenCompra) > 0) {
    foreach ($lstDetalleOrdenCompra as $objDetalleOrdenCompra) {
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A' . $inicio, str_pad($objDetalleOrdenCompra->id, 8, "0", STR_PAD_LEFT))
            ->setCellValue('B' . $inicio, $objDetalleOrdenCompra->tipo_documento)
            ->setCellValue('C' . $inicio, $objDetalleOrdenCompra->num_documento)
            ->setCellValue('D' . $inicio, $objDetalleOrdenCompra->ruc)
            ->setCellValue('E' . $inicio, $objDetalleOrdenCompra->razon_social)
            ->setCellValue('F' . $inicio, $objDetalleOrdenCompra->insumo)
            ->setCellValue('G' . $inicio, $objDetalleOrdenCompra->unidad) 
            ->setCellValue('H' . $inicio, number_format($objDetalleOrdenCompra->costo, 2, ".", ""))
            ->setCellValue('I' . $inicio, number_format($objDetalleOrdenCompra->cantidad, 2, ".", ""));                       
    
        $inicio ++;    							
    }
}
$objPHPExcel->setActiveSheetIndex(0);

for ($i = 'A'; $i != $objPHPExcel->getActiveSheet()->getHighestColumn(); $i ++) {
    $objPHPExcel->getActiveSheet()
        ->getColumnDimension($i)
        ->setAutoSize(true);
}

$outuputFileName = "rptOrdenCompraGenerado" . date('YmdHis') . ".xlsx";

// Generate an updated excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $outuputFileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

?>