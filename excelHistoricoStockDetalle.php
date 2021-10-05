<?php
include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/DetalleHistoricoStockData.php";
include "core/app/model/MovimientoData.php";
include "core/app/model/InsumoAlmacenData.php";
include "PHPExcel-1.8/Classes/PHPExcel/IOFactory.php";

$inputFileName = "rptHistoricoStockDetallado.xlsx";

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

$fechaInicio = "";
if (isset($_GET["fechaInicio"])) {
    $fechaInicio = $_GET["fechaInicio"];
}

$fechaFin = "";
if (isset($_GET["fechaFin"])) {
    $fechaFin = $_GET["fechaFin"];
}

$lstDetalleHistoricoStock = DetalleHistoricoStockData::getDetalle($sede, $almacen, $fechaInicio, $fechaFin, $lstIdInsumo, $lstIdClasificacion);

$arrFecha = explode("/", $fechaInicio);
$fechaInicioBus = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];

$arrFecha = explode("/", $fechaFin);
$fechaFinBus = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];

$lstFecha = array();
$sd = strtotime($fechaInicioBus);
$ed = strtotime($fechaFinBus);
for ($i = $sd; $i <= $ed; $i += (60 * 60 * 24)) {
    $lstFecha[] = date("d/m/Y", $i);
}

// Read the existing excel file
$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$array = array("F,G,H","I,J,K","L,M,N","O,P,Q","R,S,T","U,V,W","X,Y,Z","AA,AB,AC","AD,AE,AF","AG,AH,AI","AJ,AK,AL","AM,AN,AO","AP,AQ,AR","AS,AT,AU","AV,AW,AX",
        "AY,AZ,BA","BB,BC,BD","BE,BF,BG","BH,BI,BJ","BK,BL,BM","BN,BO,BP","BQ,BR,BS","BT,BU,BV","BW,BX,BY","BZ,CA,CB","CC,CD,CE","CF,CG,CH","CI,CJ,CK","CL,CM,CN");

$inicioCabecera = 1;
$inicio = 3;
if (count($lstDetalleHistoricoStock) > 0) {
    $indice = 0;
    foreach($lstFecha as $dataFecha) {
        $arrData = explode(",", $array[$indice++]);
        $objPHPExcel->getActiveSheet()->mergeCells($arrData[0] . $inicioCabecera . ":" . $arrData[2] . $inicioCabecera);
        $objPHPExcel->getActiveSheet()->setCellValue($arrData[0]. $inicioCabecera, $dataFecha);
        $objPHPExcel->getActiveSheet()->setCellValue($arrData[0] . ($inicioCabecera + 1), "INGRESA");
        $objPHPExcel->getActiveSheet()->setCellValue($arrData[1] . ($inicioCabecera + 1), "QUEDA");
        $objPHPExcel->getActiveSheet()->setCellValue($arrData[2] . ($inicioCabecera + 1), "STOCK");
    }
    
    $item = 1;
    foreach ($lstDetalleHistoricoStock as $objDetalleHistoricoStock) {        
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A' . $inicio, $item++)
            ->setCellValue('B' . $inicio, $objDetalleHistoricoStock->nom_insumo)
            ->setCellValue('C' . $inicio, $objDetalleHistoricoStock->clasificacion)
            ->setCellValue('D' . $inicio, $objDetalleHistoricoStock->unidad_medida);
            
        $indice = 0;
        foreach($lstFecha as $dataFecha) {
            $stockInicial = $ingresos = $stockActual = 0;
            $lstDetalleHistoricoStockTmp = DetalleHistoricoStockData::getDetalleXInsumo($sede, $almacen, $dataFecha, $dataFecha, $objDetalleHistoricoStock->insumo);
            if (count($lstDetalleHistoricoStockTmp) == 1) {
                $stockInicial = $lstDetalleHistoricoStockTmp[0]->stock;
                
                $totalEntradas = $objDetalleHistoricoStock->getTotal(1, $sede, $objDetalleHistoricoStock->insumo, 1, $dataFecha, 1);
                $ingresos = $totalEntradas->total;
                
                $arrFecha = explode("/", $dataFecha);
                $fechaTmp = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
                
                $fecha = date_create($fechaTmp);
                date_add($fecha, date_interval_create_from_date_string("1 days"));
                $dataFechaTmp = date_format($fecha, "d/m/Y");
                
                $lstDetalleHistoricoStockTmp = DetalleHistoricoStockData::getDetalleXInsumo($sede, $almacen, $dataFechaTmp, $dataFechaTmp, $objDetalleHistoricoStock->insumo);
                if (count($lstDetalleHistoricoStockTmp) == 1) {
                    $stockActual = $lstDetalleHistoricoStockTmp[0]->stock;
                } else {
                    $lstInsumoXAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objDetalleHistoricoStock->insumo, $almacen);
                    if (count($lstInsumoXAlmacen) == 1) {
                        $stockActual = $lstInsumoXAlmacen[0]->stock;
                    }
                }
            }
            $arrData = explode(",", $array[$indice++]);
            $objPHPExcel->getActiveSheet()->setCellValue($arrData[0] . $inicio, number_format($ingresos, 2, ".", ""));
            $objPHPExcel->getActiveSheet()->setCellValue($arrData[1] . $inicio, number_format($ingresos + $stockInicial, 2, ".", ""));            
            $objPHPExcel->getActiveSheet()->setCellValue($arrData[2] . $inicio, number_format($stockActual, 2, ".", ""));
        }
        $inicio++;
    }
    
    $numColumna = $objPHPExcel->getActiveSheet()->getHighestColumn();
    
    $objPHPExcel->getActiveSheet()
    ->getStyle("F" . $inicioCabecera . ":" . $numColumna . ($inicioCabecera + 1))
    ->applyFromArray(array(            
            "fill" => array(
                    "type" => PHPExcel_Style_Fill::FILL_SOLID,
                    "color" => array(
                            "rgb" => "2e75b6"
                    )
            ),
            "alignment" => array(
                    "horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            ),
            "font" => array(
                    "bold" => true,
                    "size" => 10
            )
    ));
}
$objPHPExcel->setActiveSheetIndex(0);

for ($i = 'A'; $i != $objPHPExcel->getActiveSheet()->getHighestColumn(); $i ++) {
    $objPHPExcel->getActiveSheet()
        ->getColumnDimension($i)
        ->setAutoSize(true);
}

$outuputFileName = "rptStockDetalladoGenerado" . date('YmdHis') . ".xlsx";

// Generate an updated excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $outuputFileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

?>