<?php

include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/ClienteData.php";
include "core/app/model/HistorialDocumentoData.php";
include "core/app/model/PagoData.php";
include "core/app/model/ComprobanteData.php";
include "core/app/model/DetalleComprobanteData.php";
include "core/app/model/ParametroData.php";
include "core/app/model/PedidoData.php";
include "core/app/model/MesaData.php";
include "core/app/model/UsuarioData.php";
include "core/app/model/EmpresaData.php";
include "core/app/model/SedeData.php";
include "core/app/model/HistorialPagoData.php";
include "html2pdf/_tcpdf_5.9.206/tcpdf.php";
include "formatos/numletras.php";
include "cpeconfig/datos.php";

$objHistorialDocumento = HistorialDocumentoData::getById($_GET["id"]);
$objPago = $objHistorialDocumento->getHistorialPago()->getPago();
$fecha = date("d-m-Y H:i:s", strtotime($objPago->fecha_creacion));
$cajero = $objPago->getUsuario()->nombres." ".$objPago->getUsuario()->apellidos;
$tarjeta = "";
$formaPago = $objHistorialDocumento->getHistorialPago()->getFormaPago()->nombre;
if ($objHistorialDocumento->getHistorialPago()->getFormaPago()->valor1 == 1 || $objHistorialDocumento->getHistorialPago()->getFormaPago()->valor1 == 2) {
    $tarjeta = $objHistorialDocumento->getHistorialPago()->getTipoTarjeta()->nombre;
}

$objPedido = $objPago->getPedido();
$codPedido = str_pad($objPedido->id, 8, "0", STR_PAD_LEFT);
$descuento = number_format($objPedido->descuento_pedido, 2);
$mesa = "";
if ($objPedido->getMesa()) {
    $mesa = $objPedido->getMesa()->nombre;
}
$usuario = $objPedido->getUsuario()->nombres." ".$objPedido->getUsuario()->apellidos;
$sede = $objPedido->getSede()->nombre;
$direccionSede = $objPedido->getSede()->direccion;
$tipoPedido = $objPedido->getTipo()->nombre;
$pedidoEnLocal = true;
if ($objPedido->getTipo()->valor1 == 0) {
    $pedidoEnLocal = false;
}
$objEmpresa = $objPedido->getSede()->getEmpresa();
$indicadorFactElectronica = $objEmpresa->facturacion_electronica;

// Datos del documento electronico
$objComprobante = $objHistorialDocumento->getComprobante(); 
$serie = $objComprobante->fe_comprobante_ser;
$correlativo = $objComprobante->fe_comprobante_cor;
$numDocumento = $objComprobante->tb_cliente_numdoc;
$cliente = $objComprobante->tb_cliente_nom;
$direccionCliente = $objComprobante->tb_cliente_dir;
$sumigv = $objComprobante->fe_comprobante_sumigv;
$imptot = $objComprobante->fe_comprobante_imptot;
$tipoDocumentoCliente = $objComprobante->cs_tipodocumentoidentidad_cod;

$tipoDocumento = $txtDocumento = "";
$codTipoDocumento = $objComprobante->cs_tipodocumento_cod;
if ($codTipoDocumento == 3) {
    $txtDocumento = "DNI";
    $tipoDocumento = "BOLETA DE VENTA ELECTRONICA";
} else if ($codTipoDocumento == 1) {
    $txtDocumento = "RUC";
    $tipoDocumento = "FACTURA DE VENTA ELECTRONICA";
} else {
    $txtDocumento = "TICKET";
    $tipoDocumento = "TICKET DE VENTA";
}

$lstDetalleComprobante = DetalleComprobanteData::getById($objComprobante->fe_comprobante_id);

$existeServicioMesa = false;
$servicioMesa = 0;
$lstParametro = ParametroData::getAll(1, "OPCIONES GENERALES", "SERVICIO");
if (count($lstParametro) > 0) {
    $servicioMesa = $lstParametro[0]->valor1;
    $existeServicioMesa = true;
}

$exonerado = false;
$lstParametro = ParametroData::getAll(1, "OPCIONES GENERALES", "EXONERADO");
if (count($lstParametro) > 0 && $lstParametro[0]->valor1 == 1) {
    $exonerado = true;    
}

