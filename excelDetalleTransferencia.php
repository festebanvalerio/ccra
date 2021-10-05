<?php
include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/TransferenciaData.php";
include "core/app/model/DetalleTransferenciaData.php";
include "core/app/model/AlmacenData.php";
include "core/app/model/InsumoData.php";
include "core/app/model/UnidadData.php";
include "core/app/model/SedeData.php";
include "PHPExcel-1.8/Classes/PHPExcel/IOFactory.php";

$inputFileName = 'rptDetalleTransferencia.xlsx';

$nomSedeOrigen = $nomSedeDestino = $nomAlmacenOrigen = $nomAlmacenDestino = "";
$lstDetalleTransferencia = NULL;
if (isset($_GET["transferencia"])) {
    $id = $_GET["transferencia"];
    
    $objTransferencia = TransferenciaData::getById($id);
    $nomSedeOrigen = $objTransferencia->getSedeOrigen()->nombre;
    $nomSedeDestino = $objTransferencia->getSedeDestino()->nombre;
    $nomAlmacenOrigen = $objTransferencia->getAlmacenOrigen()->nombre;
    $nomAlmacenDestino = $objTransferencia->getAlmacenDestino()->nombre;
    
    $lstDetalleTransferencia = DetalleTransferenciaData::getAllByTransferencia($id);
}

// Read the existing excel file
$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$inicio = 2;
if (count($lstDetalleTransferencia) > 0) {
    foreach ($lstDetalleTransferencia as $objDetalleTransferencia) {
        $objInsumo = $objDetalleTransferencia->getInsumo();
        $objUnidad = $objInsumo->getUnidad();
        
        $objPHPExcel->getActiveSheet()
        ->
        setCellValue('A' . $inicio, $nomSedeOrigen)
        ->
        setCellValue('B' . $inicio, $nomAlmacenOrigen)
        ->
        setCellValue('C' . $inicio, $nomSedeDestino)
        ->
        setCellValue('D' . $inicio, $nomAlmacenDestino)
        ->
        setCellValue('E' . $inicio, $objInsumo->nombre)
        ->
        setCellValue('F' . $inicio, $objUnidad->abreviatura)
        ->
        setCellValue('G' . $inicio, number_format($objDetalleTransferencia->cantidad, 2, ".", ""));
        
        $inicio ++;
    }
}
$objPHPExcel->setActiveSheetIndex(0);

for ($i = 'A'; $i !=  $objPHPExcel->getActiveSheet()->getHighestColumn(); $i++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
}

$outuputFileName = "rptDetalleTransferencia".date('YmdHis').".xlsx";

// Generate an updated excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $outuputFileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

?>