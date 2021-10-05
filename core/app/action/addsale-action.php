<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {
        mysqli_begin_transaction(Database::getCon(), MYSQLI_TRANS_START_READ_WRITE);
        if ($_POST["accion"] == 1) {
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "OPCION: REGISTRAR PEDIDO \n", FILE_APPEND);
            
            // Registrar Pedido
            $lstDetalle = array();
            if (isset($_SESSION["productos"])) {
                $lstDetalle = $_SESSION["productos"];
            }
            if (count($lstDetalle) > 0) {
                $existeError = false;
                if ($_POST["id"] == 0) {
                    $objPedido = new PedidoData();
                    $objPedido->sede = $_POST["idsede"];
                    $objPedido->piso = $_POST["idpiso"];
                    $objPedido->mesa = $_POST["idmesa"];
                    $objPedido->num_comensales = 0;
                    if (isset($_POST["numcomensales"])) {
                        $objPedido->num_comensales = trim($_POST["numcomensales"]);
                    }
                    if (isset($_POST["telefono"]) && isset($_POST["direccion"]) && isset($_POST["datos"]) && isset($_POST["hora"])) {
                        $objPedido->telefono = trim($_POST["telefono"]);
                        $objPedido->direccion = strtoupper(trim($_POST["direccion"]));
                        $objPedido->datos = strtoupper(trim($_POST["datos"]));
                        $objPedido->hora = strtoupper(trim($_POST["hora"]));
                        
                        /*$objTelefono = new TelefonoData();
                        $objTelefono->telefono = $objPedido->telefono;
                        $objTelefono->contacto = $objPedido->datos;
                        $objTelefono->direccion = $objPedido->direccion;
                        $objTelefono->add();*/
                    } else if (isset($_POST["datos"]) && isset($_POST["hora"])) {
                        $objPedido->telefono = "";
                        $objPedido->direccion = "";
                        $objPedido->datos = strtoupper(trim($_POST["datos"]));
                        $objPedido->hora = strtoupper(trim($_POST["hora"]));
                    }
                    $objPedido->fecha = date("Y-m-d H:i:s");
                    $objPedido->descuento_programado = 0.00;
                    $objPedido->descuento_pedido = 0.00;
                    $objPedido->servicio = 0.00;
                    $objPedido->subtotal = 0.00;
                    $objPedido->igv = 0.00;
                    $objPedido->total = 0.00;
                    $objPedido->mozo = $_POST["idusuario"];
                    $objPedido->tipo = $_POST["tipopedido"];
                    $objPedido->fecha_actualizacion = date("Y-m-d H:i:s");
                    $objPedido->usuario_actualizacion = $_SESSION["user"];
                    $objPedido->estado = 1;
                    $resultado = $objPedido->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        $idPedido = $resultado[1];

                        file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla pedidos : " . $idPedido . "\n", FILE_APPEND);
                        
                        $indicador = 0;
                        $objPiso = PisoData::getById($objPedido->piso);
                        if ($objPiso) {
                            $indicador = $objPiso->indicador;
                        }
                        
                        $porcentajeServicio = 0;
                        $lstParametro = ParametroData::getAll(1, "OPCIONES GENERALES", "SERVICIO");
                        if (count($lstParametro) > 0) {
                            $porcentajeServicio = $lstParametro[0]->valor1;
                        }
                        
                        $lstPromocion = "";
                        $arrPromocion = array();
                        foreach ($lstDetalle as $valor) {
                            $data = explode("|", $valor);
                            
                            $objProducto = ProductoData::getById($data[0]);
                            if ($objProducto->getTipo()->valor1 != "") {
                                if (isset($arrPromocion[$objProducto->getTipo()->id])) {
                                    $arrPromocion[$objProducto->getTipo()->id] = $arrPromocion[$objProducto->getTipo()->id] + $data[1];
                                } else {
                                    $arrPromocion[$objProducto->getTipo()->id] = $data[1];
                                    $lstPromocion .= $objProducto->getTipo()->id.",";
                                }
                            }
                        }
                        $numPromociones = $montoPromocion = 0;
                        $lstPromocion = substr($lstPromocion, 0, strlen($lstPromocion) - 1);
                        $arrLstPromocion = explode(",", $lstPromocion);
                        for ($indice = 0; $indice < count($arrLstPromocion); $indice ++) {
                            $objTipo = ParametroData::getById($arrLstPromocion[$indice]);
                            if ($objTipo) {
                                if ($objTipo->valor1 != "") {
                                    if (($arrPromocion[$arrLstPromocion[$indice]] % $objTipo->valor1) == 0) {
                                        $numPromociones = $arrPromocion[$arrLstPromocion[$indice]] / $objTipo->valor1;
                                        $montoPromocion = $objTipo->valor2 * $numPromociones;
                                    }
                                }
                            }
                        }
                        $montoProductoPromocion = 0;
                        foreach ($lstDetalle as $valor) {
                            $data = explode("|", $valor);
                            
                            $objProducto = ProductoData::getById($data[0]);
                            $precioVenta = $objProducto->precio1;
                            if ($objProducto->getTipo()->valor1 != "") {
                                if ($indicador == 1) {
                                    $precioVenta = $objProducto->precio2;
                                }
                                $montoProductoPromocion += $precioVenta * $data[1];
                            }                            
                        }
                        $dsctoPromocion = $montoProductoPromocion - $montoPromocion;
                        
                        $totalDescuento = $totalServicio = $totalIgv = $totalGeneral = $totalSubtotal = 0;
                        foreach ($lstDetalle as $valor) {
                            $data = explode("|", $valor);

                            $objProducto = ProductoData::getById($data[0]);
                            $precioCosto = $objProducto->costo;
                            $precioVenta = $objProducto->precio1;
                            if ($indicador == 1) {
                                $precioVenta = $objProducto->precio2;
                            }
                            $objCategoria = $objProducto->getCategoria();
                            $cantidad = $data[1];
                            $comentario = $data[2];
                            
                            $descuento = 0;
                            $precioReal = $precioVenta;
                            $objDescuentoProgramado = DescuentoProgramadoData::getDescuentoXProducto($objPedido->sede, $objProducto->id, date("Y-m-d"));
                            if ($objDescuentoProgramado) {
                                // En caso tenga un descuento programado
                                $precioVenta = $objDescuentoProgramado->precio_descuento;
                                $precioReal = $objDescuentoProgramado->precio_actual;
                                $descuento = ($precioReal - $precioVenta) * $cantidad;
                            }
                            
                            $servicio = 0;
                            $total = $precioVenta * $cantidad;
                            if ($porcentajeServicio > 0) {
                                $servicio = $total * $porcentajeServicio;
                            }
                            
                            $subtotal = $igv = 0;
                            if ($_SESSION["exonerado"] == 0) {
                                $subtotal = ($total - $servicio) / 1.18;
                                $igv = $subtotal * 0.18;
                            } else if ($_SESSION["exonerado"] == 1) {
                                $subtotal = ($total - $servicio);
                            }

                            $totalIgv += $igv;
                            $totalGeneral += $total;
                            $totalSubtotal += $subtotal;
                            $totalDescuento += $descuento;
                            $totalServicio += $servicio;
                            
                            $objDetallePedido = new DetallePedidoData();
                            $objDetallePedido->pedido = $idPedido;
                            $objDetallePedido->producto = $objProducto->id;
                            $objDetallePedido->nom_producto = $objProducto->nombre;
                            $objDetallePedido->tipo = $objProducto->getTipo()->nombre;
                            $objDetallePedido->categoria = $objCategoria->nombre;
                            $objDetallePedido->cantidad = $cantidad;
                            $objDetallePedido->comentario = $comentario;
                            $objDetallePedido->precio_costo = $precioCosto;
                            $objDetallePedido->precio_venta = $precioVenta;
                            $objDetallePedido->precio_real = $precioReal;
                            $objDetallePedido->subtotal = round($subtotal, 2);
                            $objDetallePedido->igv = round($igv, 2);
                            $objDetallePedido->total = round($total, 2);
                            $objDetallePedido->estado = 1;
                            $objDetallePedido->fecha_actualizacion = date("Y-m-d H:i:s");
                            $objDetallePedido->usuario_actualizacion = $_SESSION["user"];
                            $objDetallePedido->fecha_creacion = date("Y-m-d H:i:s");
                            $objDetallePedido->usuario_creacion = $_SESSION["user"];
                            $resultado = $objDetallePedido->add();
                            if (isset($resultado[1]) && $resultado[1] > 0) {
                                file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla detalle_pedidos : " . $resultado[1] . "\n", FILE_APPEND);
                                
                                // Obtener los insumos asociados a cada producto
                                $lstInsumoXProducto = DetalleRecetaData::getAllInsumosByProducto($objProducto->id);
                                if (count($lstInsumoXProducto) > 0) {
                                    foreach ($lstInsumoXProducto as $objInsumoXProducto) {
                                        $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objInsumoXProducto->insumo, $_POST["idalmacen"]);
                                        if (count($lstInsumoAlmacen) > 0) {
                                            // En caso el insumo tenga stock en el almacen
                                            $objInsumoAlmacen = $lstInsumoAlmacen[0];
                                            $objInsumoAlmacen->stock = $objInsumoAlmacen->stock - ($objInsumoXProducto->cantidad * $cantidad);
                                            $resultado = $objInsumoAlmacen->updateStock();
                                            if (isset($resultado[0]) && $resultado[0] == 1) {
                                                $objMovimiento = new MovimientoData();
                                                $objMovimiento->sede = $objPedido->sede;
                                                $objMovimiento->insumo = $objInsumoXProducto->insumo;
                                                $objMovimiento->tipo = 0;
                                                $objMovimiento->cantidad = $objInsumoXProducto->cantidad * $cantidad;
                                                $objMovimiento->detalle = "PEDIDO REGISTRADO " . str_pad($idPedido, 8, "0", STR_PAD_LEFT);
                                                $objMovimiento->modulo = "1";
                                                $objMovimiento->fecha = date("Y-m-d H:i:s");
                                                $objMovimiento->estado = 1;
                                                $resultado = $objMovimiento->add();
                                                if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                                    file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla movimientos\n", FILE_APPEND);
                                                    $existeError = true;
                                                    break;
                                                }
                                                file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla movimientos : " . $resultado[1] . "\n", FILE_APPEND);
                                            }
                                        } else {
                                            // En caso el insumo no tenga stock en el almacen
                                            $objInsumoAlmacen = new InsumoAlmacenData();
                                            $objInsumoAlmacen->almacen = $_POST["idalmacen"];
                                            $objInsumoAlmacen->insumo = $objInsumoXProducto->insumo;
                                            $objInsumoAlmacen->stock = ($objInsumoXProducto->cantidad * $cantidad * - 1);
                                            $objInsumoAlmacen->stock_minimo = 0.00;
                                            $objInsumoAlmacen->stock_maximo = 0.00;
                                            $objInsumoAlmacen->estado = 1;
                                            $resultado = $objInsumoAlmacen->add();
                                            if (isset($resultado[1]) && $resultado[1] > 0) {
                                                file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla insumos_almacen : " . $resultado[1] . "\n", FILE_APPEND);
                                                
                                                $objMovimiento = new MovimientoData();
                                                $objMovimiento->sede = $objPedido->sede;
                                                $objMovimiento->insumo = $objInsumoXProducto->insumo;
                                                $objMovimiento->tipo = 0;
                                                $objMovimiento->cantidad = $objInsumoXProducto->cantidad * $cantidad;
                                                $objMovimiento->detalle = "PEDIDO REGISTRADO " . str_pad($idPedido, 8, "0", STR_PAD_LEFT);
                                                $objMovimiento->modulo = "1";
                                                $objMovimiento->fecha = date("Y-m-d H:i:s");
                                                $objMovimiento->estado = 1;
                                                $resultado = $objMovimiento->add();
                                                if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                                    file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla movimientos\n", FILE_APPEND);
                                                    $existeError = true;
                                                    break;
                                                }
                                                file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla movimientos : " . $resultado[1] . "\n", FILE_APPEND);
                                            } else {
                                                file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla insumos_almacen\n", FILE_APPEND);
                                                
                                                $existeError = true;
                                                break;
                                            }
                                        }
                                    }
                                }
                            } else {
                                file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla detalle_pedidos\n", FILE_APPEND);                                
                                $existeError = true;
                            }
                        }
                        if (!$existeError) {
                            $objPedido->descuento_pedido = round($dsctoPromocion, 2);
                            $objPedido->descuento_programado = round($totalDescuento, 2);
                            $objPedido->servicio = round($totalServicio, 2);
                            $objPedido->subtotal = round($totalSubtotal, 2);
                            $objPedido->igv = round($totalIgv, 2);
                            $objPedido->total = round($totalGeneral, 2);
                            $objPedido->id = $idPedido;
                            $resultado = $objPedido->update();
                            if (isset($resultado[0]) && $resultado[0] == 1) {
                                file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla pedidos : " . $idPedido . "\n", FILE_APPEND);
                                mysqli_commit(Database::getCon());

                                unset($_SESSION["productos"]);
                                unset($_SESSION["tmp_productos"]);

                                echo $idPedido;
                            } else {
                                file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla pedidos : " . $idPedido . "\n", FILE_APPEND);
                                mysqli_rollback(Database::getCon());
                                echo 0;
                            }
                        } else {
                            mysqli_rollback(Database::getCon());
                            echo 0;
                        }
                    } else {
                        file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla pedidos\n", FILE_APPEND);                        
                        mysqli_rollback(Database::getCon());
                        echo 0;
                    }
                }
            } else {
                // En caso no haya info de productos
                echo - 1;
                file_put_contents("info" . date("Ymd") . ".log", "No hay productos en el pedido\n", FILE_APPEND);                
            }
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
        } else if ($_POST["accion"] == 2) {
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "OPCION: ANULAR PEDIDO \n", FILE_APPEND);
            
            // Anular pedido
            $objPedido = PedidoData::getById($_POST["id"]);
            $objPedido->estado = 0;
            $objPedido->fecha_actualizacion = date("Y-m-d H:i:s");
            $objPedido->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objPedido->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
        
                $existeError = false;
                $objPago = PagoData::getByPedido($_POST["id"]);
                if ($objPago) {
                    $objPago->estado = 0;
                    $resultado = $objPago->delete();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla pagos : " . $objPago->id . "\n", FILE_APPEND);
                        
                        $lstHistorialPago = HistorialPagoData::getAllByPago($objPago->id);
                        if (count($lstHistorialPago) > 0) {
                            foreach ($lstHistorialPago as $objHistorialPago) {
                                $objHistorialPago->estado = 0;
                                $resultado = $objHistorialPago->delete();
                                if (isset($resultado[0]) && $resultado[0] == 1) {
                                    file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla historial_pagos : " . $objHistorialPago->id . "\n", FILE_APPEND);
                                    
                                    $objHistorialDocumento = HistorialDocumentoData::getByHistorialPago($objHistorialPago->id);
                                    if ($objHistorialDocumento) {
                                        $objHistorialDocumento->estado = 0;
                                        $resultado = $objHistorialDocumento->delete();
                                        if (isset($resultado[0]) && $resultado[0] == 1) {
                                            file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla historial_documento : " . $objHistorialDocumento->id . "\n", FILE_APPEND);
                                            
                                            $objComprobante = ComprobanteData::getById($objHistorialDocumento->comprobante);
                                            if ($objComprobante) {
                                                $objComprobante->fe_comprobante_est = 2;
                                                $resultado = $objComprobante->update();
                                                if (isset($resultado[0]) && $resultado[0] == 1) {
                                                    file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla comprobantes : " . $objComprobante->fe_comprobante_id . "\n", FILE_APPEND);
                                                } else {
                                                    file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla comprobantes : " . $objComprobante->fe_comprobante_id . "\n", FILE_APPEND);
                                                    $existeError = true;
                                                    break;
                                                }
                                            }
                                        } else {
                                            file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla historial_documento : " . $objHistorialDocumento->id . "\n", FILE_APPEND);
                                            $existeError = true;
                                            break;
                                        }
                                    }
                                } else {
                                    file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla historial_pagos : " . $objHistorialPago->id . "\n", FILE_APPEND);
                                    $existeError = true;
                                    break;
                                }
                            }
                        }
                    } else {
                        file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla pagos : " . $objPago->id . "\n", FILE_APPEND);
                        $existeError = true;
                    }                    
                }
                if ($existeError) {
                    mysqli_rollback(Database::getCon());
                    echo 0;
                } else {
                    $lstDetallePedido = DetallePedidoData::getProductosXPedido($objPedido->id);
                    foreach ($lstDetallePedido as $objDetallePedido) {
                        // Obtener los insumos asociados a cada producto
                        $lstInsumoXProducto = DetalleRecetaData::getAllInsumosByProducto($objDetallePedido->producto);
                        if (count($lstInsumoXProducto) > 0) {
                            $lstAlmacen = AlmacenData::getAll(1, $_SESSION["empresa"], $objPedido->sede);
                            if (count($lstAlmacen) > 0) {
                                $objAlmacen = $lstAlmacen[0];
                                foreach ($lstInsumoXProducto as $objInsumoXProducto) {
                                    $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objInsumoXProducto->insumo, $objAlmacen->id);
                                    if (count($lstInsumoAlmacen) > 0) {
                                        $objInsumoAlmacen = $lstInsumoAlmacen[0];
                                        $objInsumoAlmacen->stock = $objInsumoAlmacen->stock + ($objInsumoXProducto->cantidad * $objDetallePedido->cantidad);
                                        $resultado = $objInsumoAlmacen->updateStock();
                                        if (isset($resultado[0]) && $resultado[0] == 1) {
                                            file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla insumos_almacen : (Insumo : " . $objInsumoXProducto->insumo . " - Almacen : " . $objAlmacen->id . ")\n", FILE_APPEND);
                                            
                                            $objMovimiento = new MovimientoData();
                                            $objMovimiento->sede = $objPedido->sede;
                                            $objMovimiento->insumo = $objInsumoXProducto->insumo;
                                            $objMovimiento->tipo = 1;
                                            $objMovimiento->cantidad = $objInsumoXProducto->cantidad * $objDetallePedido->cantidad;
                                            $objMovimiento->detalle = "PEDIDO ANULADO " . str_pad($objPedido->id, 8, "0", STR_PAD_LEFT);
                                            $objMovimiento->modulo = "1";
                                            $objMovimiento->fecha = date("Y-m-d H:i:s");
                                            $objMovimiento->estado = 1;
                                            $resultado = $objMovimiento->add();
                                            if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                                file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla movimientos\n", FILE_APPEND);
                                                $existeError = true;
                                                break;
                                            }
                                            file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla movimientos : " . $resultado[1] . "\n", FILE_APPEND);
                                        } else {
                                            file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla insumos_almacen : (Insumo : " . $objInsumoXProducto->insumo . " - Almacen : " . $objAlmacen->id . ")\n", FILE_APPEND);
                                            $existeError = true;
                                            break;
                                        }
                                    }
                                }
                                if ($existeError) {
                                    break;
                                }
                            }
                        }
                    }
                    if ($existeError) {
                        mysqli_rollback(Database::getCon());
                        echo 0;
                    } else {
                        mysqli_commit(Database::getCon());
                        echo $objPedido->id;
                    }
                }
            } else {
                file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
                mysqli_rollback(Database::getCon());
                echo 0;
            }
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);            
        } else if ($_POST["accion"] == 3) {
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "OPCION: REGISTRAR PAGO TOTAL \n", FILE_APPEND);
            
            $idPago = $idHistorialPago = $error = $esCredito = 0;
            
            // Pago Completo
            $objPago = new PagoData();
            $objPago->pedido = $_POST["id"];
            $objPago->tipo_pago = $_POST["tipopago"];
            $objPago->monto_total = str_replace(",", "", trim($_POST["montototal"]));
            $objPago->porcentaje_descuento = str_replace(",", "", trim($_POST["descuento"])) / 100;
            $objPago->monto_descuento = round(($objPago->porcentaje_descuento * $objPago->monto_total), 1);
            $objParametro = ParametroData::getById($_POST["formapago"]);
            if ($objParametro) {
                file_put_contents("info" . date("Ymd") . ".log", "Monto Total : " . $objPago->monto_total . "\n", FILE_APPEND);
                file_put_contents("info" . date("Ymd") . ".log", "Porcentaje Descuento : " . $objPago->porcentaje_descuento . "\n", FILE_APPEND);
                file_put_contents("info" . date("Ymd") . ".log", "Monto Descuento : " . $objPago->monto_descuento . "\n", FILE_APPEND);
                file_put_contents("info" . date("Ymd") . ".log", "Monto Pagado : " . $_POST["montopagado"] . "\n", FILE_APPEND);
                
                if ($objPago->porcentaje_descuento > 0) {
                    $_POST["montopagado"] = round($objPago->monto_total - $objPago->monto_descuento, 2);
                    file_put_contents("info" . date("Ymd") . ".log", "Monto Pagado Corregido : " . $_POST["montopagado"] . "\n", FILE_APPEND);
                 }
                if ($objParametro->valor1 == 0) {
                    // Efectivo
                    $objPago->monto_pagado_efectivo = str_replace(",", "", trim($_POST["montopagado"]));
                    $objPago->monto_pagado_tarjeta = 0.00;
                    $objPago->monto_credito = 0.00;
                                        
                    
                    file_put_contents("info" . date("Ymd") . ".log", "Efectivo - Monto Efectivo : " . $objPago->monto_pagado_efectivo . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Efectivo - Monto Tarjeta : " . $objPago->monto_pagado_tarjeta . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Efectivo - Monto Credito : " . $objPago->monto_credito . "\n", FILE_APPEND);
                } else if ($objParametro->valor1 == 1) {
                    // Tarjeta
                    $objPago->monto_pagado_efectivo = 0.00;
                    $objPago->monto_pagado_tarjeta = str_replace(",", "", trim($_POST["montopagado"]));
                    $objPago->monto_credito = 0.00;
                    
                    file_put_contents("info" . date("Ymd") . ".log", "Tarjeta - Monto Efectivo : " . $objPago->monto_pagado_efectivo . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Tarjeta - Monto Tarjeta : " . $objPago->monto_pagado_tarjeta . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Tarjeta - Monto Credito : " . $objPago->monto_credito . "\n", FILE_APPEND);
                } else if ($objParametro->valor1 == 2) {
                    // Mixta
                    $montoEfectivo = str_replace(",", "", trim($_POST["montoefectivo"]));
                    $montoTarjeta = str_replace(",", "", trim($_POST["montotarjeta"]));
                    
                    if ($montoEfectivo == 0 || $montoTarjeta == 0) {
                        echo -1;
                        return;
                    }                    
                    $objPago->monto_pagado_efectivo = $montoEfectivo;
                    $objPago->monto_pagado_tarjeta = $montoTarjeta;
                    $objPago->monto_credito = 0.00;
                    
                    file_put_contents("info" . date("Ymd") . ".log", "Monto Pagado - Efectivo : " . $_POST["montoefectivo"] . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Monto Pagado - Tarjeta : " . $_POST["montotarjeta"] . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Mixta - Monto Efectivo : " . $objPago->monto_pagado_efectivo . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Mixta - Monto Tarjeta : " . $objPago->monto_pagado_tarjeta . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Mixta - Monto Credito : " . $objPago->monto_credito . "\n", FILE_APPEND);                    
                } else if ($objParametro->valor1 == 3) {
                    // Credito
                    $objPago->monto_pagado_efectivo = 0.00;
                    $objPago->monto_pagado_tarjeta = 0.00;
                    $objPago->monto_credito = str_replace(",", "", trim($_POST["montopagado"]));
                    $esCredito = 1;
                    
                    file_put_contents("info" . date("Ymd") . ".log", "Credito - Monto Efectivo : " . $objPago->monto_pagado_efectivo . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Credito - Monto Tarjeta : " . $objPago->monto_pagado_tarjeta . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Credito - Monto Credito : " . $objPago->monto_credito . "\n", FILE_APPEND);
                }
            }
            $objPago->estado = 1;
            $objPago->fecha_creacion = date("Y-m-d H:i:s");
            $objPago->usuario_creacion = $_SESSION["user"];
            $resultado = $objPago->add();
            if (isset($resultado[1]) && $resultado[1] > 0) {
                $idPago = $resultado[1];

                file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla pagos : " . $idPago . "\n", FILE_APPEND);

                // Registrar el historial del pago
                $objHistorialPago = new HistorialPagoData();
                $objHistorialPago->pago = $idPago;
                $objHistorialPago->forma_pago = $_POST["formapago"];
                $objParametro = ParametroData::getById($objHistorialPago->forma_pago);
                if ($objParametro && $objParametro->valor1 == 1 || $objParametro && $objParametro->valor1 == 2) {
                    // Tarjeta y Mixta
                    $objHistorialPago->tipo_tarjeta = $_POST["tipotarjeta"];
                    $objHistorialPago->num_operacion = $_POST["numope"];
                    
                    $objHistorialPago->monto_efectivo = 0.00;
                    $objHistorialPago->monto_tarjeta = $objPago->monto_pagado_tarjeta;
                    if ($objParametro && $objParametro->valor1 == 2) {
                        $objHistorialPago->monto_efectivo = $objPago->monto_pagado_efectivo;
                    }
                    $objHistorialPago->monto_credito = 0.00;
                } else if ($objParametro && $objParametro->valor1 == 3) {
                    // Credito
                    $objHistorialPago->num_documento = trim($_POST["numdocumento"]);
                    $objHistorialPago->cliente = strtoupper(trim($_POST["nomcliente"]));
                    $objHistorialPago->monto_efectivo = 0.00;
                    $objHistorialPago->monto_tarjeta = 0.00;
                    $objHistorialPago->monto_credito = $objPago->monto_credito;
                    $objHistorialPago->tipo_tarjeta = 0;
                } else {
                    // Efectivo
                    $objHistorialPago->monto_efectivo = $objPago->monto_pagado_efectivo;
                    $objHistorialPago->monto_tarjeta = 0.00;
                    $objHistorialPago->monto_credito = 0.00;
                    $objHistorialPago->tipo_tarjeta = 0;
                }
                $objHistorialPago->fecha = date("Y-m-d H:i:s");
                $objHistorialPago->usuario = $_POST["usuario"];
                $objHistorialPago->caja = $_POST["caja"];
                $objHistorialPago->estado = 1;
                $resultado = $objHistorialPago->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    $idHistorialPago = $resultado[1];
                    
                    file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla historial_pagos : " . $idHistorialPago. "\n", FILE_APPEND);
                    
                    // Actualizar el detalle del pedido (cantidad pagada)                    
                    $lstDetallePedido = DetallePedidoData::getProductosXPedido($objPago->pedido);
                    foreach ($lstDetallePedido as $objDetallePedido) {
                        $objDetallePedido->cantidad_pagada = $objDetallePedido->cantidad;
                        if ($objDetallePedido->fecha_comanda == "") {
                            $objDetallePedido->fecha_comanda = "0000-00-00 00:00:00";
                        }
                        $resultado = $objDetallePedido->update();
                        if (isset($resultado[0]) && $resultado[0] == 1) {
                            file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla detalle_pedidos : " . $objDetallePedido->id . "\n", FILE_APPEND);
                            
                            // Registrar el detalle historial del pago
                            $objDetalleHistorialPago = new DetalleHistorialPagoData();
                            $objDetalleHistorialPago->historial_pago = $idHistorialPago;
                            $objDetalleHistorialPago->nom_categoria = $objDetallePedido->categoria;
                            $objDetalleHistorialPago->producto = $objDetallePedido->producto;
                            $objDetalleHistorialPago->nom_producto = $objDetallePedido->nom_producto;
                            $objDetalleHistorialPago->cantidad = $objDetallePedido->cantidad;
                            $objDetalleHistorialPago->precio = $objDetallePedido->precio_venta;
                            $objDetalleHistorialPago->total = $objDetallePedido->total;
                            $objDetalleHistorialPago->estado = 1;
                            $resultado = $objDetalleHistorialPago->add();
                            if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla detalle_historial_pagos\n", FILE_APPEND);
                                $error = 1;
                                break;
                            }
                            file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla detalle_historial_pagos " . $resultado[1] . "\n", FILE_APPEND);
                        } else {
                            file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla detalle_pedidos: " . $objDetallePedido->id . "\n", FILE_APPEND);
                            $error = 1;
                            break;
                        }
                    }
                    if ($error == 0 && $esCredito == 1) {
                        // En el caso que sea a credito, registrar el monto
                        $objCredito = CreditoData::getByNumDoc(trim($_POST["numdocumento"]));
                        if ($objCredito) {
                            $objCredito->monto = $objCredito->monto + $objPago->monto_credito;
                            $resultado = $objCredito->update();
                            if (!(isset($resultado[0]) && $resultado[0] == 1)) {
                                file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla creditos : " . $objCredito->id . "\n", FILE_APPEND);
                                $error = 1;
                            }
                            file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla creditos : " . $objCredito->id . "\n", FILE_APPEND);
                        } else {
                            $objCredito = new CreditoData();
                            $objCredito->num_documento = trim($_POST["numdocumento"]);
                            $objCredito->datos = strtoupper(trim($_POST["nomcliente"]));
                            $objCredito->monto = $objPago->monto_credito;
                            $objCredito->abono = 0.00;
                            $resultado = $objCredito->add();
                            if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla creditos\n", FILE_APPEND);
                                $error = 1;
                            }
                            file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla creditos : " . $resultado[1]. "\n", FILE_APPEND);
                        }
                    }
                    if ($error == 0 && isset($_POST["idcredito"]) && $_POST["idcredito"] > 0) {
                        // En el caso que se paga un credito
                        $objCredito = CreditoData::getByNumDoc(trim($_POST["numdoc"]));
                        $objCredito->abono = $objCredito->abono + $objPago->monto_total;
                        $resultado = $objCredito->update();
                        if (!(isset($resultado[0]) && $resultado[0] == 1)) {
                            file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla creditos : " . $objCredito->id . "\n", FILE_APPEND);
                            $error = 1;
                        } else {
                            file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla creditos : " . $objCredito->id . "\n", FILE_APPEND);
                            
                            $objHistorialCredito = new HistorialCreditoData();
                            $objHistorialCredito->credito = $objCredito->id;
                            $objHistorialCredito->fecha = date("Y-m-d H:i:s");
                            $objHistorialCredito->monto = $objPago->monto_total;
                            $resultado = $objHistorialCredito->add();
                            if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla historial_creditos : (Credito : " . $objCredito->id . ")\n", FILE_APPEND);
                                $error = 1;
                            }
                            file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla historial_creditos : " . $resultado[1] . "\n", FILE_APPEND);
                        }
                    }
                } else {
                    $error = 1;
                }
                if ($error == 0) {
                    // Actualizar el estado del pedido
                    $objPedido = PedidoData::getById($objPago->pedido);
                    $objPedido->descuento_pedido = $objPedido->descuento_pedido + $objPago->monto_descuento;
                    if ($esCredito == 0) {
                        $objPedido->estado = 2;
                    } else {
                        $objPedido->estado = 3;
                    }
                    $objPedido->fecha_actualizacion = date("Y-m-d H:i:s");
                    $objPedido->usuario_actualizacion = $_SESSION["user"];
                    $resultado = $objPedido->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
                        
                        // Genera Ticket, Boleta, Factura
                        if (!isset($_POST["generadoc"])) {
                            $_POST["generadoc"] = 0;    
                        }
                        if (isset($_POST["generadoc"])) {
                            $indicadorResBol = 2;
                            $idCliente = $_POST["cliente"];
                            $datosCliente = $dirCliente = "";
                            $numDocCliente = "";
                            if (isset($_POST["numdoc"])) {
                                $numDocCliente = trim($_POST["numdoc"]);
                            }
                            $objTipoDocumento = NULL;
                            if ($_POST["generadoc"] == 1) {
                                $objTipoDocumento = ParametroData::getById($_POST["tipodoc"]);
                                if ($objTipoDocumento->valor2 == 3) {
                                    // Boleta
                                    $indicadorResBol = 1;
                                } else if ($objTipoDocumento->valor2 == 1) {
                                    // Factura
                                    $indicadorResBol = 0;
                                }                                
                            } else if ($_POST["generadoc"] == 0) {
                                $lstTipoDocumento = ParametroData::getAll(1, "TIPO DOCUMENTO", "TICKET");
                                if (count($lstTipoDocumento) > 0) {
                                    $objTipoDocumento = $lstTipoDocumento[0];
                                }
                            }
                            if ($numDocCliente == "") {
                                $numDocCliente = "10000000";
                                $datosCliente = "CLIENTE GENERAL";
                            } else {
                                $tipoDocumentoCliente = "";
                                if ($_POST["generadoc"] == 1) {
                                    if ($objTipoDocumento->valor2 == 3) {
                                        // Boleta
                                        $datosCliente = strtoupper(trim($_POST["nomape"]));
                                        $indicadorResBol = 1;
                                        $lstTipoDocumentoCliente = ParametroData::getAll(1, "TIPO DOCUMENTO CLIENTE", "DNI");
                                        $tipoDocumentoCliente = $lstTipoDocumentoCliente[0]->id;
                                    } else if ($objTipoDocumento->valor2 == 1) {
                                        // Factura
                                        $datosCliente = strtoupper(trim($_POST["razon"]));
                                        $dirCliente = strtoupper(trim($_POST["direccion"]));
                                        $indicadorResBol = 0;
                                        $lstTipoDocumentoCliente = ParametroData::getAll(1, "TIPO DOCUMENTO CLIENTE", "RUC");
                                        $tipoDocumentoCliente = $lstTipoDocumentoCliente[0]->id;
                                    }
                                    if ($_POST["cliente"] == 0) {
                                        // Registrar Cliente
                                        $objCliente = new ClienteData();
                                        $objCliente->tipo_documento = $tipoDocumentoCliente;
                                        $objCliente->num_documento = $numDocCliente;
                                        $objCliente->datos = $datosCliente;
                                        $objCliente->direccion = $dirCliente;
                                        $objCliente->estado = 1;
                                        $resultado = $objCliente->add();
                                        if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                            file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla clientes\n", FILE_APPEND);
                                            $error = 1;
                                        } else {
                                            $idCliente = $resultado[1];
                                            file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla clientes : " . $idCliente . "\n", FILE_APPEND);
                                        }
                                    } else {
                                        file_put_contents("info" . date("Ymd") . ".log", "Existe cliente en tabla clientes : " . $idCliente . "\n", FILE_APPEND);
                                    }
                                } else if ($_POST["generadoc"] == 0) {
                                    $indicadorResBol = 2;
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
                                            $objComprobante->fe_comprobante_totvengra = round(($objPago->monto_total - $objPago->monto_descuento) / 1.18, 2);
                                            $objComprobante->fe_comprobante_totvenina = 0.00;
                                            $objComprobante->fe_comprobante_totvenexo = 0.00;
                                            $objComprobante->fe_comprobante_totvengratui = 0.00;
                                            $objComprobante->fe_comprobante_totdes = 0.00;
                                            $objComprobante->fe_comprobante_sumigv = round(($objComprobante->fe_comprobante_totvengra * 0.18), 2);
                                        } else {
                                            $objComprobante->fe_comprobante_totvengra = 0.00;
                                            $objComprobante->fe_comprobante_totvenina = 0.00;
                                            $objComprobante->fe_comprobante_totvenexo = round(($objPago->monto_total - $objPago->monto_descuento), 2);
                                            $objComprobante->fe_comprobante_totvengratui = 0.00;
                                            $objComprobante->fe_comprobante_totdes = 0.00;
                                            $objComprobante->fe_comprobante_sumigv = 0.00;
                                        }
                                        $objComprobante->fe_comprobante_sumisc = 0.00;
                                        $objComprobante->fe_comprobante_sumotrtri = 0.00;
                                        $objComprobante->fe_comprobante_desglo = 0.00;
                                        $objComprobante->fe_comprobante_sumotrcar = 0.00;
                                        $objComprobante->fe_comprobante_imptot = $objPago->monto_total - $objPago->monto_descuento;
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
                                        $objComprobante->historial_pago = $idHistorialPago;
                                        $resultado = $objComprobante->add();
                                        if (isset($resultado[1]) && $resultado[1] > 0) {
                                            $idComprobante = $resultado[1];
                                            
                                            file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla comprobantes : " . $idComprobante . "\n", FILE_APPEND);
                                            
                                            $objHistorialPago = HistorialPagoData::getById($idHistorialPago);
                                            $objHistorialPago->comprobante = $idComprobante;
                                            $objHistorialPago->indicador_cierre = 0;
                                            $resultado = $objHistorialPago->update();
                                            if (isset($resultado[0]) && $resultado[0] == 1) {
                                                file_put_contents("info" . date("Ymd") . ".log", "Actualizacion en tabla historial_pagos : " . $idHistorialPago . "\n", FILE_APPEND);
                                                
                                                $objHistorialDocumento = new HistorialDocumentoData();
                                                $objHistorialDocumento->historial_pago = $idHistorialPago;
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
                                                        $objDetalleComprobante->fe_comprobantedetalle_nro = $item++;
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
                                                        if (!(isset($resultado[0]) && $resultado[0] == 1)) {
                                                            $error = 1;
                                                            file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla detalle_comprobantes\n", FILE_APPEND);
                                                            mysqli_rollback(Database::getCon());
                                                            echo 0;
                                                            break;
                                                        }
                                                        file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla detalle_comprobantes " . $resultado[1] . "\n", FILE_APPEND);
                                                    }
                                                    if ($error == 0) {
                                                        file_put_contents("info" . date("Ymd") . ".log", "Se genera comprobante\n", FILE_APPEND);
                                                        mysqli_commit(Database::getCon());
                                                        echo 1;
                                                    }
                                                } else {
                                                    file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla historial_documentos\n", FILE_APPEND);
                                                    mysqli_rollback(Database::getCon());
                                                    echo 0;
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
                                        file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla correlativos " . $objCorrelativo->id . "\n", FILE_APPEND);
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
                        }                        
                    } else {
                        file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla pedidos " . $objPedido->id . "\n", FILE_APPEND);
                        mysqli_rollback(Database::getCon());                        
                        echo 0;
                    }
                } else {
                    mysqli_rollback(Database::getCon());
                    echo 0;
                }
            } else {
                file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla pagos\n", FILE_APPEND);                
                mysqli_rollback(Database::getCon());
                echo 0;                
            }
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
        } else if ($_POST["accion"] == 4) {
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "OPCION: REGISTRAR PAGO PARCIAL \n", FILE_APPEND);
            
            // Pago por partes
            $idPago = $idHistorialPago = $total = $error = $esCredito = 0;
            $indicadorPago = false;
            if ($_POST["idpago"] == 0) {
                // Primer pago
                $objPago = new PagoData();
                $objPago->pedido = $_POST["id"];
                $objPago->tipo_pago = $_POST["tipopago"];
                $objPago->porcentaje_descuento = 0.00;
                $objPago->monto_descuento = 0.00;
                $objPago->monto_total = str_replace(",", "", trim($_POST["montototal"]));
                $objParametro = ParametroData::getById($_POST["formapago"]);
                if ($objParametro) {
                    if ($objParametro->valor1 == 0) {
                        // Efectivo
                        $objPago->monto_pagado_efectivo = str_replace(",", "", trim($_POST["montopagado"]));
                        $objPago->monto_pagado_tarjeta = 0.00;
                        $objPago->monto_credito = 0.00;                        
                    } else if ($objParametro->valor1 == 1) {
                        // Tarjeta
                        $objPago->monto_pagado_efectivo = 0.00;
                        $objPago->monto_pagado_tarjeta = str_replace(",", "", trim($_POST["montopagado"]));
                        $objPago->monto_credito = 0.00;
                    } else if ($objParametro->valor1 == 2) {
                        // Mixta
                        $montoEfectivo = str_replace(",", "", trim($_POST["montoefectivo"]));
                        $montoTarjeta = str_replace(",", "", trim($_POST["montotarjeta"]));
                        
                        if ($montoEfectivo == 0 || $montoTarjeta == 0) {
                            echo -1;
                            return;
                        }
                        $objPago->monto_pagado_efectivo = $montoEfectivo;
                        $objPago->monto_pagado_tarjeta = $montoTarjeta;
                        $objPago->monto_credito = 0.00;
                    } else if ($objParametro->valor1 == 3) {
                        // Credito
                        $objPago->monto_pagado_efectivo = 0.00;
                        $objPago->monto_pagado_tarjeta = 0.00;
                        $objPago->monto_credito = str_replace(",", "", trim($_POST["montopagado"]));
                        $esCredito = 1;
                    }
                    if (round($objPago->monto_total, 2) <= round(($objPago->monto_pagado_efectivo + $objPago->monto_pagado_tarjeta + $objPago->monto_credito), 2)) {
                        $indicadorPago = true;
                    }
                }
                $objPago->estado = 1;
                $objPago->fecha_creacion = date("Y-m-d H:i:s");
                $objPago->usuario_creacion = $_SESSION["user"];
                $resultado = $objPago->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    $idPago = $resultado[1];
                    file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla pagos : " . $idPago . "\n", FILE_APPEND);
                } else {
                    $error = 1;
                    file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla pagos\n", FILE_APPEND);
                }
            } else {
                // Pagos realizados despues del primer pago
                $idPago = $_POST["idpago"];
                $objPago = PagoData::getById($idPago);
                $objParametro = ParametroData::getById($_POST["formapago"]);
                if ($objParametro) {
                    if ($objParametro->valor1 == 0) {
                        // Efectivo
                        $objPago->monto_pagado_efectivo = $objPago->monto_pagado_efectivo + str_replace(",", "", trim($_POST["montopagado"]));                        
                    } else if ($objParametro->valor1 == 1) {
                        // Tarjeta
                        $objPago->monto_pagado_tarjeta = $objPago->monto_pagado_tarjeta + str_replace(",", "", trim($_POST["montopagado"]));                        
                    } else if ($objParametro->valor1 == 2) {
                        // Mixta
                        $montoEfectivo = str_replace(",", "", trim($_POST["montoefectivo"]));
                        $montoTarjeta = str_replace(",", "", trim($_POST["montotarjeta"]));
                        
                        if ($montoEfectivo == 0 || $montoTarjeta == 0) {
                            echo -1;
                            return;
                        }
                        $objPago->monto_pagado_efectivo = $objPago->monto_pagado_efectivo + $montoEfectivo;
                        $objPago->monto_pagado_tarjeta = $objPago->monto_pagado_tarjeta + $montoTarjeta;                        
                    } else if ($objParametro->valor1 == 3) {
                        // Credito
                        $objPago->monto_credito = str_replace(",", "", trim($_POST["montopagado"]));
                        $esCredito = 1;
                    }
                    if (round($objPago->monto_total, 2) <= round(($objPago->monto_pagado_efectivo + $objPago->monto_pagado_tarjeta + $objPago->monto_credito), 2)) {
                        $indicadorPago = true;
                    }
                }
                $resultado = $objPago->update();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla pagos : " . $idPago . "\n", FILE_APPEND);
                } else {
                    $error = 1;
                    file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla pagos : " . $idPago . "\n", FILE_APPEND);
                }
            }
            if ($error == 0) {
                // Registrar el historial del pago
                $objHistorialPago = new HistorialPagoData();
                $objHistorialPago->pago = $idPago;
                $objHistorialPago->forma_pago = $_POST["formapago"];
                $objParametro = ParametroData::getById($_POST["formapago"]);
                if ($objParametro && $objParametro->valor1 == 1 || $objParametro && $objParametro->valor1 == 2) {
                    // Tarjeta y Mixta
                    $objHistorialPago->tipo_tarjeta = $_POST["tipotarjeta"];
                    $objHistorialPago->num_operacion = $_POST["numope"];
                    
                    $objHistorialPago->monto_efectivo = 0.00;
                    if ($objParametro && $objParametro->valor1 == 2) {
                        $objHistorialPago->monto_tarjeta = str_replace(",", "", trim($_POST["montotarjeta"]));
                        $objHistorialPago->monto_efectivo = str_replace(",", "", trim($_POST["montoefectivo"]));
                    } else {
                        $objHistorialPago->monto_tarjeta = str_replace(",", "", trim($_POST["montopagado"]));
                    }
                    $objHistorialPago->monto_credito = 0.00;
                } else if ($objParametro && $objParametro->valor1 == 3) {
                    // Credito
                    $objHistorialPago->num_documento = trim($_POST["numdocumento"]);
                    $objHistorialPago->cliente = strtoupper(trim($_POST["nomcliente"]));
                    $objHistorialPago->monto_efectivo = 0.00;
                    $objHistorialPago->monto_tarjeta = 0.00;
                    $objHistorialPago->monto_credito = str_replace(",", "", trim($_POST["montopagado"]));
                } else {
                    // Efectivo
                    $objHistorialPago->monto_efectivo = str_replace(",", "", trim($_POST["montopagado"]));
                    $objHistorialPago->monto_tarjeta = 0.00;
                    $objHistorialPago->monto_credito = 0.00;
                    $objHistorialPago->tipo_tarjeta = 0;
                }
                $objHistorialPago->fecha = date("Y-m-d H:i:s");
                $objHistorialPago->usuario = $_POST["usuario"];
                $objHistorialPago->caja = $_POST["caja"];
                $objHistorialPago->estado = 1;
                $resultado = $objHistorialPago->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    $idHistorialPago = $resultado[1];
                    
                    file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla historial_pagos : " . $idHistorialPago. "\n", FILE_APPEND);
                    
                    // Actualizar el detalle del pedido (cantidad pagada)
                    $lstDetallePedido = DetallePedidoData::getProductosXPedido($objPago->pedido);
                    foreach ($lstDetallePedido as $objDetallePedido) {
                        $texto = "cantapagar" . $objDetallePedido->id;
                        $check = "check" . $objDetallePedido->id;
                        if (isset($_POST[$texto]) && $_POST[$texto] != "" && isset($_POST[$check]) && $_POST[$check] == "on") {
                            $objDetallePedido->cantidad_pagada = $objDetallePedido->cantidad_pagada + $_POST[$texto];
                            if ($objDetallePedido->fecha_comanda == "") {
                                $objDetallePedido->fecha_comanda = "0000-00-00 00:00:00";
                            }
                            $resultado = $objDetallePedido->update();
                            if (isset($resultado[0]) && $resultado[0] == 1) {
                                file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla detalle_pedidos : " . $objDetallePedido->id . "\n", FILE_APPEND);
                                
                                $objDetalleHistorialPago = new DetalleHistorialPagoData();
                                $objDetalleHistorialPago->historial_pago = $idHistorialPago;
                                $objDetalleHistorialPago->nom_categoria = $objDetallePedido->categoria;
                                $objDetalleHistorialPago->producto = $objDetallePedido->producto;
                                $objDetalleHistorialPago->nom_producto = $objDetallePedido->nom_producto;
                                $objDetalleHistorialPago->cantidad = $_POST[$texto];
                                $objDetalleHistorialPago->precio = $objDetallePedido->precio_venta;
                                $objDetalleHistorialPago->total = $objDetalleHistorialPago->cantidad * $objDetalleHistorialPago->precio;
                                $objDetalleHistorialPago->estado = 1;
                                $resultado = $objDetalleHistorialPago->add();
                                if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                    file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla detalle_historial_pagos\n", FILE_APPEND);
                                    $error = 1;
                                    break;
                                }                                
                                $total += $objDetalleHistorialPago->total;
                                
                                file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla detalle_historial_pagos " . $resultado[1] . "\n", FILE_APPEND);
                            } else {
                                file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla detalle_pedidos : " . $objDetallePedido->id . "\n", FILE_APPEND);
                                $error = 1;
                                break;
                            }
                        }
                    }
                } else {
                    $error = 1;
                }
                if ($error == 0) {
                    // En el caso que se cancele todos los productos se actualiza el pedido
                    if ($indicadorPago) {
                        $objPedido = PedidoData::getById($objPago->pedido);                        
                        $objPedido->estado = 2;
                        $objPedido->fecha_actualizacion = date("Y-m-d H:i:s");
                        $objPedido->usuario_actualizacion = $_SESSION["user"];
                        $resultado = $objPedido->update();
                        if (!(isset($resultado[0]) && $resultado[0] == 1)) {
                            $error = 1;
                            file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla pedidos : " . $objPago->pedido . "\n", FILE_APPEND);
                        } else {
                            file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla pedidos : " . $objPago->pedido . "\n", FILE_APPEND);
                        }
                    }
                        
                    // Genera Boleta o Factura
                    if ($error == 0) {
                        if (!isset($_POST["generadoc"])) {
                            $_POST["generadoc"] = 0;
                        }
                        if (isset($_POST["generadoc"])) {
                            $indicadorResBol = 2;
                            $idCliente = $_POST["cliente"];
                            $datosCliente = $dirCliente = $numDocCliente = "";
                            if (isset($_POST["numdoc"])) {
                                $numDocCliente = trim($_POST["numdoc"]);
                            }
                            $objTipoDocumento = NULL;
                            if ($_POST["generadoc"] == 1) {
                                $objTipoDocumento = ParametroData::getById($_POST["tipodoc"]);
                                if ($objTipoDocumento->valor2 == 3) {
                                    // Boleta
                                    $indicadorResBol = 1;
                                } else if ($objTipoDocumento->valor2 == 1) {
                                    // Factura
                                    $indicadorResBol = 0;
                                }
                            } else {
                                $lstTipoDocumento = ParametroData::getAll(1, "TIPO DOCUMENTO", "TICKET");
                                if (count($lstTipoDocumento) > 0) {
                                    $objTipoDocumento = $lstTipoDocumento[0];
                                }
                            }
                            if ($numDocCliente == "") {
                                $numDocCliente = "10000000";
                                $datosCliente = "CLIENTE GENERAL";
                            } else {
                                if ($_POST["generadoc"] == 1) {
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
                                    if ($_POST["cliente"] == 0) {
                                        // Registrar Cliente
                                        $objCliente = new ClienteData();
                                        $objCliente->tipo_documento = $tipoDocumentoCliente;
                                        $objCliente->num_documento = $numDocCliente;
                                        $objCliente->datos = $datosCliente;
                                        $objCliente->direccion = $dirCliente;
                                        $objCliente->estado = 1;
                                        $resultado = $objCliente->add();
                                        if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                            file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla clientes\n", FILE_APPEND);
                                            $error = 1;
                                        } else {
                                            $idCliente = $resultado[1];
                                            file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla clientes : " . $idCliente . "\n", FILE_APPEND);
                                        }
                                    } else {
                                        file_put_contents("info" . date("Ymd") . ".log", "Existe cliente en tabla clientes : " . $idCliente . "\n", FILE_APPEND);
                                    }
                                } else if ($_POST["generadoc"] == 0) {
                                    $indicadorResBol = 2;
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
                                            $objComprobante->fe_comprobante_totvengra = round($total / 1.18, 2);
                                            $objComprobante->fe_comprobante_totvenina = 0.00;
                                            $objComprobante->fe_comprobante_totvenexo = 0.00;
                                            $objComprobante->fe_comprobante_totvengratui = 0.00;
                                            $objComprobante->fe_comprobante_totdes = 0.00;
                                            $objComprobante->fe_comprobante_sumigv = round(($objComprobante->fe_comprobante_totvengra * 0.18), 2);
                                        } else {
                                            $objComprobante->fe_comprobante_totvengra = 0.00;
                                            $objComprobante->fe_comprobante_totvenina = 0.00;
                                            $objComprobante->fe_comprobante_totvenexo = round($total, 2);
                                            $objComprobante->fe_comprobante_totvengratui = 0.00;
                                            $objComprobante->fe_comprobante_totdes = 0.00;
                                            $objComprobante->fe_comprobante_sumigv = 0.00;
                                        }
                                        $objComprobante->fe_comprobante_sumisc = 0.00;
                                        $objComprobante->fe_comprobante_sumotrtri = 0.00;
                                        $objComprobante->fe_comprobante_desglo = 0.00;
                                        $objComprobante->fe_comprobante_sumotrcar = 0.00;
                                        $objComprobante->fe_comprobante_imptot = $total;
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
                                        $objComprobante->historial_pago = $idHistorialPago;
                                        $resultado = $objComprobante->add();
                                        if (isset($resultado[1]) && $resultado[1] > 0) {
                                            $idComprobante = $resultado[1];
                                            
                                            file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla comprobantes : " . $idComprobante . "\n", FILE_APPEND);
                                            
                                            $objHistorialPago = HistorialPagoData::getById($idHistorialPago);
                                            $objHistorialPago->comprobante = $idComprobante;
                                            $objHistorialPago->indicador_cierre = 0;
                                            $resultado = $objHistorialPago->update();
                                            if (isset($resultado[0]) && $resultado[0] == 1) {
                                                file_put_contents("info" . date("Ymd") . ".log", "Actualizacion en tabla historial_pagos : " . $idHistorialPago . "\n", FILE_APPEND);
                                                                                            
                                                $objHistorialDocumento = new HistorialDocumentoData();
                                                $objHistorialDocumento->historial_pago = $idHistorialPago;
                                                $objHistorialDocumento->comprobante = $idComprobante;
                                                $objHistorialDocumento->estado = 1;
                                                $resultado = $objHistorialDocumento->add();
                                                if (isset($resultado[1]) && $resultado[1] > 0) {
                                                    file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla historial_documentos : " . $resultado[1] . "\n", FILE_APPEND);
                                                    
                                                    $lstDetalle = DetallePedidoData::getProductosXPedido($objPago->pedido);
                                                    
                                                    $item = 1;                          
                                                    foreach ($lstDetalle as $objDetalle) {
                                                        $texto = "cantapagar" . $objDetalle->id;
                                                        $check = "check" . $objDetalle->id;
                                                        if (isset($_POST[$texto]) && $_POST[$texto] != "" && isset($_POST[$check]) && $_POST[$check] == "on") {
                                                            $total = $objDetalle->precio_venta * $_POST[$texto];
                                                            
                                                            $objDetalleComprobante = new DetalleComprobanteData();
                                                            $objDetalleComprobante->fe_comprobantedetalle_nro = $item++;
                                                            $objDetalleComprobante->fe_comprobantedetalle_cod = $objDetalle->id;
                                                            $objDetalleComprobante->fe_comprobantedetalle_nom = $objDetalle->nom_producto;
                                                            $objDetalleComprobante->cs_tipoafectacionigv_cod = "10";
                                                            $objDetalleComprobante->cs_tipounidadmedida_cod = "NIU";
                                                            $objDetalleComprobante->fe_comprobantedetalle_can = $_POST[$texto];
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
                                                            file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla detalle_comprobantes : " . $resultado[1] . "\n", FILE_APPEND);
                                                        }
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
                                        file_put_contents("info" . date("Ymd") . ".log", "Error actualización tabla correlativos " . $objCorrelativo->id . "\n", FILE_APPEND);
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
                        }
                    } else {
                        mysqli_rollback(Database::getCon());
                        echo 0;
                    }
                } else {
                    file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla historial_pagos\n", FILE_APPEND);
                    mysqli_rollback(Database::getCon());
                    echo 0;
                }
            } else {                
                mysqli_rollback(Database::getCon());
                echo 0;
            }
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
        } else if ($_POST["accion"] == 5) {
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            
            $objHistorialPago = HistorialPagoData::getById($_POST["id"]);
            $objHistorialPago->estado = 0;
            $resultado = $objHistorialPago->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla historial_pagos : " . $objHistorialPago->id . "\n", FILE_APPEND);
                
                $ok = $error = 0;
                $arrDetalle = explode("<br>", $objHistorialPago->detalle);
                for ($indice = 0; $indice < count($arrDetalle); $indice ++) {
                    $arrProducto = explode("-", $arrDetalle[$indice]);
                    $lstDetallePedido = DetallePedidoData::getProductosXPedido($objHistorialPago->getPago()->getPedido()->id, $arrProducto[0]);
                    if (count($lstDetallePedido) > 0) {
                        $lstDetallePedido[0]->cantidad_pagada = $lstDetallePedido[0]->cantidad_pagada - $arrProducto[1];
                        $resultado = $lstDetallePedido[0]->update();
                        if (isset($resultado[0]) && $resultado[0] == 1) {
                            file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla detalle_pedidos : " . $objDetallePedido->id . "\n", FILE_APPEND);
                            $ok++;
                        } else {
                            file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla detalle_pedidos : " . $objDetallePedido->id . "\n", FILE_APPEND);
                            $error++;
                        }
                    }
                }
                if ($error == 0) {
                    $montoPagadoTarjeta = $montoPagadoEfectivo = 0;
                    if ($objHistorialPago->tipo_tarjeta > 0) {
                        $montoPagadoTarjeta = $objHistorialPago->monto_pagado;
                    } else {
                        $montoPagadoEfectivo = $objHistorialPago->monto_pagado;
                    }
                    $objPago = PagoData::getById($objHistorialPago->pago);
                    $objPago->monto_pagado_efectivo = $objPago->monto_pagado_efectivo - $montoPagadoEfectivo;
                    $objPago->monto_pagado_tarjeta = $objPago->monto_pagado_tarjeta - $montoPagadoTarjeta;
                    $resultado = $objPago->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla pagos : " . $objHistorialPago->getPago()->id . "\n", FILE_APPEND);
                        
                        $objPedido = PedidoData::getById($objHistorialPago->getPago()->getPedido()->id);
                        $objPedido->estado = 1;
                        $objPedido->fecha_actualizacion = date("Y-m-d H:i:s");
                        $objPedido->usuario_actualizacion = $_SESSION["user"];
                        $resultado = $objPedido->delete();
                        if (isset($resultado[0]) && $resultado[0] == 1) {
                            file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
                            mysqli_commit(Database::getCon());
                            echo $resultado[0];
                        } else {
                            file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
                            mysqli_rollback(Database::getCon());
                            echo 0;
                        }
                    } else {
                        file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla pagos : " . $objHistorialPago->pago . "\n", FILE_APPEND);
                        mysqli_rollback(Database::getCon());
                        echo 0;
                    }
                } else {
                    mysqli_rollback(Database::getCon());
                    echo 0;
                }
            } else {
                file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla historial_pagos : " . $objHistorialPago->id . "\n", FILE_APPEND);
                mysqli_rollback(Database::getCon());
                echo 0;
            }
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
        } else if ($_POST["accion"] == 6) {
            // Anular un producto de un pedido
            $objDetallePedido = DetallePedidoData::getById($_POST["id"]);
            $resultado = $objDetallePedido->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>