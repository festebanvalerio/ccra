<?php

include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/EmpresaData.php";
include "core/app/model/HistorialPagoData.php";
include "core/app/model/HistorialCajaData.php";
include "core/app/model/ParametroData.php";
include "core/app/model/CajaData.php";
include "core/app/model/SedeData.php";
include "html2pdf/_tcpdf_5.9.206/tcpdf.php";

session_start();

$idHistorialCaja = 0;
if (isset($_GET["id"])) {
    $idHistorialCaja = $_GET["id"];
}

$idCaja = 0;
if (isset($_GET["caja"])) {
    $idCaja = $_GET["caja"];    
}

$fecha = "";
if (isset($_GET["fecha"])) {
    $fecha = $_GET["fecha"];
}

$objHistorialCaja = HistorialCajaData::getById($idHistorialCaja);
$codigo = str_pad($objHistorialCaja->id, 8, "0", STR_PAD_LEFT);
$fechaApertura = date("d/m/Y H:i", strtotime($objHistorialCaja->fecha_apertura));
$fechaCierre = date("d/m/Y H:i", strtotime($objHistorialCaja->fecha_cierre));
$montoApertura = $objHistorialCaja->monto_apertura;
$montoCierre = $objHistorialCaja->monto_cierre;
$nomCaja = $objHistorialCaja->getCaja()->nombre;
$sede = $objHistorialCaja->getSede()->nombre;
$empresa = $objHistorialCaja->getSede()->empresa;

$lstFormaPagoEfectivo = ParametroData::getAll(1, "FORMA PAGO", "EFECTIVO");
$lstFormaPagoTarjeta = ParametroData::getAll(1, "TIPO TARJETA");

// Datos Emisor
$objEmpresa = EmpresaData::getById($empresa);
$ruc = $objEmpresa->ruc;
$razonSocial = $objEmpresa->razon_social;
$nomComercial = $objEmpresa->nombre_comercial;
$direccion = $objEmpresa->direccion;

$totalCantCierre = $totalMontoCierre = 0;

class MYPDF extends TCPDF {
    public function Header() {
    }
    
    public function Footer() {
    }
}

$pdf = null;
$pdf = new MYPDF("P","mm", array(45,350));

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("");
$pdf->SetTitle("");
$pdf->SetSubject("");
$pdf->SetKeywords("");

//set margins
$pdf->SetMargins(0,10,0);    
$pdf->AddPage();
$pdf->SetFont('Helvetica','',5);

$html = '
    <!DOCTYPE html>
    <html lang="es">
        <head>
            <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
        </head>
        <body>
        <table>
            <tr>
                <td style="text-align: center; height: 10px;">
                    <strong style="font-size: 18px;">'.$nomComercial.'</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; height: 10px;">
                    <strong style="font-size: 18px;">'.$razonSocial.'</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; height: 10px;">
                    <strong style="font-size: 16px;">'. $direccion.'</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; height: 10px;">
                    <strong style="font-size: 16px;">'. $sede.'</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; height: 10px;">
                    <strong style="font-size: 16px;">RUC '.$ruc.'</strong>
                </td>
            </tr>
        </table>
        <br/>
        <br/>
        <table>
            <tr>
                <th colspan="3">-------------------------------------------------------------------------</th>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center; font-size: 14px;"><b>Cierre Caja '.$codigo.'</b></td>
            </tr>
            <tr>
                <th colspan="3">-------------------------------------------------------------------------</th>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left; font-size: 14px;"><b>APERTURA CAJA : '.$nomCaja.'</b></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: left; font-size: 14px;"><b>'.$codigo.'</b></td>
                <td style="text-align: rigth; font-size: 14px;"><b>'.number_format($montoApertura, 2).'</b></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left; font-size: 14px;"><b>'.$fechaApertura.'</b></td>
            </tr>
            <tr>
                <th colspan="3">-------------------------------------------------------------------------</th>
            </tr>
            <tr>
                <th style="font-size: 14px; width: 20%; text-align:left;"><b>CANT</b></th>
                <th style="font-size: 14px; width: 60%;"><b>DESCRIPCION</b></th>                    
                <th style="font-size: 14px; width: 20%; text-align:rigth;"><b>TOTAL</b></th>
            </tr>
            <tr>
                <th colspan="3">-------------------------------------------------------------------------</th>
            </tr>';                