// Datos Emisor
$objEmpresa = EmpresaData::getById($objEmpresa->id);
$ruc = $objEmpresa->ruc;
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
                <tr>
                    <td style="text-align: center;">
                        <strong style="font-size: 16px;">RUC '.$ruc.'</strong>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <strong style="font-size: 16px;"><b>'.$tipoDocumento.'</b></strong>
                    </td>
                </tr>    
                <tr>
                    <td style="text-align: center;">
                        <strong style="font-size: 16px;"><b>'.$serie.'-'.$correlativo.'</b><br></strong>
                    </td>
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
    
if ($numDocumento != "10000000") {
    $html .= '    
                <tr>
                    <td style="width: 40%;"><b>'.$txtDocumento.':</b></td>
                    <td style="width: 60%;"><b>'.$numDocumento.'</b></td>
                </tr>
                <tr>
                    <td style="width: 40%;"><b>CLIENTE :</b></td>
                    <td style="width: 60%;"><b>'.$cliente.'</b></td>
                </tr>
                <tr>
                    <td style="width: 40%;"><b>DIRECCIÓN:</b></td>
                    <td style="width: 60%;"><b>'.($direccionCliente == "" ? "-" : $direccionCliente).'</b></td>
                </tr>';
}
$html .= '
                <tr>
                    <td style="width: 40%;"><b>CAJERO(A):</b></td>
                    <td style="width: 60%;"><b>'.$cajero.'</b></td>
                </tr>
                <tr>
                    <td style="width: 40%;"><b>TIPO:</b></td>
                    <td style="width: 60%;"><b>'.$tipoPedido.'</b></td>
                </tr>
                <tr>
                    <td style="width: 40%;"><b>FORMA PAGO:</b></td>
                    <td style="width: 60%;"><b>'.$formaPago.'</b></td>
                </tr>';

if ($tarjeta != "") {
    $html .= '
                <tr>
                    <td style="width: 40%;"><b>TARJETA:</b></td>
                    <td style="width: 60%;"><b>'.$tarjeta.'</b></td>
                </tr>';
}

// Para llevar
if ($objPedido->getTipo()->valor2 == 2) {
    $datos = $objPedido->datos;
    $hora = $objPedido->hora;
    
    $html .= '
                <tr>
                    <td style="width: 40%;"><b>CONTACTO:</b></td>
                    <td style="width: 60%;"><b>'.($datos == "" ? "-" : $datos).'</b></td>
                </tr>
                <tr>
                    <td style="width: 40%;"><b>HORA:</b></td>
                    <td style="width: 60%;"><b>'.($hora == "" ? "-" : $hora).'</b></td>
                </tr>';
}


$html .= '  </table>
            <br/>
            <br/>
            <table>
                <tr>
                    <th colspan="3">-------------------------------------------------------------------------</th>
                </tr>
                <tr>
                    <th style="font-size: 14px; width: 20%; text-align: left;"><b>CANT</b></th>
                    <th style="font-size: 14px; width: 60%;"><b>DESCRIPCION</b></th>                    
                    <th style="font-size: 14px; width: 20%; text-align: rigth;"><b>TOTAL</b></th>
                </tr>
                <tr>
                    <th colspan="3">-------------------------------------------------------------------------</th>
                </tr>';

foreach ($lstDetalleComprobante as $objDetalleComprobante) {
    $html .= '
                <tr class="row">
                    <td style="text-align: left; font-size: 14px;"><b>'.number_format($objDetalleComprobante->fe_comprobantedetalle_can, 2).'</b></td>
                    <td style="text-align: left; font-size: 14px;"><b>'.$objDetalleComprobante->fe_comprobantedetalle_nom.'</b></td>                    
                    <td style="text-align: right; font-size: 14px;"><b>'.number_format($objDetalleComprobante->fe_comprobantedetalle_can * $objDetalleComprobante->fe_comprobantedetalle_preuni, 2).'</b></td>
                </tr>';
}

