<?php
include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/HistorialDocumentoData.php";
include "core/app/model/ComprobanteData.php";
include "core/app/model/EstadoData.php";
include "PHPExcel-1.8/Classes/PHPExcel/IOFactory.php";

session_start();

$inputFileName = "rptComprobantes.xlsx";

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

$tipo = "";
if (isset($_GET["tipo"])) {
    $tipo = $_GET["tipo"];
}

$exonerado = $_SESSION["exonerado"];

$lstHistorialDocumento = HistorialDocumentoData::getAll($estado, $sede, $tipo, $fechaInicio, $fechaFin);

// Read the existing excel file
$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$inicio = 2;
if (count($lstHistorialDocumento) > 0) {
    foreach ($lstHistorialDocumento as $objHistorialDocumento) {
        $objComprobante = $objHistorialDocumento->getComprobante();
        $objEstado = $objHistorialDocumento->getEstado();
        $montoComprobante = $objComprobante->fe_comprobante_totvengra;
        if ($exonerado == 1) {
            $montoComprobante = $objComprobante->fe_comprobante_totvenexo;
        }
        
        $objPHPExcel->getActiveSheet()
            ->setCellValue("A" . $inicio, date("d/m/Y", strtotime($objComprobante->fe_comprobante_reg)))
            ->setCellValue("B" . $inicio, $objComprobante->fe_comprobante_ser."-".$objComprobante->fe_comprobante_cor)
            ->setCellValue("C" . $inicio, $objComprobante->tb_cliente_numdoc."-".$objComprobante->tb_cliente_nom)
            ->setCellValue("D" . $inicio, number_format($montoComprobante, 2, ".", ""))
            ->setCellValue("E" . $inicio, number_format($objComprobante->fe_comprobante_sumigv, 2, ".", ""))
            ->setCellValue("F" . $inicio, number_format($objComprobante->fe_comprobante_imptot, 2, ".", ""))
            ->setCellValue("G" . $inicio, $objEstado->nombre);
            
        $inicio ++;
    }
}

$objPHPExcel->setActiveSheetIndex(0);

for ($i = "A"; $i != $objPHPExcel->getActiveSheet()->getHighestColumn(); $i ++) {
    $objPHPExcel->getActiveSheet()
        ->getColumnDimension($i)
        ->setAutoSize(true);
}

$outuputFileName = "rptComprobanteGenerado" . date("YmdHis") . ".xlsx";

// Generate an updated excel file
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Disposition: attachment;filename="' . $outuputFileName . '"');
header("Cache-Control: max-age=0");
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
$objWriter->save("php://output");

?>