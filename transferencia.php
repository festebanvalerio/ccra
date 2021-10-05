<?php

include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

include "core/app/model/TransferenciaData.php";
include "core/app/model/SedeData.php";
include "core/app/model/AlmacenData.php";
include "core/app/model/DetalleTransferenciaData.php";
include "core/app/model/UsuarioData.php";
include "core/app/model/InsumoData.php";
include "core/app/model/UnidadData.php";
include "html2pdf/_tcpdf_5.9.206/tcpdf.php";

$idTransferencia = 0;
if (isset($_GET["id"])) {
    $idTransferencia = $_GET["id"];
}

$objTransferencia = TransferenciaData::getById($idTransferencia);
$codigo = str_pad($objTransferencia->id, 8, "0", STR_PAD_LEFT);
$fecha = date("d-m-Y H:i:s", strtotime($objTransferencia->fecha));
$sedeOrigen = $objTransferencia->getSedeOrigen()->nombre;
$sedeDestino = $objTransferencia->getSedeDestino()->nombre;
$almacenOrigen = $objTransferencia->getAlmacenOrigen()->nombre;
$almacenDestino = $objTransferencia->getAlmacenDestino()->nombre;
$usuario = $objTransferencia->getUsuario()->nombres." ".$objTransferencia->getUsuario()->apellidos;

$lstDetalleTransferencia = DetalleTransferenciaData::getAllByTransferencia($idTransferencia);

class MYPDF extends TCPDF {
    public function Header() {
    }
    
    public function Footer() {
    }
}

$pdf = null;
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("");
$pdf->SetTitle("");
$pdf->SetSubject("");
$pdf->SetKeywords("");

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(12, 15, 12);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->AddPage('P', 'A4');


$pdf->SetFont('Helvetica', '', 11);

$html = '
        <!DOCTYPE html>
        <html lang="es">
            <head>
                <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
            </head>
            <body>
            <table>
                <tr>
                    <td style="width: 45%;"><b>CODIGO DE TRANSFERENCIA :</b></td>
                    <td style="width: 55%;">'.$codigo.'</td>
                </tr>
                <tr>
                    <td style="width: 45%;"><b>USUARIO DE TRANSFERENCIA :</b></td>
                    <td style="width: 55%;">'.$usuario.'</td>
                </tr>
                <tr>
                    <td style="width: 45%;"><b>FECHA Y HORA DE TRANSFERENCIA :</b></td>
                    <td style="width: 55%;">'.$fecha.'</td>
                </tr>
                <tr>
                    <td style="width: 45%;"><b>ALMACEN ORIGEN :</b></td>
                    <td style="width: 55%;">'.$sedeOrigen." - ".$almacenOrigen.'</td>
                </tr>
                <tr>
                    <td style="width: 45%;"><b>ALMACEN DESTINO :</b></td>
                    <td style="width: 55%;">'.$sedeDestino." - ".$almacenDestino.'</td>
                </tr>
            </table>
            <br/>
            <br/>
            <table>
                <tr>
                    <th colspan="4" style="text-align: left;">LISTADO DE INSUMOS</th>
                </tr>
                <tr>
                    <td><br/></td>
                </tr>
                <tr>
                    <th style="width: 10%;"><b>ITEM</b></th>
                    <th style="width: 50%;"><b>INSUMO</b></th>
                    <th style="width: 20%;"><b>UNIDAD</b></th>
                    <th style="width: 20%; text-align:rigth;"><b>CANTIDAD</b></th>
                </tr>
                <tr>
                    <th colspan="4">----------------------------------------------------------------------------------------------------------------------------------------------</th>
                </tr>';

$item = 1;
foreach ($lstDetalleTransferencia as $objDetalleTransferencia) {
    $html .= '
                <tr class="row">
                    <td style="text-align: left;">'.$item++.'</td>
                    <td style="text-align: left;">'.$objDetalleTransferencia->getInsumo()->nombre.'</td>
                    <td style="text-align: left;">'.$objDetalleTransferencia->getInsumo()->getUnidad()->abreviatura.'</td>
                    <td style="text-align: right;">'.number_format($objDetalleTransferencia->cantidad, 2).'</td>
                </tr>';
}

$html .= '    
                <tr>
                    <th colspan="4">----------------------------------------------------------------------------------------------------------------------------------------------</th>
                </tr>
            </table>
            <br/>
            <br/>
            <br/>            
            <table>
                <tr>
                    <td style="width: 40%; height: 30px;">DATOS DE RECEPCIÓN :</td>
                    <td style="width: 60%; height: 30px;"></td>
                </tr>
                <tr>
                    <td style="width: 40%; height: 30px;">NOMBRES Y APELLIDOS :</td>
                    <td style="width: 60%; height: 30px;"></td>                    
                </tr>
                <tr>
                    <td style="width: 40%; height: 30px;">DNI :</td>
                    <td style="width: 60%; height: 30px;"></td>
                </tr>
                <tr>
                    <td style="width: 40%; height: 30px;">FECHA :</td>
                    <td style="width: 60%; height: 30px;"></td>
                </tr>
            </table>
            <br/>
            <br/>
            <br/>
            <br/>
            <table>
                <tr>
                    <td style="width: 30%;"></td>
                    <td style="width: 40%;">-----------------------------------------------------</td>
                    <td style="width: 30%;"></td>
                </tr>
                <tr>
                    <td style="width: 30%;"></td>
                    <td style="width: 40%; text-align: center;">FIRMA</td>
                    <td style="width: 30%;"></td>
                </tr>
            </table>            
        </body>
    </html>';

$pdf->writeHTML($html, true, 0, true, true);
$nomArchivo = "transferencia".date('YmdHis').".pdf";
$pdf->Output($nomArchivo, 'I');