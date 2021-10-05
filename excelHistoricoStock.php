<?php
include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/DetalleHistoricoStockData.php";
include "core/app/model/MovimientoData.php";
include "core/app/model/InsumoAlmacenData.php";
include "PHPExcel-1.8/Classes/PHPExcel/IOFactory.php";

$inputFileName = "rptHistoricoStock.xlsx";

$sede = "";
if (isset($_GET["sede"])) {
    $sede = $_GET["sede"];
}

$almacen = "";
if (isset($_GET["almacen"])) {
    $almacen = $_GET["almacen"];
}

$lstIdInsumo = "";
if (isset($_GET["insumo"])) {
    $lstIdInsumo = $_GET["insumo"];
}

$lstIdClasificacion = "";
if (isset($_GET["clasificacion"])) {
    $lstIdClasificacion = $_GET["clasificacion"];
}

$fecha = "";
if (isset($_GET["fecha"])) {
    $fecha = $_GET["fecha"];
}

$lstDetalleHistoricoStock = DetalleHistoricoStockData::getAll($sede, $almacen, $fecha, $lstIdInsumo, $lstIdClasificacion);

// Read the existing excel file
$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$inicio = 6;
if (count($lstDetalleHistoricoStock) > 0) {
    $objPHPExcel->getActiveSheet()->setCellValue('H3', $fecha);
    
    $item = 1;
    foreach ($lstDetalleHistoricoStock as $objDetalleHistoricoStock) {
        $totalEntradas = $objDetalleHistoricoStock->getTotal(1, $sede, $objDetalleHistoricoStock->insumo, 1, $fecha);
        $totalSalidas = $objDetalleHistoricoStock->getTotal(1, $sede, $objDetalleHistoricoStock->insumo, -1, $fecha);
        
        $color1 = "0865F3";
        $saldoFinal = $objDetalleHistoricoStock->stock + $totalEntradas->total - $totalSalidas->total;
        if ($saldoFinal < 0) {
            $color1 = "F31608";
        }
        
        $phpColor1 = new PHPExcel_Style_Color();
        $phpColor1->setRGB($color1);
        
        $color2 = "0865F3";
        $diferencia = $objDetalleHistoricoStock->stock - $saldoFinal;
        if ($diferencia < 0) {
            $color2 = "F31608";
        }
        
        $phpColor2 = new PHPExcel_Style_Color();
        $phpColor2->setRGB($color2);
        
        $stockActual = 0;
        $lstInsumoXAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objDetalleHistoricoStock->insumo, $almacen);
        if (count($lstInsumoXAlmacen) == 1) {
            $stockActual = $lstInsumoXAlmacen[0]->stock;
        }
        
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A' . $inicio, $item++)
            ->setCellValue('B' . $inicio, $objDetalleHistoricoStock->nom_insumo)
            ->setCellValue('C' . $inicio, $objDetalleHistoricoStock->clasificacion)
            ->setCellValue('D' . $inicio, $objDetalleHistoricoStock->unidad_medida)
            ->setCellValue('E' . $inicio, number_format($objDetalleHistoricoStock->stock, 2, ".", ""))
            ->setCellValue('F' . $inicio, number_format($totalEntradas->total, 2, ".", ""))
            ->setCellValue('G' . $inicio, number_format($totalSalidas->total, 2, ".", ""))
            ->setCellValue('H' . $inicio, number_format($saldoFinal, 2, ".", ""))
            ->setCellValue('I' . $inicio, number_format($stockActual, 2, ".", ""));

            $objPHPExcel->getActiveSheet()->getStyle('H' . $inicio)->getFont()->setColor($phpColor1);
            $objPHPExcel->getActiveSheet()->getStyle('I' . $inicio)->getFont()->setColor($phpColor2);
            
        $inicio ++;
    }
}
$objPHPExcel->setActiveSheetIndex(0);

for ($i = 'A'; $i != $objPHPExcel->getActiveSheet()->getHighestColumn(); $i ++) {
    $objPHPExcel->getActiveSheet()
        ->getColumnDimension($i)
        ->setAutoSize(true);
}

$outuputFileName = "rptStockGenerado" . date('YmdHis') . ".xlsx";

// Generate an updated excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $outuputFileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

?>