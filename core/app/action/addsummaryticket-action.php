<?php

$mensaje = "";
if (count($_POST) > 0) {
    if ($_POST["accion"] == 1) {
        $fecha = $_POST["fecha"];
        $fechaActual = date("d/m/Y");
        $_SESSION["resumen_boleta_fecha"] = $_POST["fecha"];
        
        $contador = 0;
        $lstDocumento = ComprobanteData::getAllSummaryByFecha($fecha);
        if (count($lstDocumento) > 0) {
            foreach ($lstDocumento as $objDocumento) {
                $lstResumenBoletaDetalle = ResumenBoletaDetalleData::getDetalle($objDocumento->fe_comprobante_id);
                if (count($lstResumenBoletaDetalle) == 0) {
                    $contador++;
                } else if (count($lstResumenBoletaDetalle) == 1) {
                    if ($objDocumento->fe_comprobante_est == 2) {
                        $contador++;
                    }
                }        
            }
        }
        
        if ($contador > 0) {
            $fechaBoleta = $numero = $codigo = "";
            $objResumenBoleta = ResumenBoletaData::getLastId($fechaActual);
            if ($objResumenBoleta) {
                $arrFecha = explode("/", $fechaActual);
                $fechaBoleta = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
                
                $numero = $objResumenBoleta->ultimo_numero + 1;
                $codigo = "RC-".str_replace("-", "", $fechaBoleta)."-".str_pad($numero, 3, "0", STR_PAD_LEFT);
            } else {
                $arrFecha = explode("/", $fechaActual);
                $fechaBoleta = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
                
                $numero = 1;
                $codigo = "RC-".str_replace("-", "", $fechaBoleta)."-".str_pad($numero, 3, "0", STR_PAD_LEFT);
            }
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
            
            $objResumenBoleta = new ResumenBoletaData();
            $objResumenBoleta->fe_resumenboleta_reg = date("Y-m-d H:i:s");
            $objResumenBoleta->fe_resumenboleta_usureg = $_SESSION["user"];
            $objResumenBoleta->fe_resumenboleta_fec = $fechaBoleta;
            $objResumenBoleta->fe_resumenboleta_fecref = $fecha;
            $objResumenBoleta->fe_resumenboleta_cod = $codigo;
            $objResumenBoleta->fe_resumenboleta_num = $numero;
            $resultado = $objResumenBoleta->add();
            if (isset($resultado[1]) && $resultado[1] > 0) {
                $idResumenBoleta = $resultado[1];
                
                $item = 0;                
                foreach ($lstDocumento as $objDocumento) {
                    $estado = "";
                    $insertar = 0;
                    
                    $lstResumenBoletaDetalle = ResumenBoletaDetalleData::getDetalle($objDocumento->fe_comprobante_id);
                    if (count($lstResumenBoletaDetalle) == 0) {
                        $item++;
                        $estado = 1;
                        $insertar = 1;
                    }
                    if (count($lstResumenBoletaDetalle) == 1) {
                        if ($objDocumento->fe_comprobante_est == 2) {
                            $item++;
                            $estado = 3;
                            $insertar = 1;
                        }
                    }
                    
                    if ($insertar == 1) {
                        List($docrelser, $docrelcor) = explode("-", $objDocumento->tb_notacredeb_numdoc);
                        
                        $objResumenBoletaDetalle = new ResumenBoletaDetalleData();
                        $objResumenBoletaDetalle->fe_resumenboleta_id = $idResumenBoleta;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_num = $item;
                        $objResumenBoletaDetalle->cs_tipodocumento_cod = $objDocumento->cs_tipodocumento_cod;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_ser = $objDocumento->fe_comprobante_ser;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_cor = $objDocumento->fe_comprobante_cor;
                        $objResumenBoletaDetalle->cs_tipodocumentoidentidad_cod = $objDocumento->cs_tipodocumentoidentidad_cod;
                        $objResumenBoletaDetalle->tb_cliente_numdoc = $objDocumento->tb_cliente_numdoc;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_tipdocrel = $objDocumento->tb_notacredeb_tipdoc;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_docrelser = $docrelser;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_docrelcor = $docrelcor;
                        $objResumenBoletaDetalle->cs_tipomoneda_cod = $objDocumento->cs_tipomoneda_cod;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_totvengra = $objDocumento->fe_comprobante_totvengra;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_totvenina = $objDocumento->fe_comprobante_totvenina;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_totvenexo = $objDocumento->fe_comprobante_totvenexo;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_totvengratui = $objDocumento->fe_comprobante_totvengratui;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_sumotrcar = $objDocumento->fe_comprobante_sumotrcar;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_sumisc = $objDocumento->fe_comprobante_sumisc;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_sumigv = $objDocumento->fe_comprobante_sumigv;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_sumotrtri = $objDocumento->fe_comprobante_sumotrtri;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_imptot = $objDocumento->fe_comprobante_imptot;
                        $objResumenBoletaDetalle->fe_comprobante_id = $objDocumento->fe_comprobante_id;
                        $objResumenBoletaDetalle->fe_resumenboletadetalle_est = $estado;
                        $objResumenBoletaDetalle->add();
                    }
                    //solo envia 500 por lote
                    if ($item == 500) {
                        break;
                    }
                }
                
                /*$ruta = "/var/www/html/huroelsa/";
                
                require_once($ruta."resumen.php");*/
                
                $objLog = LogData::getById($idResumenBoleta);
                if (!$objLog) {
                    $mensaje = "Registrado " . $codigo . " Correctamente. Nro Filas " . $item;
                } else {
                    $mensaje = "Ocurrio un error en el envio a la SUNAT " . $objLog->mensaje;
                }
            }  else {
                $mensaje = "Ocurrio un error inesperado, comunicarse con el administrador";
            }
        } else {
            $mensaje = "No existe Comprobantes para declarar";
        }
    }
    if ($_POST["accion"] == 2) {
        $objResumenBoleta = ResumenBoletaData::getById($_POST["id"]);
        $objResumenBoleta->fe_resumenboleta_est = 2;
        $resultado = $objResumenBoleta->delete();
        if (isset($resultado[0]) && $resultado[0] == 1) {
            $mensaje = "Resumen ".$objResumenBoleta->fe_resumenboleta_cod." eliminado";
        }       
    }
    if ($_POST["accion"] == 3) {
        $idResumenBoleta = $_POST["id"];
        $codigo = $_POST["codigo"];
        $item = $_POST["item"];
        
        $ruta = "/var/www/html/huroelsa/";
        
        require_once($ruta."resumen.php");
        
        $objLog = LogData::getById($idResumenBoleta);
        if (!$objLog) {
            $mensaje = "Registrado " . $codigo . " Correctamente. Nro Filas " . $item;
        } else {
            $mensaje = "Ocurrio un error en el envio a la SUNAT " . $objLog->mensaje;
        }
    }
}
echo $mensaje;