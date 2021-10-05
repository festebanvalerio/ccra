<?php
include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/PagoData.php";
include "core/app/model/PedidoData.php";
include "core/app/model/EstadoData.php";
include "core/app/model/ParametroData.php";
include "PHPExcel-1.8/Classes/PHPExcel/IOFactory.php";

$inputFileName = "rptPagos.xlsx";

$sede = "";
if (isset($_GET["sede"])) {
    $sede = $_GET["sede"];
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

$lstPago = PagoData::getAll($estado, $sede, $fechaInicio, $fechaFin);

// Read the existing excel file
$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$inicio = 2;
if (count($lstPago) > 0) {
    foreach ($lstPago as $objPago) {
        $objEstado = $objPago->getEstado();
        $objPedido = $objPago->getPedido();
        $objTipoPago = $objPago->getTipoPago();
        
        $montoEfectivo = $objPago->monto_pagado_efectivo;
        $montoTarjeta = $objPago->monto_pagado_tarjeta;
        $montoTotal = $objPago->monto_total;
        $montoDescuento = $objPago->monto_descuento;
        $montoPagado = $montoEfectivo + $montoTarjeta;
        
        $objPHPExcel->getActiveSheet()
            ->setCellValue("A" . $inicio, str_pad($objPedido->id, 8, "0", STR_PAD_LEFT))
            ->setCellValue("B" . $inicio, str_pad($objPago->id, 8, "0", STR_PAD_LEFT))
            ->setCellValue("C" . $inicio, date("d/m/Y H:i", strtotime($objPago->fecha_creacion)))
            ->setCellValue("D" . $inicio, $objPedido->getTipo()->nombre)
            ->setCellValue("E" . $inicio, $objTipoPago->nombre)
            ->setCellValue("F" . $inicio, number_format($montoTotal, 2, ".", ""))
            ->setCellValue("G" . $inicio, number_format($montoDescuento, 2, ".", "")) 
            ->setCellValue("H" . $inicio, number_format($montoPagado, 2, ".", ""))
            ->setCellValue("I" . $inicio, number_format($montoEfectivo, 2, ".", ""))
            ->setCellValue("J" . $inicio, number_format($montoTarjeta, 2, ".", ""))
            ->setCellValue("K" . $inicio, $objEstado->nombre);
            
        $inicio ++;
    }
}

$objPHPExcel->setActiveSheetIndex(0);

for ($i = "A"; $i != $objPHPExcel->getActiveSheet()->getHighestColumn(); $i ++) {
    $objPHPExcel->getActiveSheet()
        ->getColumnDimension($i)
        ->setAutoSize(true);
}

$outuputFileName = "rptPagosGenerado" . date("YmdHis") . ".xlsx";

// Generate an updated excel file
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Disposition: attachment;filename="' . $outuputFileName . '"');
header("Cache-Control: max-age=0");
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
$objWriter->save("php://output");

?>