$totalGeneral = $totalCantidad = $totalDescuento = 0;
foreach ($lstFormaPagoEfectivo as $objFormaPago) {
    $lstHistorialPago = HistorialPagoData::getAllByReporteCierre(1, $idCaja, $fecha, $objHistorialCaja->id, $objFormaPago->id, 1);
    if (count($lstHistorialPago) > 0) {
        $html .= '
            <tr class="row">
                <td colspan="3"><br/></td>
            </tr>
            <tr class="row">
                <td colspan="3" style="text-align: left; font-size: 14px;"><b>'.$objFormaPago->nombre.'</b></td>
            </tr>';                    
        
        $categoria = "";
        $totalGeneralFormaPago = 0;
        foreach ($lstHistorialPago as $objHistorialPago) {        
            if ($categoria == "") {
                $categoria = $objHistorialPago->categoria;            
                
                $html .= '
            <tr class="row">
                <td colspan="3" style="text-align: left; font-size: 14px;"><b>'.$categoria.'</b></td>
            </tr>
            <tr class="row">
                <td style="text-align: left; font-size: 14px;">'.number_format($objHistorialPago->cantidad, 0).'</td>
                <td style="text-align: left; font-size: 14px;">'.$objHistorialPago->producto.'</td>
                <td style="text-align: right; font-size: 14px;">'.number_format($objHistorialPago->total, 2).'</td>
            </tr>';
            } else if ($categoria != $objHistorialPago->categoria) {
                $categoria = $objHistorialPago->categoria;
                
                $html .= '
            <tr class="row">
                <td colspan="3"><br/></td>
            </tr>
            <tr class="row">
                <td colspan="3" style="text-align: left; font-size: 14px;"><b>'.$categoria.'</b></td>
            </tr>
            <tr class="row">
                <td style="text-align: left; font-size: 14px;">'.number_format($objHistorialPago->cantidad, 0).'</td>
                <td style="text-align: left; font-size: 14px;">'.$objHistorialPago->producto.'</td>
                <td style="text-align: right; font-size: 14px;">'.number_format($objHistorialPago->total, 2).'</td>
            </tr>';                    
            } else if ($categoria == $objHistorialPago->categoria) {
                $html .= '
            <tr class="row">
                <td style="text-align: left; font-size: 14px;">'.number_format($objHistorialPago->cantidad, 0).'</td>
                <td style="text-align: left; font-size: 14px;">'.$objHistorialPago->producto.'</td>
                <td style="text-align: right; font-size: 14px;">'.number_format($objHistorialPago->total, 2).'</td>
            </tr>';
            }
            $totalGeneralFormaPago += $objHistorialPago->total;
            $totalGeneral += $objHistorialPago->total;
            $totalCantidad += $objHistorialPago->cantidad;
        }
        $objFormaPagoMixtaEfectivo = HistorialPagoData::getTotalXFormaPagoMixta(1, $idCaja, $fecha, $objHistorialCaja->id, 1);
        if ($objFormaPagoMixtaEfectivo) {
            $totalGeneral += $objFormaPagoMixtaEfectivo->total;
        }
        
        $html .= '
            <tr class="row">
                <td colspan="3"><br/></td>
            </tr>
            <tr class="row">
                <td style="text-align: left; font-size: 14px;"></td>
                <td style="text-align: left; font-size: 14px;"><b>TOTAL EFECTIVO - MIXTA</b></td>
                <td style="text-align: right; font-size: 14px;"><b>'.number_format($objFormaPagoMixtaEfectivo->total, 2).'</b></td>
            </tr>
            <tr class="row">
                <td style="text-align: left; font-size: 14px;"></td>
                <td style="text-align: left; font-size: 14px;"><b>TOTAL '.$objFormaPago->nombre.'</b></td>
                <td style="text-align: right; font-size: 14px;"><b>'.number_format($totalGeneralFormaPago, 2).'</b></td>
            </tr>';

        $lstDetalle = HistorialPagoData::getAllDescuentoXCierre($fecha, $idCaja, $objHistorialCaja->id, $objFormaPago->id, 1);
        foreach ($lstDetalle as $objDetalle) {
            $totalDescuento += $objDetalle->descuento;
        }
        
        $html .= '
            <tr class="row">
                <td style="text-align: left; font-size: 14px;"></td>
                <td style="text-align: left; font-size: 14px;"><b>TOTAL DESCUENTO</b></td>
                <td style="text-align: right; font-size: 14px;"><b>'.number_format($totalDescuento, 2).'</b></td>
            </tr>                    
            <tr>
                <th colspan="3">-------------------------------------------------------------------------</th>
            </tr>';
    }
}

