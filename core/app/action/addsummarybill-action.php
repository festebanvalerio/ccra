<?php

$mensaje = "";
if (count($_POST) > 0) {
    if ($_POST["accion"] == 1) {
        $fecha = $_POST["fecha"];
        $fechaActual = date("d/m/Y");
        $_SESSION["com_baja_fecha"] = $_POST["fecha"];
        
        $contador = 0;
        $lstDocumento = ComprobanteData::getAllBillByFecha($fecha);
        if (count($lstDocumento) > 0) {
            foreach ($lstDocumento as $objDocumento) {
                $lstResumenComBajaDetalle = ComBajaDetalleData::getDetalle($objDocumento->fe_comprobante_id);
                if (count($lstResumenComBajaDetalle) == 0) {
                    $contador++;
                } else if (count($lstResumenComBajaDetalle) == 1) {
                    if ($objDocumento->fe_comprobante_est == 2) {
                        $contador++;
                    }
                }        
            }
        }
        
        if ($contador > 0) {
            $fechaFactura = $numero = $codigo = "";
            $objComBaja = ComBajaData::getLastId($fechaActual);
            if ($objComBaja) {
                $arrFecha = explode("/", $fechaActual);
                $fechaFactura = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
                
                $numero = $objComBaja->ultimo_numero + 1;
                $codigo = "RA-".str_replace("-", "", $fechaFactura)."-".str_pad($numero, 3, "0", STR_PAD_LEFT);
            } else {
                $arrFecha = explode("/", $fechaActual);
                $fechaFactura = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
                
                $numero = 1;
                $codigo = "RA-".str_replace("-", "", $fechaFactura)."-".str_pad($numero, 3, "0", STR_PAD_LEFT);
            }
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
            
            $objComBaja = new ComBajaData();
            $objComBaja->fe_combaja_reg = date("Y-m-d H:i:s");
            $objComBaja->fe_combaja_usureg = $_SESSION["user"];
            $objComBaja->fe_combaja_fec = $fechaFactura;
            $objComBaja->fe_combaja_fecref = $fecha;
            $objComBaja->fe_combaja_cod = $codigo;
            $objComBaja->fe_combaja_num = $numero;
            $resultado = $objComBaja->add();
            if (isset($resultado[1]) && $resultado[1] > 0) {
                $idComBaja = $resultado[1];
                
                $item = 0;
                foreach ($lstDocumento as $objDocumento) {
                    $insertar = 0;
                    
                    $lstComBajaDetalle = ComBajaDetalleData::getDetalle($objDocumento->fe_comprobante_id);
                    if (count($lstComBajaDetalle) == 0) {
                        $item++;
                        $insertar = 1;
                    }
                    if (count($lstComBajaDetalle) == 1) {
                        if ($objDocumento->fe_comprobante_est == 2) {
                            $item++;
                            $insertar = 1;
                        }
                    }
                    
                    if ($insertar == 1) {
                        $objComBajaDetalle = new ComBajaDetalleData();
                        $objComBajaDetalle->fe_combaja_id = $idComBaja;
                        $objComBajaDetalle->fe_combajadetalle_num = $item;                    
                        $objComBajaDetalle->cs_tipodocumento_cod = $objDocumento->cs_tipodocumento_cod;
                        $objComBajaDetalle->fe_combajadetalle_ser = $objDocumento->fe_comprobante_ser;
                        $objComBajaDetalle->fe_combajadetalle_cor = $objDocumento->fe_comprobante_cor;
                        $objComBajaDetalle->fe_combajadetalle_mot = "CANCELACION";
                        $objComBajaDetalle->fe_comprobante_id = $objDocumento->fe_comprobante_id;                    
                        $objComBajaDetalle->add();
                    }
                    //solo envia 500 por lote
                    if ($item == 500) {
                        break;
                    }
                }
                
                /*$ruta = "/var/www/html/huroelsa/";
                
                require_once($ruta."combaja.php");*/
                
                $objLog = LogData::getById($idComBaja);
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
        $objComBaja = ComBajaData::getById($_POST["id"]);
        $objComBaja->fe_combaja_est = 2;
        $resultado = $objComBaja->delete();
        if (isset($resultado[0]) && $resultado[0] == 1) {
            $mensaje = "Comunicación de Baja ".$objComBaja->fe_combaja_cod." eliminado";
        }
    }
    if ($_POST["accion"] == 3) {
        $idComBaja = $_POST["id"];
        $codigo = $_POST["codigo"];
        $item = $_POST["item"];
        
        $ruta = "/var/www/html/huroelsa/";
        
        require_once($ruta."combaja.php");
        
        $objLog = LogData::getById($idComBaja);
        if (!$objLog) {
            $mensaje = "Registrado " . $codigo . " Correctamente. Nro Filas " . $item;
        } else {
            $mensaje = "Ocurrio un error en el envio a la SUNAT " . $objLog->mensaje;
        }
    }
}
echo $mensaje;