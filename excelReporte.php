<?php
include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/PedidoData.php";
include "core/app/model/PagoData.php";
include "core/app/model/PerfilData.php";
include "PHPExcel-1.8/Classes/PHPExcel/IOFactory.php";

$inputFileName = "";

$sede = "";
if (isset($_GET["sede"])) {
    $sede = $_GET["sede"];
}

$indicador = "";
$fechaInicio = $fechaFin = "";
$anho = $mesInicio = $mesFin = "";
$anhoInicio = $anhoFin = "";
if (isset($_GET["indicador"])) {
    $indicador = $_GET["indicador"];
    if ($_GET["indicador"] == 0) {
        if (isset($_GET["fechaInicio"])) {
            $fechaInicio = $_GET["fechaInicio"];
        }
        if (isset($_GET["fechaFin"])) {
            $fechaFin = $_GET["fechaFin"];
        }
    } else if ($_GET["indicador"] == 1) {
        if (isset($_GET["anho"])) {
            $anho = $_GET["anho"];
        }
        if (isset($_GET["mesInicio"])) {
            $mesInicio = $_GET["mesInicio"];
        }
        if (isset($_GET["mesFin"])) {
            $mesFin = $_GET["mesFin"];
        }
    } else if ($_GET["indicador"] == 2) {
        if (isset($_GET["anhoInicio"])) {
            $anhoInicio = $_GET["anhoInicio"];
        }
        if (isset($_GET["anhoFin"])) {
            $anhoFin = $_GET["anhoFin"];
        }
    }
}

$opcion = $perfil = "";
if (isset($_GET["opcion"])) {
    $opcion = $_GET["opcion"];
    if ($opcion == 2) {
        $inputFileName = "rptVentas.xlsx";
    } else if ($opcion == 3) {
        $inputFileName = "rptVentasFormaPago.xlsx";
    } else if ($opcion == 4) {
        $inputFileName = "rptVentasPersonal.xlsx";
        $objPerfil = PerfilData::getInfoPerfil(1, 2);
        $perfil = $objPerfil->id;
    } else if ($opcion == 5) {
        $inputFileName = "rptPedidos.xlsx";
    } else if ($opcion == 6) {
        $inputFileName = "rptTipoDescuento.xlsx";
    }
}

$lstVenta = "";
if ($opcion == 2 || $opcion == 3 || $opcion == 4) {
    if ($indicador == 0) {
        $lstVenta = PagoData::getDataReporteGroupByFecha($sede, $fechaInicio, $fechaFin, $perfil);
    } else if ($indicador == 1) {
        $lstVenta = PagoData::getDataReporteGroupByMes($sede, $anho, $mesInicio, $mesFin, $perfil);
    } else if ($indicador == 2) {
        $lstVenta = PagoData::getDataReporteGroupByAnho($sede, $anhoInicio, $anhoFin, $perfil);
    }
} else if ($opcion == 5) {
    if ($indicador == 0) {
        $lstVenta = PedidoData::getDataReporteGroupByFecha($sede, $fechaInicio, $fechaFin, $perfil);
    } else if ($indicador == 1) {
        $lstVenta = PedidoData::getDataReporteGroupByMes($sede, $anho, $mesInicio, $mesFin, $perfil);
    } else if ($indicador == 2) {
        $lstVenta = PedidoData::getDataReporteGroupByAnho($sede, $anhoInicio, $anhoFin, $perfil);
    }
} else if ($opcion == 6) {
    if ($indicador == 0) {
        $lstVenta = PagoData::getDataReporteTipoDescuentoGroupByFecha($sede, $fechaInicio, $fechaFin);
    } else if ($indicador == 1) {
        $lstVenta = PagoData::getDataReporteTipoDescuentoGroupByMes($sede, $anho, $mesInicio, $mesFin);
    } else if ($indicador == 2) {
        $lstVenta = PagoData::getDataReporteTipoDescuentoGroupByAnho($sede, $anhoInicio, $anhoFin);
    }
}