$html .= '
            <tr>
                <td style="text-align: left; font-size: 14px;"><b>'.number_format($totalCantidad, 2).'</b></td>
                <td style="text-align: left; font-size: 14px;"></td>
                <td style="text-align: right; font-size: 14px;"><b>'.number_format($totalGeneral - $totalDescuento, 2).'</b></td>
            </tr>
            <tr>
                <th colspan="3">-------------------------------------------------------------------------</th>
            </tr>';

$totalCantCierre += $totalCantidad;
$totalMontoCierre += ($totalGeneral - $totalDescuento);

foreach ($lstFormaPagoTarjeta as $objTipoTarjeta) {
    $lstHistorialPago = HistorialPagoData::getAllByReporteCierre(1, $idCaja, $fecha, $objHistorialCaja->id, $objTipoTarjeta->id, 2);

    $totalGeneral = $totalCantidad = $totalDescuento = 0;

    $html .= '
        <tr class="row">
            <td colspan="3"><br/></td>
        </tr>
        <tr class="row">
            <td colspan="3" style="text-align: left; font-size: 13px;"><b>'.$objTipoTarjeta->nombre.'</b></td>
        </tr>';                   
    
    $categoria = "";
    $totalGeneralFormaPago = 0;
    foreach ($lstHistorialPago as $objHistorialPago) {
        if ($categoria == "") {
            $categoria = $objHistorialPago->categoria;
            
            $html .= '
        <tr class="row">
            <td colspan="3" style="text-align: left; font-size: 13px;"><b>'.$categoria.'</b></td>
        </tr>
        <tr class="row">
            <td style="text-align: left; font-size: 14px;">'.number_format($objHistorialPago->cantidad, 0).'</td>
            <td style="text-align: left; font-size: 14px;">'.$objHistorialPago->producto.'</td>
            <td style="text-align: right; font-size: 14px;">'.number_format($objHistorialPago->total, 2).'</td>
        </tr>';
        } else if ($categoria != $objHistorialPago->categoria) {
            $categoria = $objHistorialPago->categoria;
            
            $html .= '
        <tr class="row">
            <td colspan="3"><br/></td>
        </tr>
        <tr class="row">
            <td colspan="3" style="text-align: left; font-size: 14px;"><b>'.$categoria.'</b></td>
        </tr>
        <tr class="row">
            <td style="text-align: left; font-size: 14px;">'.number_format($objHistorialPago->cantidad, 0).'</td>
            <td style="text-align: left; font-size: 14px;">'.$objHistorialPago->producto.'</td>
            <td style="text-align: right; font-size: 14px;">'.number_format($objHistorialPago->total, 2).'</td>
        </tr>';
        } else if ($categoria == $objHistorialPago->categoria) {
            $html .= '
        <tr class="row">
            <td style="text-align: left; font-size: 14px;">'.number_format($objHistorialPago->cantidad, 0).'</td>
            <td style="text-align: left; font-size: 14px;">'.$objHistorialPago->producto.'</td>
            <td style="text-align: right; font-size: 14px;">'.number_format($objHistorialPago->total, 2).'</td>
        </tr>';
        }
        $totalGeneralFormaPago += $objHistorialPago->total;
        $totalGeneral += $objHistorialPago->total;
        $totalCantidad += $objHistorialPago->cantidad;
    }
    $objFormaPagoMixtaTarjeta = HistorialPagoData::getTotalXFormaPagoMixta(1, $idCaja, $fecha, $objHistorialCaja->id, 2, $objTipoTarjeta->id);
    if ($objFormaPagoMixtaTarjeta) {
        $totalGeneral += $objFormaPagoMixtaTarjeta->total;
    }
    
    $html .= '
        <tr class="row">
            <td colspan="3"><br/></td>
        </tr>
        <tr class="row">
            <td style="text-align: left; font-size: 14px;"></td>
            <td style="text-align: left; font-size: 14px;"><b>TOTAL '.$objTipoTarjeta->nombre.' MIXTA</b></td>
            <td style="text-align: right; font-size: 14px;"><b>'.number_format($objFormaPagoMixtaTarjeta->total, 2).'</b></td>
        </tr>
        <tr class="row">
            <td style="text-align: left; font-size: 14px;"></td>
            <td style="text-align: left; font-size: 14px;"><b>TOTAL '.$objTipoTarjeta->nombre.'</b></td>
            <td style="text-align: right; font-size: 14px;"><b>'.number_format($totalGeneralFormaPago, 2).'</b></td>
        </tr>';
    
    $lstDetalle = HistorialPagoData::getAllDescuentoXCierre($fecha, $idCaja, $objHistorialCaja->id, $objTipoTarjeta->id, 2);
    foreach ($lstDetalle as $objDetalle) {
        $totalDescuento += $objDetalle->descuento;
    }
    
    $html .= '
        <tr class="row">
            <td style="text-align: left; font-size: 14px;"></td>
            <td style="text-align: left; font-size: 14px;"><b>TOTAL DESCUENTO</b></td>
            <td style="text-align: right; font-size: 14px;"><b>'.number_format($totalDescuento, 2).'</b></td>
        </tr>
        <tr>
            <th colspan="3">-------------------------------------------------------------------------</th>
        </tr>
        <tr>
            <td style="text-align: left; font-size: 14px;"><b>'.number_format($totalCantidad, 2).'</b></td>
            <td style="text-align: left; font-size: 14px;"></td>
            <td style="text-align: right; font-size: 14px;"><b>'.number_format($totalGeneral - $totalDescuento, 2).'</b></td>
        </tr>
        <tr>
            <th colspan="3">-------------------------------------------------------------------------</th>
        </tr>';

    $totalCantCierre += $totalCantidad;
    $totalMontoCierre += ($totalGeneral - $totalDescuento);
}

