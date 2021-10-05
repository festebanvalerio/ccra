<?php

include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/ParametroData.php";
include "core/app/model/PedidoData.php";
include "core/app/model/MesaData.php";
include "core/app/model/UsuarioData.php";
include "core/app/model/EmpresaData.php";
include "core/app/model/SedeData.php";
include "core/app/model/DetallePedidoData.php";
include "html2pdf/_tcpdf_5.9.206/tcpdf.php";
include "formatos/numletras.php";
include "cpeconfig/datos.php";

$objPedido = PedidoData::getById($_GET["id"]);
$codPedido = str_pad($objPedido->id, 8, "0", STR_PAD_LEFT);

$mesa = "";
if ($objPedido->getMesa()) {
    $mesa = $objPedido->getMesa()->nombre;
}

$pedidoEnLocal = true;
if ($objPedido->getTipo()->valor1 == 0) {
    $pedidoEnLocal = false;
}

$usuario = $objPedido->getUsuario()->nombres." ".$objPedido->getUsuario()->apellidos;
$sede = $objPedido->getSede()->nombre;
$direccionSede = $objPedido->getSede()->direccion;
$tipoPedido = $objPedido->getTipo()->nombre;
$fecha = date("d-m-Y H:i:s", strtotime($objPedido->fecha));
$objEmpresa = $objPedido->getSede()->getEmpresa();

$lstDetallePedido = DetallePedidoData::getProductosXPedido($_GET["id"]);

// Datos Emisor
$objEmpresa = EmpresaData::getById($objEmpresa->id);
$razonSocial = $objEmpresa->razon_social;
$nomComercial = $objEmpresa->nombre_comercial;
$direccion = $objEmpresa->direccion;

$mon = "S/";

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
                    <td style="text-align: center;">
                        <strong style="font-size: 18px;">'.$nomComercial.'</strong>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <strong style="font-size: 18px;">'.$razonSocial.'</strong>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <strong style="font-size: 16px;">'. $direccion.'</strong>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; height: 1px;"></td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <strong style="font-size: 16px;">LOCAL: '. $sede.'</strong>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <strong style="font-size: 16px;">'. $direccionSede.'</strong>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; height: 1px;"></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 40%;"><b>FECHA EMISION:</b></td>
                    <td style="width: 60%;"><b>'.$fecha.'</b></td>
                </tr>
                <tr>
                    <td style="width: 40%;"><b>PEDIDO:</b></td>
                    <td style="width: 60%;"><b>'.$codPedido.'</b></td>
                </tr>
                <tr>
                    <td style="width: 40%;"><b>TIPO:</b></td>
                    <td style="width: 60%;"><b>'.$tipoPedido.'</b></td>
                </tr>';

if ($pedidoEnLocal) {
    $html .= '
                <tr>
                    <td style="width: 40%;"><b>MESA:</b></td>
                    <td style="width: 60%;"><b>'.$mesa.'</b></td>
                </tr>
                <tr>
                    <td style="width: 40%;"><b>MESERO(A):</b></td>
                    <td style="width: 60%;"><b>'.$usuario.'</b></td>
                </tr>';
}
    
$html .= '  </table>
            <br/>
            <br/>
            <table>
                <tr>
                    <th colspan="2">-------------------------------------------------------------------------</th>
                </tr>
                <tr>
                    <th style="font-size: 14px; width: 20%; text-align: left;"><b>CANT</b></th>
                    <th style="font-size: 14px; width: 60%;"><b>DESCRIPCION</b></th>
                    <th style="font-size: 14px; width: 20%; text-align: rigth;"><b>TOTAL</b></th>
                </tr>
                <tr>
                    <th colspan="3">-------------------------------------------------------------------------</th>
                </tr>';

$total = 0;
foreach ($lstDetallePedido as $objDetallePedido) {
    $total += $objDetallePedido->precio_venta*$objDetallePedido->cantidad;
    $html .= '
                <tr class="row">
                    <td style="text-align: left; font-size: 14px;"><b>'.number_format($objDetallePedido->cantidad, 2).'</b></td>
                    <td style="text-align: left; font-size: 14px;"><b>'.$objDetallePedido->nom_producto.'</b></td>
                    <td style="text-align: rigth; font-size: 14px;"><b>'.number_format($objDetallePedido->precio_venta*$objDetallePedido->cantidad,2).'</b></td>
                </tr>';
}

$html.='
                <tr>
                    <th colspan="3">-------------------------------------------------------------------------</th>
                </tr>
            </table>
            <br/>
            <table>
                <tr>
                    <td style="width: 40%;"></td>
                    <td style="width: 40%;"><b>TOTAL : </b></td>
                    <td style="text-align: rigth; width: 20%;"><b>'.$mon.' '.number_format($total,2).'</b></td>
                </tr>
            </table>
        </body>
    </html>';

$pdf->writeHTML($html, true, 0, true, true);
$nomArchivo = "comanda".date('YmdHis').".pdf";
$pdf->Output($nomArchivo, 'I');