<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {
        mysqli_begin_transaction(Database::getCon());
        if ($_POST["accion"] == 3 || $_POST["accion"] == 4) {
            $idPago = $_POST["idpago"];
            $objPago = PagoData::getById($idPago);
            $montoPagado = $objPago->getPedido()->total - $objPago->monto_descuento;
            
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "OPCION: REGISTRAR COMPROBANTE \n", FILE_APPEND);
            
            $error = 0;
            $indicadorResBol = 2;
            $idCliente = $_POST["cliente"];
            $datosCliente = $dirCliente = "";
            $numDocCliente = trim($_POST["numdoc"]);
            $objTipoDocumento = ParametroData::getById($_POST["tipodoc"]);
            if ($numDocCliente == "") {
                $numDocCliente = "10000000";
                $datosCliente = "CLIENTE GENERAL";
            } else {
                $tipoDocumentoCliente = "";
                if ($objTipoDocumento->valor2 == 3) {
                    // Boleta
                    $datosCliente = trim($_POST["nomape"]);
                    $indicadorResBol = 1;
                    $lstTipoDocumentoCliente = ParametroData::getAll(1, "TIPO DOCUMENTO CLIENTE", "DNI");
                    $tipoDocumentoCliente = $lstTipoDocumentoCliente[0]->id;
                } else if ($objTipoDocumento->valor2 == 1) {
                    // Factura
                    $datosCliente = trim($_POST["razon"]);
                    $dirCliente = trim($_POST["direccion"]);
                    $indicadorResBol = 0;
                    
                    $lstTipoDocumentoCliente = ParametroData::getAll(1, "TIPO DOCUMENTO CLIENTE", "RUC");
                    $tipoDocumentoCliente = $lstTipoDocumentoCliente[0]->id;
                }
                if ($idCliente == 0) {
                    // Registrar Cliente
                    $objCliente = new ClienteData();
                    $objCliente->tipo_documento = $tipoDocumentoCliente;
                    $objCliente->num_documento = $numDocCliente;
                    $objCliente->datos = $datosCliente;
                    $objCliente->direccion = $dirCliente;
                    $objCliente->estado = 1;
                    $resultado = $objCliente->add();
                    if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                        file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla cliente\n", FILE_APPEND);
                        $error = 1;
                    } else {
                        $idCliente = $resultado[1];
                        file_put_contents("info" . date("Ymd") . ".log", "Reistro en tabla cliente : " . $idCliente . "\n", FILE_APPEND);
                    }
                }
            }
            if ($error == 0) {
                // Obtener el correlativo del documento electronico
                $serie = $secuencia = "";
                $objCorrelativo = CorrelativoData::getBySecuencia($_SESSION["sede"], date("Y"), $objTipoDocumento->id);
                if ($objCorrelativo) {
                    $serie = $objTipoDocumento->valor1 . $objCorrelativo->serie;
                    //$secuencia = str_pad($objCorrelativo->secuencia, 8, "0", STR_PAD_LEFT);
                    $secuencia = $objCorrelativo->secuencia;
                    
                    // Actualizar el correlativo
                    $objCorrelativo->secuencia = $objCorrelativo->secuencia + 1;
                    $resultado = $objCorrelativo->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        $objHistorialPago = HistorialPagoData::getByPago($idPago);
                        file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla correlativos : " . $objCorrelativo->id . "\n", FILE_APPEND);
                        
                        $objComprobante = new ComprobanteData();
                        $objComprobante->fe_comprobante_reg = date("Y-m-d H:i:s");
                        $objComprobante->cs_tipodocumento_cod = $objTipoDocumento->valor2;
                        $objComprobante->fe_comprobante_ser = $serie;
                        $objComprobante->fe_comprobante_cor = $secuencia;
                        $objComprobante->fe_comprobante_fec = date("Y-m-d");
                        $objComprobante->cs_tipomoneda_cod = "PEN";
                        $objComprobante->cs_tipodocumentoidentidad_cod = $objTipoDocumento->valor3;
                        $objComprobante->tb_cliente_numdoc = $numDocCliente;
                        $objComprobante->tb_cliente_nom = $datosCliente;
                        $objComprobante->tb_cliente_dir = $dirCliente;
                        
                        // No esta exonerado
                        if ($_SESSION["exonerado"] == 0) {
                            $objComprobante->fe_comprobante_totvengra = round($montoPagado / 1.18, 2);
                            $objComprobante->fe_comprobante_totvenina = 0.00;
                            $objComprobante->fe_comprobante_totvenexo = 0.00;
                            $objComprobante->fe_comprobante_totvengratui = 0.00;
                            $objComprobante->fe_comprobante_totdes = 0.00;
                            $objComprobante->fe_comprobante_sumigv = round(($objComprobante->fe_comprobante_totvengra * 0.18), 2);
                        } else {
                            $objComprobante->fe_comprobante_totvengra = 0.00;
                            $objComprobante->fe_comprobante_totvenina = 0.00;
                            $objComprobante->fe_comprobante_totvenexo = round($montoPagado, 2);
                            $objComprobante->fe_comprobante_totvengratui = 0.00;
                            $objComprobante->fe_comprobante_totdes = 0.00;
                            $objComprobante->fe_comprobante_sumigv = 0.00;
                        }
                        $objComprobante->fe_comprobante_sumisc = 0.00;
                        $objComprobante->fe_comprobante_sumotrtri = 0.00;
                        $objComprobante->fe_comprobante_desglo = 0.00;
                        $objComprobante->fe_comprobante_sumotrcar = 0.00;
                        $objComprobante->fe_comprobante_imptot = $montoPagado;
                        $objComprobante->cs_tipooperacion_cod = 1;
                        $objComprobante->fe_comprobante_detcod = "";
                        $objComprobante->fe_comprobante_detpor = 0.00;
                        $objComprobante->fe_comprobante_detmon = 0.00;
                        $objComprobante->cs_documentosrelacionados_cod = "";
                        $objComprobante->fe_comprobante_docrel = "";
                        $objComprobante->fe_comprobante_tipcam = 1.000;
                        $objComprobante->tb_notacredeb_tip = 6;
                        $objComprobante->tb_notacredeb_mot = "ANULACION";
                        $objComprobante->tb_notacredeb_tipdoc = 3;
                        $objComprobante->tb_notacredeb_numdoc = "B001-";
                        $objComprobante->fe_comprobante_faucod = "";
                        $objComprobante->fe_comprobante_digval = "";
                        $objComprobante->fe_comprobante_sigval = "";
                        $objComprobante->fe_comprobante_val = "";
                        $objComprobante->fe_comprobante_fecenvsun = "0000-00-00 00:00:00";
                        $objComprobante->fe_comprobante_resbol = $indicadorResBol;
                        $objComprobante->fe_comprobante_combaj = 0;
                        $objComprobante->fe_comprobante_estsun = 0;
                        $objComprobante->fe_comprobante_est = 1;
                        $objComprobante->historial_pago = $objHistorialPago->id;
                        $resultado = $objComprobante->add();
                        if (isset($resultado[1]) && $resultado[1] > 0) {
                            $idComprobante = $resultado[1];
                            
                            file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla comprobantes : " . $idComprobante . "\n", FILE_APPEND);
                            
                            // Actualizar el comprobante en la tabla historial_pagos
                            $objHistorialPago->comprobante = $idComprobante;
                            $resultado = $objHistorialPago->update();
                            if (isset($resultado[0]) && $resultado[0] == 1) {
                                file_put_contents("info" . date("Ymd") . ".log", "Actualizacion en tabla historial_pagos : " . $objHistorialPago->id . "\n", FILE_APPEND);
                                
                                $objHistorialDocumento = new HistorialDocumentoData();
                                $objHistorialDocumento->historial_pago = $objHistorialPago->id;
                                $objHistorialDocumento->comprobante = $idComprobante;
                                $objHistorialDocumento->estado = 1;
                                $resultado = $objHistorialDocumento->add();
                                if (isset($resultado[1]) && $resultado[1] > 0) {
                                    file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla historial_documentos : " . $resultado[1] . "\n", FILE_APPEND);
                                    
                                    $lstDetalle = DetallePedidoData::getProductosXPedido($objPago->pedido);
                                    
                                    $item = 1;
                                    foreach ($lstDetalle as $objDetalle) {
                                        $total = $objDetalle->precio_venta * $objDetalle->cantidad;
                                    
                                        $objDetalleComprobante = new DetalleComprobanteData();
                                        $objDetalleComprobante->fe_comprobantedetalle_nro = $item ++;
                                        $objDetalleComprobante->fe_comprobantedetalle_cod = $objDetalle->id;
                                        $objDetalleComprobante->fe_comprobantedetalle_nom = $objDetalle->nom_producto;
                                        $objDetalleComprobante->cs_tipoafectacionigv_cod = "10";
                                        $objDetalleComprobante->cs_tipounidadmedida_cod = "NIU";
                                        $objDetalleComprobante->fe_comprobantedetalle_can = $objDetalle->cantidad;
                                        $objDetalleComprobante->fe_comprobantedetalle_preuni = $objDetalle->precio_venta;
                                        $objDetalleComprobante->fe_comprobantedetalle_valrefuni = $objDetalle->precio_venta;
                                        
                                        // No esta exonerado
                                        if ($_SESSION["exonerado"] == 0) {
                                            $objDetalleComprobante->fe_comprobantedetalle_valuni = round(($objDetalle->precio_venta / 1.18), 2);
                                            $objDetalleComprobante->fe_comprobantedetalle_valven = round(($total / 1.18), 2);
                                            $objDetalleComprobante->fe_comprobantedetalle_des = 0.00;
                                            $objDetalleComprobante->fe_comprobantedetalle_igv = round(($objDetalleComprobante->fe_comprobantedetalle_valven * 0.18), 2);
                                        } else {
                                            $objDetalleComprobante->fe_comprobantedetalle_valuni = $objDetalle->precio_venta;
                                            $objDetalleComprobante->fe_comprobantedetalle_valven = round($total, 2);
                                            $objDetalleComprobante->fe_comprobantedetalle_des = 0.00;
                                            $objDetalleComprobante->fe_comprobantedetalle_igv = 0.00;
                                        }
                                        $objDetalleComprobante->cs_tiposistemacalculoisc_cod = "";
                                        $objDetalleComprobante->fe_comprobantedetalle_isc = 0.00;
                                        $objDetalleComprobante->fe_comprobante_id = $idComprobante;
                                        $resultado = $objDetalleComprobante->add();
                                        if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                            $error = 1;
                                            file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla detalle_comprobantes\n", FILE_APPEND);
                                            mysqli_rollback(Database::getCon());
                                            echo 0;
                                            break;
                                        }
                                        file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla detalle_comprobantes " . $resultado[1] . "\n", FILE_APPEND);
                                    }
                                } else {
                                    file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla historial_documentos\n", FILE_APPEND);
                                    mysqli_rollback(Database::getCon());
                                    echo 0;
                                }
                                if ($error == 0) {
                                    file_put_contents("info" . date("Ymd") . ".log", "Se genera comprobante\n", FILE_APPEND);
                                    mysqli_commit(Database::getCon());
                                    echo 1;
                                }
                            } else {
                                file_put_contents("info" . date("Ymd") . ".log", "Error actualizacion en tabla historial_pagos : " . $idHistorialPago . "\n", FILE_APPEND);
                                mysqli_rollback(Database::getCon());
                                echo 0;
                            }
                        } else {
                            file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla comprobantes\n", FILE_APPEND);
                            mysqli_rollback(Database::getCon());
                            echo 0;
                        }
                    } else {
                        mysqli_rollback(Database::getCon());
                        echo 0;
                    }
                } else {
                    file_put_contents("info" . date("Ymd") . ".log", "No existe data en tabla correlativos\n", FILE_APPEND);
                    mysqli_rollback(Database::getCon());
                    echo 0;
                }
            } else {
                mysqli_rollback(Database::getCon());
                echo 0;
            }
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
        } else if ($_POST["accion"] == 2) {
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "OPCION: ANULAR COMPROBANTE \n", FILE_APPEND);
            
            // Anular comprobante cuando es un pago total            
            $objHistorialDocumento = HistorialDocumentoData::getById($_POST["id"]);
            $objHistorialDocumento->estado = 0;            
            $resultado = $objHistorialDocumento->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla historial_documentos " . $objHistorialDocumento->id . "\n", FILE_APPEND);
                
                $objComprobante = $objHistorialDocumento->getComprobante();                
                $objComprobante->fe_comprobante_est = 2;
                $resultado = $objComprobante->update();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla historial_documentos " . $objComprobante->fe_comprobante_id . "\n", FILE_APPEND);
                    
                    $objHistorialPago = $objHistorialDocumento->getHistorialPago();
                    $objHistorialPago->comprobante = 0;
                    $resultado = $objHistorialPago->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla historial_pagos " . $objHistorialPago->id . "\n", FILE_APPEND);
                        mysqli_commit(Database::getCon());
                        echo $resultado[0];
                    } else {
                        file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla historial_pagos " . $objHistorialPago->id . "\n", FILE_APPEND);
                        mysqli_rollback(Database::getCon());
                        echo 0;
                    }
                } else {
                    file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla historial_documentos " . $objComprobante->fe_comprobante_id . "\n", FILE_APPEND);
                    mysqli_rollback(Database::getCon());
                    echo 0;
                }
            } else {
                file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla historial_documentos " . $objHistorialDocumento->id . "\n", FILE_APPEND);                
                mysqli_rollback(Database::getCon());
                echo 0;
            }
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
        } else if ($_POST["accion"] == 3) {
            // Anular comprobante desde la bandeja de comprobantes
            $objHistorialDocumento = HistorialDocumentoData::getById($_POST["id"]);
            $objHistorialDocumento->estado = 0;
            $resultado = $objHistorialDocumento->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                $objComprobante = $objHistorialDocumento->getComprobante();
                $objComprobante->fe_comprobante_est = 2;
                $resultado = $objComprobante->update();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    $objHistorialPago = $objHistorialDocumento->getHistorialPago();
                    $objHistorialPago->comprobante = 0;
                    $resultado = $objHistorialPago->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        mysqli_commit(Database::getCon());
                        echo $resultado[0];
                    } else {
                        mysqli_rollback(Database::getCon());
                        echo 0;
                    }
                } else {
                    mysqli_rollback(Database::getCon());
                    echo 0;
                }
            } else {
                mysqli_rollback(Database::getCon());
                echo 0;
            }
        } else if ($_POST["accion"] == 5) {
            // Actualizar documentos para ser enviados de nuevo a Sunat
            $objHistorialDocumento = HistorialDocumentoData::getById($_POST["id"]);
            if ($objHistorialDocumento) {
                $objComprobante = $objHistorialDocumento->getComprobante();
                $objComprobante->fe_comprobante_fecenvsun = "0000-00-00 00:00:00";
                $objComprobante->fe_comprobante_estsun = 0;
                $objComprobante->fe_comprobante_faucod = "";
                $resultado = $objComprobante->updateSunat();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    mysqli_commit(Database::getCon());
                    echo $resultado[0];
                } else {
                    mysqli_rollback(Database::getCon());
                    echo 0;
                }
            } else {
                mysqli_rollback(Database::getCon());
                echo 0;
            }
        }
    }
}

?>