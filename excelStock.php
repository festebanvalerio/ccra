<?php
include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/AlmacenData.php";
include "core/app/model/InsumoAlmacenData.php";
include "core/app/model/InsumoData.php";
include "core/app/model/UnidadData.php";
include "core/app/model/SedeData.php";
include "PHPExcel-1.8/Classes/PHPExcel/IOFactory.php";

$inputFileName = 'rptStock.xlsx';

session_start();

$lstStock = array();
$lstAlmacen = AlmacenData::getAll(1, $_SESSION["empresa"], $_SESSION["sede"]);
if (count($lstAlmacen) > 0) {
    $objAlmacen = $lstAlmacen[0];
    $lstStock = InsumoAlmacenData::getAllByInsumo(1, "", $objAlmacen->id); 
}

// Read the existing excel file
$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$inicio = 2;
if (count($lstStock) > 0) {
    foreach ($lstStock as $objStock) {
        $objAlmacen = $objStock->getAlmacen();
        $objInsumo = $objStock->getInsumo();
        $objUnidad = $objInsumo->getUnidad();
        $objSede = $objAlmacen->getSede();
        
        $objPHPExcel->getActiveSheet()
        ->
        setCellValue('A' . $inicio, $objSede->nombre)
        ->
        setCellValue('B' . $inicio, $objAlmacen->nombre)
        ->
        setCellValue('C' . $inicio, $objInsumo->nombre)
        ->
        setCellValue('D' . $inicio, $objUnidad->abreviatura)
        ->
        setCellValue('E' . $inicio, number_format($objStock->stock, 2, ".", ""));
        
        $inicio ++;
    }
}
$objPHPExcel->setActiveSheetIndex(0);

for ($i = 'A'; $i !=  $objPHPExcel->getActiveSheet()->getHighestColumn(); $i++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
}

$outuputFileName = "rptStock".date('YmdHis').".xlsx";

// Generate an updated excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $outuputFileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

?>