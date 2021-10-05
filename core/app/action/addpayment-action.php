<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {
        $error = 0;
        mysqli_begin_transaction(Database::getCon());
        if ($_POST["accion"] == 2) {
            // Editar los datos del pago (Forma Pago / Tipo Tarjeta)
            $valoresIguales = true;
            $objHistorialPago = HistorialPagoData::getById($_POST["id"]);
            $objPago = PagoData::getById($objHistorialPago->pago);
            if ($objHistorialPago->forma_pago != $_POST["formapago"]) {
                $valoresIguales = false;
            }
            $objHistorialPago->forma_pago = $_POST["formapago"];
            $objParametro = ParametroData::getById($_POST["formapago"]);
            if ($objParametro && $objParametro->valor1 == 1) {
                // Tarjeta
                $objHistorialPago->tipo_tarjeta = $_POST["tipotarjeta"];
                $objHistorialPago->num_operacion = $_POST["numope"];
                if (!$valoresIguales) {
                    $total = 0;
                    $lstDetalleHistorialPago = DetalleHistorialPagoData::getAllByHistorialPago($objHistorialPago->id);
                    foreach ($lstDetalleHistorialPago as $objDetalleHistorialPago) {
                        $total += $objDetalleHistorialPago->total;
                    }
                    $objPago->monto_pagado_tarjeta = round($objPago->monto_pagado_tarjeta + $total, 2);
                    $objPago->monto_pagado_efectivo = round($objPago->monto_pagado_efectivo - $total, 2);
                    $objHistorialPago->monto_tarjeta = $objHistorialPago->monto_efectivo;
                    $objHistorialPago->monto_efectivo = 0;
                }                
            } else if ($objParametro && $objParametro->valor1 == 0) {
                // Efectivo            
                $objHistorialPago->tipo_tarjeta = 0;
                $objHistorialPago->num_operacion = "";
                if (!$valoresIguales) {
                    $total = 0;
                    $lstDetalleHistorialPago = DetalleHistorialPagoData::getAllByHistorialPago($objHistorialPago->id);
                    foreach ($lstDetalleHistorialPago as $objDetalleHistorialPago) {
                        $total += $objDetalleHistorialPago->total;
                    }
                    $objPago->monto_pagado_tarjeta = round($objPago->monto_pagado_tarjeta - $total, 2);
                    $objPago->monto_pagado_efectivo = round($objPago->monto_pagado_efectivo + $total, 2);
                    $objHistorialPago->monto_efectivo = $objHistorialPago->monto_tarjeta;
                    $objHistorialPago->monto_tarjeta = 0;
                }
            }
            $resultado = $objHistorialPago->update();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                // En el caso que se haya actualizado la forma de pago
                if (!$valoresIguales) {
                    $resultado = $objPago->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        mysqli_commit(Database::getCon());
                        echo $resultado[0];
                    } else {
                        mysqli_rollback(Database::getCon());
                        echo 0;
                    }
                } else {
                    mysqli_commit(Database::getCon());
                    echo $resultado[0];
                }
            } else {
                mysqli_rollback(Database::getCon());
                echo 0;
            }
        } else if ($_POST["accion"] == 3) {
            // Anular el pago cuando es parcial
            $error = $total = 0;
            $objHistorialPago = HistorialPagoData::getById($_POST["id"]);
            $objHistorialPago->estado = 0;
            $resultado = $objHistorialPago->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                $lstDetalleHistorialPago = DetalleHistorialPagoData::getAllByHistorialPago($objHistorialPago->id);
                foreach ($lstDetalleHistorialPago as $objDetalleHistorialPago) {
                    $total += $objDetalleHistorialPago->total;
                }
                if ($error == 0) {
                    $objHistorialDocumento = HistorialDocumentoData::getByHistorialPago($objHistorialPago->id);
                    $objHistorialDocumento->estado = 0;
                    $resultado = $objHistorialDocumento->delete();
                    if (isset($resultado[0]) && $resultado[0] == 1) {                    
                        $objComprobante = $objHistorialDocumento->getComprobante();
                        $objComprobante->fe_comprobante_est = 2;
                        $resultado = $objComprobante->update();
                        if (isset($resultado[0]) && $resultado[0] == 1) {                        
                            $objPago = PagoData::getById($objHistorialPago->pago);
                            $objParametro = ParametroData::getById($objHistorialPago->forma_pago);
                            if ($objParametro) {
                                if ($objParametro->valor1 == 0) {
                                    // Efectivo
                                    $objPago->monto_pagado_efectivo = $objPago->monto_pagado_efectivo - $total;
                                } else if ($objParametro->valor1 == 1) {
                                    // Tarjeta
                                    $objPago->monto_pagado_tarjeta = $objPago->monto_pagado_tarjeta - $total;
                                }
                            }
                            $resultado = $objPago->update();
                            if (isset($resultado[0]) && $resultado[0] == 1) {
                                $lstDetallePedido = DetallePedidoData::getProductosXPedido($objPago->pedido);
                                foreach ($lstDetallePedido as $objDetallePedido) {
                                    $lstDetalleHistorialPago = DetalleHistorialPagoData::getAllByHistorialPago($objHistorialPago->id, 1, $objDetallePedido->producto);
                                    if (count($lstDetalleHistorialPago) > 0) {                                        
                                        $objDetallePedido->cantidad_pagada = $objDetallePedido->cantidad_pagada - $lstDetalleHistorialPago[0]->cantidad;
                                        $resultado = $objDetallePedido->update();
                                        if (isset($resultado[0]) && $resultado[0] == 1) {
                                            $objDetalleHistorialPago = $lstDetalleHistorialPago[0];
                                            $objDetalleHistorialPago->estado = 0;
                                            $resultado = $objDetalleHistorialPago->delete();
                                            if (!(isset($resultado[0]) && $resultado[0] == 1)) {
                                                $error = 1;
                                                mysqli_rollback(Database::getCon());
                                                break;
                                            }
                                        }
                                    }
                                }
                                if ($error == 0) {
                                    $objPedido = PedidoData::getById($objPago->pedido);
                                    if ($objPedido->estado == 2) {
                                        $objPedido->estado = 1;
                                        $objPedido->fecha_actualizacion = date("Y-m-d H:i:s");
                                        $objPedido->usuario_actualizacion = $_SESSION["user"];
                                        $resultado = $objPedido->update();
                                        if (isset($resultado[0]) && $resultado[0] == 1) {
                                            mysqli_commit(Database::getCon());
                                            echo $resultado[0];
                                        } else {
                                            mysqli_rollback(Database::getCon());
                                            echo 0;
                                        }
                                    } else {
                                        mysqli_commit(Database::getCon());
                                        echo $objPedido->id;
                                    }
                                }
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