// Read the existing excel file
$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$inicio = 2;
if (count($lstVenta) > 0) {
    if ($opcion == 2) {
        foreach ($lstVenta as $objVenta) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A' . $inicio, date("d/m/Y", strtotime($objVenta->fecha)))
                ->setCellValue('B' . $inicio, $objVenta->sede)
                ->setCellValue('C' . $inicio, $objVenta->tipo)
                ->setCellValue('D' . $inicio, str_pad($objVenta->codigo, 8, "0", STR_PAD_LEFT))
                ->setCellValue('E' . $inicio, $objVenta->producto)
                ->setCellValue('F' . $inicio, number_format($objVenta->total, 2, ".", ""))
                ->setCellValue('G' . $inicio, number_format($objVenta->descuento, 2, ".", ""))
                ->setCellValue('H' . $inicio, number_format(($objVenta->total * (1 - $objVenta->descuento)), 2, ".", ""));

            $inicio ++;
        }
    }
    if ($opcion == 3) {
        foreach ($lstVenta as $objVenta) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A' . $inicio, date("d/m/Y", strtotime($objVenta->fecha)))
                ->setCellValue('B' . $inicio, $objVenta->sede)
                ->setCellValue('C' . $inicio, $objVenta->tipo)
                ->setCellValue('D' . $inicio, $objVenta->forma_pago)
                ->setCellValue('E' . $inicio, $objVenta->tipo_tarjeta)
                ->setCellValue('F' . $inicio, str_pad($objVenta->codigo, 8, "0", STR_PAD_LEFT))
                ->setCellValue('G' . $inicio, $objVenta->producto)
                ->setCellValue('H' . $inicio, number_format($objVenta->total, 2, ".", ""))
                ->setCellValue('I' . $inicio, number_format($objVenta->descuento, 2, ".", ""))
                ->setCellValue('J' . $inicio, number_format(($objVenta->total * (1 - $objVenta->descuento)), 2, ".", ""));

            $inicio ++;
        }
    }
    if ($opcion == 4) {
        foreach ($lstVenta as $objVenta) {
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A' . $inicio, date("d/m/Y", strtotime($objVenta->fecha)))
            ->setCellValue('B' . $inicio, $objVenta->sede)
            ->setCellValue('C' . $inicio, $objVenta->tipo)
            ->setCellValue('D' . $inicio, $objVenta->usuario)
            ->setCellValue('E' . $inicio, str_pad($objVenta->codigo, 8, "0", STR_PAD_LEFT))
            ->setCellValue('F' . $inicio, $objVenta->producto)
            ->setCellValue('G' . $inicio, number_format($objVenta->total, 2, ".", ""))
            ->setCellValue('H' . $inicio, number_format($objVenta->descuento, 2, ".", ""))
            ->setCellValue('I' . $inicio, number_format(($objVenta->total * (1 - $objVenta->descuento)), 2, ".", ""));

            $inicio ++;
        }
    }
    if ($opcion == 5) {
        foreach ($lstVenta as $objVenta) {
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A' . $inicio, date("d/m/Y", strtotime($objVenta->fecha)))
            ->setCellValue('B' . $inicio, $objVenta->sede)
            ->setCellValue('C' . $inicio, $objVenta->tipo)
            ->setCellValue('D' . $inicio, str_pad($objVenta->codigo, 8, "0", STR_PAD_LEFT))
            ->setCellValue('E' . $inicio, $objVenta->categoria)
            ->setCellValue('F' . $inicio, $objVenta->producto)
            ->setCellValue('G' . $inicio, number_format($objVenta->cantidad, 2, ".", ""))
            ->setCellValue('H' . $inicio, number_format($objVenta->total, 2, ".", ""));
            
            $inicio ++;
        }
    }
    if ($opcion == 6) {
        foreach ($lstVenta as $objVenta) {
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A' . $inicio, date("d/m/Y", strtotime($objVenta->fecha)))
            ->setCellValue('B' . $inicio, $objVenta->sede)
            ->setCellValue('C' . $inicio, $objVenta->tipo)
            ->setCellValue('D' . $inicio, $objVenta->tipo_pago)
            ->setCellValue('E' . $inicio, $objVenta->usuario_pedido)
            ->setCellValue('F' . $inicio, $objVenta->usuario_caja)
            ->setCellValue('G' . $inicio, str_pad($objVenta->codigo, 8, "0", STR_PAD_LEFT))
            ->setCellValue('H' . $inicio, number_format($objVenta->dscto_programado, 2, ".", ""))
            ->setCellValue('I' . $inicio, number_format($objVenta->dscto_pedido, 2, ".", ""))
            ->setCellValue('J' . $inicio, number_format($objVenta->total_efectivo, 2, ".", ""))
            ->setCellValue('K' . $inicio, number_format($objVenta->total_tarjeta, 2, ".", ""))
            ->setCellValue('L' . $inicio, number_format($objVenta->total, 2, ".", ""));
            
            $inicio ++;
        }
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