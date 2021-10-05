<?php
include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/MovimientoData.php";
include "core/app/model/SedeData.php";
include "core/app/model/AlmacenData.php";
include "core/app/model/InsumoData.php";
include "core/app/model/UnidadData.php";
include "core/app/model/InsumoAlmacenData.php";
include "PHPExcel-1.8/Classes/PHPExcel/IOFactory.php";

$inputFileName = 'rptMovimientoStock.xlsx';

$sede = "";
if (isset($_GET["sede"])) {
    $sede = $_GET["sede"];
}

$insumo = "";
if (isset($_GET["insumo"])) {
    $insumo = $_GET["insumo"];
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

$objSede = SedeData::getById($sede);
$nomSede = $objSede->nombre;

$objInsumo = InsumoData::getById($insumo);
$nomInsumo = $objInsumo->nombre;
$nomUnidad = $objInsumo->getUnidad()->abreviatura;

$objAlmacen = AlmacenData::getById($almacen);
$nomAlmacen = $objAlmacen->nombre;

$lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $insumo, $almacen);
$objInsumoAlmacen = $lstInsumoAlmacen[0];
$stockActual = number_format($objInsumoAlmacen->stock, 2);

$lstMovimiento = MovimientoData::getAll(1, $sede, $insumo, "", $fechaInicio, $fechaFin);

// Read the existing excel file
$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$inicio = 8;
if (count($lstMovimiento) > 0) {
    $objPHPExcel->getActiveSheet()->setCellValue('B1', $nomSede);
    $objPHPExcel->getActiveSheet()->setCellValue('B2', $nomAlmacen);
    $objPHPExcel->getActiveSheet()->setCellValue('B3', $nomInsumo);
    $objPHPExcel->getActiveSheet()->setCellValue('B4', $nomUnidad);
    $objPHPExcel->getActiveSheet()->setCellValue('B5', $stockActual);
    
    foreach ($lstMovimiento as $objMovimiento) {
        $cantidadIngreso = $cantidadSalida = 0.00;

        // Salida
        if ($objMovimiento->tipo == 0) {
            $cantidadSalida = $objMovimiento->cantidad;
        } else { // Ingreso
            $cantidadIngreso = $objMovimiento->cantidad;
        }
        
        $objPHPExcel->getActiveSheet()            
            ->setCellValue('A' . $inicio, date("d/m/Y H:i:s", strtotime($objMovimiento->fecha)))
            ->setCellValue('B' . $inicio, $objMovimiento->detalle)
            ->setCellValue('C' . $inicio, number_format($cantidadIngreso, 2, ".", ""))
            ->setCellValue('D' . $inicio, number_format($cantidadSalida, 2, ".", ""));
        
        $inicio ++;
    }
}
$objPHPExcel->setActiveSheetIndex(0);

for ($i = 'A'; $i !=  $objPHPExcel->getActiveSheet()->getHighestColumn(); $i++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
}

$outuputFileName = "rptMovimientoStock".date('YmdHis').".xlsx";

// Generate an updated excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $outuputFileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

?>