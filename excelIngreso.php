<?php
include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/DetalleIngresoData.php";
include "PHPExcel-1.8/Classes/PHPExcel/IOFactory.php";

$inputFileName = "rptIngreso.xlsx";

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

$lstDetalleIngreso = DetalleIngresoData::getAllByReport($sede, $almacen, $fechaInicio, $fechaFin, $estado);

// Read the existing excel file
$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$inicio = 2;
if (count($lstDetalleIngreso) > 0) {
    foreach ($lstDetalleIngreso as $objDetalleIngreso) {
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A' . $inicio, str_pad($objDetalleIngreso->id, 8, "0", STR_PAD_LEFT))
            ->setCellValue('B' . $inicio, $objDetalleIngreso->insumo)
            ->setCellValue('C' . $inicio, $objDetalleIngreso->unidad)
            ->setCellValue('D' . $inicio, number_format($objDetalleIngreso->cantidad, 2, ".", ""));
    
        $inicio ++;
    }
}
$objPHPExcel->setActiveSheetIndex(0);

for ($i = 'A'; $i != $objPHPExcel->getActiveSheet()->getHighestColumn(); $i ++) {
    $objPHPExcel->getActiveSheet()
        ->getColumnDimension($i)
        ->setAutoSize(true);
}

$outuputFileName = "rptIngresoGenerado" . date('YmdHis') . ".xlsx";

// Generate an updated excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $outuputFileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

?>