$html .= '
            <tr class="row">
                <td colspan="3"><br></td>
            </tr>
            <tr class="row">
                <td colspan="3" style="text-align: left; font-size: 14px;"><b>TOTAL CIERRE</b></td>
            </tr>
            <tr>
                <th colspan="3">-------------------------------------------------------------------------</th>
            </tr>
            <tr>
                <td style="text-align: left; font-size: 14px;"><b>'.number_format($totalCantCierre, 2).'</b></td>
                <td style="text-align: left; font-size: 14px;"></td>
                <td style="text-align: right; font-size: 14px;"><b>'.number_format($totalMontoCierre, 2).'</b></td>
            </tr>
            <tr>
                <th colspan="3">-------------------------------------------------------------------------</th>
            </tr>   
            <tr>
                <td colspan="3" style="text-align: left; font-size: 14px;"><b>CIERRE CAJA : '.$nomCaja.'</b></td>                    
            </tr>
            <tr>
                <td colspan="2" style="text-align: left; font-size: 14px;"><b>'.$codigo.'</b></td>
                <td style="text-align: rigth; font-size: 14px;"><b>'.number_format($montoCierre, 2).'</b></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left; font-size: 14px;"><b>'.$fechaCierre.'</b></td>
            </tr>
            <tr>
                <th colspan="3">-------------------------------------------------------------------------</th>
            </tr>
        </table>
    </body>
</html>';

$pdf->writeHTML($html, true, 0, true, true);
$nomArchivo = "cierreCaja".date('YmdHis').".pdf";
$pdf->Output($nomArchivo, 'I');