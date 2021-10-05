<?php
    if (count($_POST) > 0) {
        $fecha = $_POST["fecha"];
    } else {
        if (isset($_SESSION["com_baja_fecha"])) {
            $fecha = $_SESSION["com_baja_fecha"];
        }
    }
    if ($_SESSION["factel"] == 1) {
        $ruta = $_SERVER["DOCUMENT_ROOT"] . "/";
        require_once ($ruta . "cpeconfig/funciones.php");
    }
    $lstComprobante = ComprobanteData::getAllBill($fecha);
    $lstResumen = ComBajaData::getAll($fecha);
    
    $codigo = "";
    $fechaFactura = date("d/m/Y");
    $objResumenBaja = ComBajaData::getLastId($fechaFactura);
    if ($objResumenBaja) {
        $arrFecha = explode("/", $fechaFactura);
        $fechaBoleta = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
    
        $numero = $objResumenBaja->ultimo_numero + 1;
        $codigo = "RA-" . str_replace("-", "", $fechaBoleta) . "-" . str_pad($numero, 3, "0", STR_PAD_LEFT);
    } else {
        $arrFecha = explode("/", $fechaFactura);
        $fechaBoleta = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
    
        $numero = 1;
        $codigo = "RA-" . str_replace("-", "", $fechaBoleta) . "-" . str_pad($numero, 3, "0", STR_PAD_LEFT);
    }
					<div class="table-responsive">
        						<?php
                                        $indicadorDetalle = $estadoSunat = $fechaSunat ="";
                                ?>
        							<tr>
        						<?php
        			             ?>
        					</table>