$servicio = $subtotal = $igv = $total = 0;
if ($existeServicioMesa) {    
    $total = $objComprobante->fe_comprobante_imptot;    
    $servicio = $total * $servicioMesa;    
    $subtotal = ($total - $servicio) / 1.18;    
    $igv = $subtotal * 0.18;
    
    
    $total = number_format($total, 2);
    $servicio = number_format(round($servicio, 2), 2);
    $subtotal = number_format(round($subtotal, 2), 2);
    $igv = number_format(round($igv, 2), 2);
} else {
    if ($exonerado) {
        $subtotal = number_format($objComprobante->fe_comprobante_totvenexo, 2);
    } else {
        $subtotal = number_format($objComprobante->fe_comprobante_totvengra, 2);
    }
    $igv = number_format($objComprobante->fe_comprobante_sumigv, 2);
    $total = number_format($objComprobante->fe_comprobante_imptot, 2);
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
                    <td style="width: 40%;"><b>DESCUENTO : </b></td>
                    <td style="text-align: rigth; width: 20%;"><b>'.$mon.' '.$descuento.'</b></td>
                </tr>
                <tr>
                    <td style="width: 40%;"></td>
                    <td style="width: 40%;"><b>SUBTOTAL : </b></td>
                    <td style="text-align: rigth; width: 20%;"><b>'.$mon.' '.$subtotal.'</b></td>
                </tr>
                <tr>
                    <td style="width: 40%;"></td>
                    <td style="width: 40%;"><b>IGV (18%): </b></td>
                    <td style="text-align: rigth; width: 20%;"><b>'.$mon.' '.$igv.'</b></td>
                </tr>';
    
if ($existeServicioMesa) {
    $html .= '
                <tr>
                    <td style="width: 40%;"></td>
                    <td style="width: 40%;"><b>SERVICIO (10%): </b></td>
                    <td style="text-align: rigth; width: 20%;"><b>'.$mon.' '.$servicio.'</b></td>
                </tr>';
}
    
$html .= '
                <tr>
                    <td style="width: 40%;"></td>
                    <td style="width: 40%;"><b>TOTAL: </b></td>
                    <td style="text-align: rigth; width: 20%;"><b>'.$mon.' '.$total.'</b></td>
                </tr>
                <tr>
                    <th colspan="3">-------------------------------------------------------------------------</th>
                </tr>
            </table>
            <br/>
            <table>
                <tr>
                    <td colspan="3" style="text-align: left;"><b>SON: ' . numtoletras($total). '</b></td>
                </tr>
            </table>
            <br/>
            <br/>';
            
if ($indicadorFactElectronica == 1) {
    if ($codTipoDocumento == 1 || $codTipoDocumento == 3) {
        $html .='
            <table border="0">
                <tr>
                    <td style="width:68%" align="left"><p><b>Representación impresa de la '.$tipoDocumento.'.</b><br>Autorizado mediante Resolución de Intendencia Nro. 034-005-0005315<br></p></td>
                    <td rowspan="3">';
                        
        $style = array(
            'border' => 2,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        $params = $pdf->serializeTCPDFtagParameters(array($ruc.'|'.$codTipoDocumento.'|'.$serie.'|'.$correlativo.'|'.$sumigv.'|'.$imptot.'|'.date('d-m-Y', strtotime($fecha)).'|'.$tipoDocumentoCliente.'|'.$numDocumento.'|', 'QRCODE,Q', '', '', 10, 10, $style, 'N'));
        $html .= '
                        <tcpdf method="write2DBarcode" params="'.$params.'" />
                    </td>
                </tr>
            </table>';
    }
}

// Delivery
if ($objPedido->getTipo()->valor2 == 1) {
    $datos = $objPedido->datos;
    $direccion = $objPedido->direccion;
    $telefono = $objPedido->telefono;
    $hora = $objPedido->hora;
    
    $html .= '
            <table>
                <tr>
                    <td style="width: 100%;" colspan="2"><b>DATOS DELIVERY</b></td>
                </tr>
                <tr>
                    <td style="width: 100%;" colspan="2"><b>--------------------------</b></td>
                </tr>
                <tr>
                    <td style="width: 100%; line-height: 2px; " colspan="2"></td>
                </tr>
                <tr>
                    <td style="width: 35%;"><b>DATOS:</b></td>
                    <td style="width: 65%;"><b>'.($datos == "" ? "-" : $datos).'</b></td>
                </tr>
                <tr>
                    <td style="width: 35%;"><b>DIRECCIÓN:</b></td>
                    <td style="width: 65%;"><b>'.($direccion == "" ? "-" : $direccion).'</b></td>
                </tr>
                <tr>
                    <td style="width: 35%;"><b>TELÉFONO:</b></td>
                    <td style="width: 65%;"><b>'.($telefono == "" ? "-" : $telefono).'</b></td>
                </tr>
                <tr>
                    <td style="width: 35%;"><b>HORA:</b></td>
                    <td style="width: 65%;"><b>'.($hora == "" ? "-" : $hora).'</b></td>
                </tr>
            </table>';        
}


$html .= '
        </body>
    </html>';

$pdf->writeHTML($html, true, 0, true, true);
$nomArchivo = "documento".date('YmdHis').".pdf";
$pdf->Output($nomArchivo, 'I');