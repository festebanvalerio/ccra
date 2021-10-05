<?php
    if (isset($_POST["indicador"])) {
        if ($_POST["indicador"] == 1) {
            if (isset($_POST["idPedido"]) && $_POST["idPedido"] > 0) {
                file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);                
                
                mysqli_begin_transaction(Database::getCon());
                $error = 0;
                $objPedido = PedidoData::getById($_POST["idPedido"]);
                $objProducto = ProductoData::getById($_POST["producto"]);
                $objPiso = $objPedido->getPiso();                
                $precioVenta = $objProducto->precio1;
                if ($objPiso->indicador == 1) {
                    $precioVenta = $objProducto->precio2;
                }                
                $descuento = 0;
                $precioReal = $precioVenta;
                $objDescuentoProgramado = DescuentoProgramadoData::getDescuentoXProducto($objPedido->sede, $objProducto->id, date("Y-m-d"));
                if ($objDescuentoProgramado) {
                    // En caso tenga un descuento programado
                    $precioVenta = $objDescuentoProgramado->precio_descuento;
                    $precioReal = $objDescuentoProgramado->precio_actual;
                    $descuento = ($precioReal - $precioVenta) * $_POST["cantidad"];
                }
                $total = $precioVenta * $_POST["cantidad"];
                $subtotal = $igv = 0;
                if ($_SESSION["exonerado"] == 0) {
                    $subtotal = $total / 1.18;
                    $igv = $subtotal * 0.18;
                } else if ($_SESSION["exonerado"] == 1) {
                    $subtotal = $total;
                }                
                $objDetallePedido = new DetallePedidoData();
                $objDetallePedido->pedido = $_POST["idPedido"];
                $objDetallePedido->producto = $objProducto->id;
                $objDetallePedido->nom_producto = $objProducto->nombre;
                $objDetallePedido->tipo = $objProducto->getTipo()->nombre;
                $objDetallePedido->categoria = $objProducto->getCategoria()->nombre;
                $objDetallePedido->comentario = trim($_POST["comentario"]);
                $objDetallePedido->cantidad = $_POST["cantidad"];
                $objDetallePedido->precio_costo = $objProducto->costo;                
                $objDetallePedido->precio_venta = $precioVenta;
                $objDetallePedido->precio_real = $precioReal;
                $objDetallePedido->subtotal = $subtotal;
                $objDetallePedido->igv = $igv;
                $objDetallePedido->total = $total;
                $objDetallePedido->estado = 1;
                $objDetallePedido->fecha_actualizacion = date("Y-m-d H:i:s");
                $objDetallePedido->usuario_actualizacion = $_SESSION["user"];
                $objDetallePedido->fecha_creacion = date("Y-m-d H:i:s");
                $objDetallePedido->usuario_creacion = $_SESSION["user"];
                $resultado = $objDetallePedido->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {                    
                    file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla detalle_pedidos : " . $resultado[1] . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Detalle Pedido - Cantidad : " . $objDetallePedido->cantidad . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Detalle Pedido - Total : " . $objDetallePedido->total . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Pedido - Total : " . $objPedido->total . "\n", FILE_APPEND);
                    
                    $objPedido = PedidoData::getById($_POST["idPedido"]);
                    $objPedido->descuento_pedido = $objPedido->descuento_pedido;
                    $objPedido->descuento_programado = $objPedido->descuento_programado + $descuento;
                    $objPedido->subtotal = $objPedido->subtotal + $subtotal;
                    $objPedido->igv = $objPedido->igv + $igv;
                    $objPedido->total = $objPedido->total + $total;
                    $resultado = $objPedido->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
                        file_put_contents("info" . date("Ymd") . ".log", "Pedido - Total : " . $objPedido->total . "\n", FILE_APPEND);
                        
                        // Obtener los insumos asociados a cada producto
                        $lstInsumoXProducto = DetalleRecetaData::getAllInsumosByProducto($objProducto->id);
                        if (count($lstInsumoXProducto) > 0) {
                            $lstAlmacen = AlmacenData::getAll(1, $objPedido->getSede()->empresa, $objPedido->sede);
                            if (count($lstAlmacen) > 0) {
                                $objAlmacen = $lstAlmacen[0];
                                foreach ($lstInsumoXProducto as $objInsumoXProducto) {
                                    $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objInsumoXProducto->insumo, $objAlmacen->id);
                                    if (count($lstInsumoAlmacen) > 0) {
                                        $objInsumoAlmacen = $lstInsumoAlmacen[0];
                                        $objInsumoAlmacen->stock = $objInsumoAlmacen->stock - ($objInsumoXProducto->cantidad * $_POST["cantidad"]);
                                        $resultado = $objInsumoAlmacen->updateStock();
                                        if (!(isset($resultado[0]) && $resultado[0] == 1)) {
                                            file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla insumos_almacen\n", FILE_APPEND);
                                            $error = 1;
                                            break;
                                        }
                                        file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla insumos_almacen : (Insumo : " . $objInsumoXProducto->insumo . " - Almacen : " . $objAlmacen->id . ")\n", FILE_APPEND);
                                        
                                        $objMovimiento = new MovimientoData();
                                        $objMovimiento->sede = $objPedido->sede;
                                        $objMovimiento->insumo = $objInsumoXProducto->insumo;
                                        $objMovimiento->tipo = 0;
                                        $objMovimiento->cantidad = $objInsumoXProducto->cantidad * $_POST["cantidad"];
                                        $objMovimiento->detalle = "PRODUCTO REGISTRADO - PEDIDO ".str_pad($objPedido->id, 8, "0", STR_PAD_LEFT);
                                        $objMovimiento->modulo = "1";
                                        $objMovimiento->fecha = date("Y-m-d H:i:s");
                                        $objMovimiento->estado = 1;
                                        $resultado = $objMovimiento->add();
                                        if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                            file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla movimientos\n", FILE_APPEND);
                                            $error = 1;
                                            break;
                                        }
                                        file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla movimientos : " . $resultado[1] . "\n", FILE_APPEND);
                                    }
                                }
                            }
                        }
                        if ($error == 0) {
                            mysqli_commit(Database::getCon());
                            echo $resultado[0];
                        } else {
                            mysqli_rollback(Database::getCon());
                            echo 0;
                        }                        
                    } else {
                        file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
                        mysqli_rollback(Database::getCon());
                        echo 0;
                    }
                } else {                    
                    file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla detalle_pedidos\n", FILE_APPEND);
                    mysqli_rollback(Database::getCon());
                    echo 0;
                }
                file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
                return;
            } else {
                $comentario = trim($_POST["comentario"]);
                if (!isset($_SESSION["productos"])) {
                    $_SESSION["productos"] = array();
                }
                $_SESSION["productos"][] = $_POST["producto"]."|".$_POST["cantidad"]."|".$comentario;
            }
        }
        if ($_POST["indicador"] == 2) {
            $item = 1;
            $_SESSION["tmp_productos"] = array();
                
            foreach ($_SESSION["productos"] as $valor) {
                $data = explode("|", $valor);
                if ($item != $_POST["item"]) {
                    $_SESSION["tmp_productos"][] = $data[0]."|".$data[1]."|".$data[2];
                }
                $item++;
            }
            if (count($_SESSION["tmp_productos"]) > 0) {
                $_SESSION["productos"] = array();
                $_SESSION["productos"] = $_SESSION["tmp_productos"];
            } else {
                $_SESSION["productos"] = array();
            }
        }
        if ($_POST["indicador"] == 3) {
            $item = 1;
            $_SESSION["tmp_productos"] = array();
            
            $cantidad = trim($_POST["cantidad"]);
            $comentario = strtoupper(trim($_POST["comentario"]));
            foreach ($_SESSION["productos"] as $valor) {
                $data = explode("|", $valor);
                if ($item != $_POST["item"]) {
                    $_SESSION["tmp_productos"][] = $data[0]."|".$data[1]."|".$data[2];
                } else {
                    $_SESSION["tmp_productos"][] = $data[0]."|".$cantidad."|".$comentario;
                }
                $item++;
            }
            if (count($_SESSION["tmp_productos"]) > 0) {
                $_SESSION["productos"] = array();
                $_SESSION["productos"] = $_SESSION["tmp_productos"];
            } else {
                $_SESSION["productos"] = array();
            }            
        }
        if ($_POST["indicador"] == 4) {
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "OPCION: ANULAR PRODUCTO\n", FILE_APPEND);
            
            mysqli_begin_transaction(Database::getCon());
            $error = 0;
            $objDetallePedido = DetallePedidoData::getById($_POST["id"]);
            $objDetallePedido->fecha_actualizacion = date("Y-m-d H:i:s");
            $objDetallePedido->usuario_actualizacion = $_SESSION["user"];
            $objDetallePedido->estado = 0;
            $resultado = $objDetallePedido->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                file_put_contents("info" . date("Ymd") . ".log", "Anulación en tabla detalle_pedidos : " . $objDetallePedido->id . "\n", FILE_APPEND);
                file_put_contents("info" . date("Ymd") . ".log", "Detalle Pedido - Cantidad : " . $objDetallePedido->cantidad . "\n", FILE_APPEND);
                file_put_contents("info" . date("Ymd") . ".log", "Detalle Pedido - Total : " . $objDetallePedido->total . "\n", FILE_APPEND);
                
                $objPago = PagoData::getByPedido($objDetallePedido->pedido);
                if ($objPago) {
                    $objPago->monto_total = round($objPago->monto_total - $objDetallePedido->total, 2);
                    $resultado = $objPago->update();
                    if (!(isset($resultado[0]) && $resultado[0] == 1)) {
                        file_put_contents("info" . date("Ymd") . ".log", "Error actualizacion en tabla pagos : " . $objPago->id . "\n", FILE_APPEND);
                        $error = 1;
                    } else {
                        file_put_contents("info" . date("Ymd") . ".log", "Actualizacion en tabla pedidos : " . $objPago->id . "\n", FILE_APPEND);
                        if (round($objPago->monto_total, 2) <= round(($objPago->monto_pagado_efectivo + $objPago->monto_pagado_tarjeta), 2)) {
                            $objPedido = PedidoData::getById($objPago->pedido);
                            $objPedido->estado = 2;
                            $objPedido->fecha_actualizacion = date("Y-m-d H:i:s");
                            $objPedido->usuario_actualizacion = $_SESSION["user"];
                            $resultado = $objPedido->update();
                            if (!(isset($resultado[0]) && $resultado[0] == 1)) {
                                file_put_contents("info" . date("Ymd") . ".log", "Error actualizacion en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
                                $error = 1;
                            }
                            file_put_contents("info" . date("Ymd") . ".log", "Actualizacion en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
                        }
                    }
                }                
                if ($error == 0) {
                    $objPedido = PedidoData::getById($objDetallePedido->pedido);
                    $objPedido->subtotal = round($objPedido->subtotal - $objDetallePedido->subtotal, 2);
                    $objPedido->igv = round($objPedido->igv - $objDetallePedido->igv, 2);
                    $objPedido->total = round($objPedido->total - $objDetallePedido->total, 2);
                    $resultado = $objPedido->update();                    
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla pedidos : " . $objDetallePedido->pedido . "\n", FILE_APPEND);
                        file_put_contents("info" . date("Ymd") . ".log", "Pedido - Total : " . $objPedido->total . "\n", FILE_APPEND);
                        
                        // Obtener los insumos asociados a cada producto
                        $lstInsumoXProducto = DetalleRecetaData::getAllInsumosByProducto($objDetallePedido->producto);
                        if (count($lstInsumoXProducto) > 0) {
                            $lstAlmacen = AlmacenData::getAll(1, $objPedido->getSede()->empresa, $objPedido->sede);
                            if (count($lstAlmacen) > 0) {
                                $objAlmacen = $lstAlmacen[0];                            
                                foreach ($lstInsumoXProducto as $objInsumoXProducto) {
                                    $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objInsumoXProducto->insumo, $objAlmacen->id);
                                    if (count($lstInsumoAlmacen) > 0) {
                                        $objInsumoAlmacen = $lstInsumoAlmacen[0];
                                        $objInsumoAlmacen->stock = $objInsumoAlmacen->stock + ($objInsumoXProducto->cantidad * $objDetallePedido->cantidad);
                                        $resultado = $objInsumoAlmacen->updateStock();
                                        if (!(isset($resultado[0]) && $resultado[0] == 1)) {
                                            file_put_contents("info" . date("Ymd") . ".log", "Error actualizacion en tabla insumos_almacen\n", FILE_APPEND);
                                            $error = 1;
                                            break;
                                        }
                                        file_put_contents("info" . date("Ymd") . ".log", "Actualizacion en tabla insumos_almacen : " . $resultado[0] . "\n", FILE_APPEND);
                                        
                                        $objMovimiento = new MovimientoData();
                                        $objMovimiento->sede = $objPedido->sede;
                                        $objMovimiento->insumo = $objInsumoXProducto->insumo;
                                        $objMovimiento->tipo = 1;
                                        $objMovimiento->cantidad = $objInsumoXProducto->cantidad * $objDetallePedido->cantidad;
                                        $objMovimiento->detalle = "PRODUCTO ANULADO - PEDIDO ".str_pad($objDetallePedido->pedido, 8, "0", STR_PAD_LEFT);
                                        $objMovimiento->modulo = "1";
                                        $objMovimiento->fecha = date("Y-m-d H:i:s");
                                        $objMovimiento->estado = 1;
                                        $resultado = $objMovimiento->add();
                                        if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                            file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla movimientos\n", FILE_APPEND);
                                            $error = 1;
                                            break;
                                        }
                                        file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla movimientos : " . $resultado[1] . "\n", FILE_APPEND);
                                    }
                                }
                            }
                        }
                        if ($error == 0) {
                            mysqli_commit(Database::getCon());
                            echo $resultado[0];
                        } else {
                            mysqli_rollback(Database::getCon());
                            echo 0;
                        }
                    } else {
                        file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
                        mysqli_rollback(Database::getCon());
                        echo 0;
                    }
                } else {
                    mysqli_rollback(Database::getCon());
                    echo 0;
                }
            } else {
                file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla detalle_pedidos : " . $objDetallePedido->id . "\n", FILE_APPEND);
                mysqli_rollback(Database::getCon());
                echo 0;
            }
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            return;
        }
        if ($_POST["indicador"] == 5) {
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "OPCION: AGREGAR PRODUCTO\n", FILE_APPEND);
            
            $error = 0;
            $objDetallePedido = DetallePedidoData::getById($_POST["idDetallePedido"]);            
            $cantidadActual = $objDetallePedido->cantidad;
            if ($cantidadActual != trim($_POST["cantidad"])) {                
                file_put_contents("info" . date("Ymd") . ".log", "Actualización Cantidad - Comentario\n", FILE_APPEND);
                
                mysqli_begin_transaction(Database::getCon());
                
                $objDetallePedido->cantidad = trim($_POST["cantidad"]);
                $objDetallePedido->comentario = strtoupper(trim($_POST["comentario"]));
                $totalActual = $objDetallePedido->total;
                
                $total = $objDetallePedido->cantidad * $objDetallePedido->precio_venta;
                $subtotal = $igv = 0;
                if ($_SESSION["exonerado"] == 0) {
                    $subtotal = $total / 1.18;
                    $igv = $subtotal * 0.18;
                } else if ($_SESSION["exonerado"] == 1) {
                    $subtotal = $total;
                }
                
                $objDetallePedido->fecha_comanda = "0000-00-00 00:00:00";
                $objDetallePedido->subtotal = round($subtotal, 2);
                $objDetallePedido->igv = round($igv, 2);
                $objDetallePedido->total = $total;
                $resultado = $objDetallePedido->update();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla detalle_pedidos : " . $objDetallePedido->id . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Detalle Pedido - Cantidad : " . $objDetallePedido->cantidad . "\n", FILE_APPEND);
                    file_put_contents("info" . date("Ymd") . ".log", "Detalle Pedido - Total : " . $objDetallePedido->total . "\n", FILE_APPEND);
                    
                    $objPago = PagoData::getByPedido($objDetallePedido->pedido);
                    if ($objPago) {
                        $nuevoTotal = $totalActual - $objDetallePedido->total;
                        $objPago->monto_total = round($objPago->monto_total - $nuevoTotal, 2);
                        $resultado = $objPago->update();
                        if (!(isset($resultado[0]) && $resultado[0] == 1)) {
                            file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla pagos : " . $objPago->id . "\n", FILE_APPEND);
                            $error = 1;
                        } else {
                            file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla pagos : " . $objPago->id . "\n", FILE_APPEND);
                            if (round($objPago->monto_total, 2) <= round(($objPago->monto_pagado_efectivo + $objPago->monto_pagado_tarjeta), 2)) {
                                $objPedido = PedidoData::getById($objPago->pedido);
                                $objPedido->estado = 2;
                                $objPedido->fecha_actualizacion = date("Y-m-d H:i:s");
                                $objPedido->usuario_actualizacion = $_SESSION["user"];
                                $resultado = $objPedido->update();
                                if (!(isset($resultado[0]) && $resultado[0] == 1)) {
                                    file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
                                    $error = 1;
                                }
                                file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
                            }
                        }
                    }
                    if ($error == 0) {
                        $subtotal = $igv = $total = 0;
                        $lstDetallePedido = DetallePedidoData::getProductosXPedido($objDetallePedido->pedido);
                        foreach ($lstDetallePedido as $objDetallePedido) {
                            $subtotal += $objDetallePedido->subtotal;
                            $igv += $objDetallePedido->igv;
                            $total += $objDetallePedido->total;
                        }                        
                        $objPedido = PedidoData::getById($objDetallePedido->pedido);
                        $objPedido->subtotal = round($subtotal, 2);
                        $objPedido->igv = round($igv, 2);
                        $objPedido->total = round($total, 2);
                        $resultado = $objPedido->update();
                        if (isset($resultado[0]) && $resultado[0] == 1) {
                            file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
                            file_put_contents("info" . date("Ymd") . ".log", "Pedido - Total : " . $objPedido->total . "\n", FILE_APPEND);
                            
                            // Obtener los insumos asociados a cada producto
                            $lstInsumoXProducto = DetalleRecetaData::getAllInsumosByProducto($objDetallePedido->producto);
                            if (count($lstInsumoXProducto) > 0) {
                                foreach ($lstInsumoXProducto as $objInsumoXProducto) {
                                    $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objInsumoXProducto->insumo, $_POST["almacen"]);
                                    if (count($lstInsumoAlmacen) > 0) {
                                        $objInsumoAlmacen = $lstInsumoAlmacen[0];
                                        $objInsumoAlmacen->stock = $objInsumoAlmacen->stock + ($objInsumoXProducto->cantidad * $objDetallePedido->cantidad);
                                        $resultado = $objInsumoAlmacen->updateStock();
                                        if (!(isset($resultado[0]) && $resultado[0] == 1)) {
                                            file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla insumos_almacen : (Insumo : " . $objInsumoXProducto->insumo . " - Almacen : " . $_POST["almacen"] . ")\n", FILE_APPEND);
                                            $error = 1;
                                            break;
                                        }
                                        file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla insumos_almacen : (Insumo : " . $objInsumoXProducto->insumo . " - Almacen : " . $_POST["almacen"] . ")\n", FILE_APPEND);
                                        
                                        $objMovimiento = new MovimientoData();
                                        $objMovimiento->sede = $objPedido->sede;
                                        $objMovimiento->insumo = $objInsumoXProducto->insumo;
                                        $objMovimiento->tipo = 0;
                                        $objMovimiento->cantidad = $objInsumoXProducto->cantidad * $cantidadActual;
                                        $objMovimiento->detalle = "PRODUCTO ANULADO - PEDIDO ".str_pad($objDetallePedido->pedido, 8, "0", STR_PAD_LEFT);
                                        $objMovimiento->modulo = "1";
                                        $objMovimiento->fecha = date("Y-m-d H:i:s");
                                        $objMovimiento->estado = 1;
                                        $resultado = $objMovimiento->add();
                                        if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                            file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla movimientos\n", FILE_APPEND);
                                            $error = 1;
                                            break;
                                        }
                                        file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla movimientos : " . $resultado[1] . "\n", FILE_APPEND);
                                        
                                        $objMovimiento = new MovimientoData();
                                        $objMovimiento->sede = $objPedido->sede;
                                        $objMovimiento->insumo = $objInsumoXProducto->insumo;
                                        $objMovimiento->tipo = 1;
                                        $objMovimiento->cantidad = $objInsumoXProducto->cantidad * $objDetallePedido->cantidad;
                                        $objMovimiento->detalle = "PRODUCTO ACTUALIZADO - PEDIDO ".str_pad($objDetallePedido->pedido, 8, "0", STR_PAD_LEFT);
                                        $objMovimiento->modulo = "1";
                                        $objMovimiento->fecha = date("Y-m-d H:i:s");
                                        $objMovimiento->estado = 1;
                                        $resultado = $objMovimiento->add();
                                        if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                            file_put_contents("info" . date("Ymd") . ".log", "Error registro en tabla movimientos\n", FILE_APPEND);
                                            $error = 1;
                                            break;
                                        }
                                        file_put_contents("info" . date("Ymd") . ".log", "Registro en tabla movimientos : " . $resultado[1] . "\n", FILE_APPEND);
                                    }
                                }                            
                            }
                            if ($error == 0) {
                                mysqli_commit(Database::getCon());
                                echo $resultado[0];
                            } else {
                                mysqli_rollback(Database::getCon());
                                echo 0;
                            }
                        } else {
                            file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla pedidos : " . $objPedido->id . "\n", FILE_APPEND);
                            mysqli_rollback(Database::getCon());
                            echo 0;
                        }
                    } else {
                        mysqli_rollback(Database::getCon());
                        echo 0;
                    }
                } else {
                    file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla detalle_pedidos : " . $objDetallePedido->id . "\n", FILE_APPEND);
                    mysqli_rollback(Database::getCon());
                    echo 0;
                }
            } else {
                file_put_contents("info" . date("Ymd") . ".log", "Actualización Comentario\n", FILE_APPEND);
                
                $objDetallePedido->comentario = strtoupper(trim($_POST["comentario"]));
                $objDetallePedido->fecha_comanda = "0000-00-00 00:00:00";
                $resultado = $objDetallePedido->update();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    file_put_contents("info" . date("Ymd") . ".log", "Actualización en tabla detalle_pedidos : " . $objDetallePedido->id . "\n", FILE_APPEND);
                    echo $resultado[0];
                } else {
                    file_put_contents("info" . date("Ymd") . ".log", "Error actualización en tabla detalle_pedidos : " . $objDetallePedido->id . "\n", FILE_APPEND);
                    echo 0;
                }
            }
            file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);
            return;
        }
    }
    
    $credito = 0;
    if (isset($_SESSION["productos"]) && count($_SESSION["productos"]) > 0) {        
        if (isset($_POST["credito"])) {
            $credito = $_POST["credito"];
        }
        
        $cadena = "
            <table class='table table-hover'>
                <thead>
                    <tr class='btn-primary'>
                        <th scope='col'>Item</th>
                        <th scope='col'>Categoría</th>
                        <th scope='col'>Producto</th>
                        <th scope='col'>Tipo</th>";
        if ($credito == 0) {
            $cadena .= "        
                        <th scope='col'>Comentario</th>";
        }
        $cadena .= "
                        <th scope='col' style='width: 10%;'>Cantidad</th>";
        if ($credito == 1) {
            $cadena .= "
                        <th scope='col' style='text-align: right;'>Precio</th>
                        <th scope='col' style='text-align: right;'>Total</th>";
        }
        $cadena .= "
                        <th scope='col' style='text-align: center;'>Acciones</th>
                    </tr>
                </thead>
                <tbody>";
            
        $item = $indice = 1;
        foreach ($_SESSION["productos"] as $valor) {
            $data = explode("|", $valor);
            $objProducto = ProductoData::getById($data[0]);
            if ($objProducto) {
                $objCategoria = $objProducto->getCategoria();
                $objTipo = $objProducto->getTipo();
                $cantidad = $data[1];
                $comentario = $data[2];
                $precio = $objProducto->precio1;
                $total = $cantidad * $precio;
                
                $cadena .= "
                    <tr>
                        <td style='text-align: left;'>".$indice++."</td>
                        <td style='text-align: left;'>".$objCategoria->nombre."</td>
                        <td style='text-align: left;'>".$objProducto->nombre."</td>
                        <td style='text-align: left;'>".$objTipo->nombre."</td>";
                if ($credito == 0) {
                    $cadena .= "
                        <td style='text-align: left;'>
                            <input type='text' id='comentario".$item.$objProducto->id."' name='comentario".$item.$objProducto->id."' value='".$comentario."' maxlength='50' class='form-control'/>
                        </td>";
                }
                $cadena .= "
                        <td style='text-align: right;'>
                            <input type='text' id='cantidad".$item.$objProducto->id."' name='cantidad".$item.$objProducto->id."' value='".$cantidad."' class='form-control' maxlength='5' dir='rtl' onkeypress='return soloNumeros(event)'/>
                        </td>";
                
                if ($credito == 1) {
                    $cadena .= "
                        <td style='text-align: right;'>".number_format($precio, 2)."</td>
                        <td style='text-align: right;'>".number_format($total, 2)."</td>";                        
                }
                $cadena .= "
                        <td style='text-align: center;'>
                            <a id='guardar".$item.$objProducto->id."' title='Guardar' class='btn btn-success btn-xs'><em class='fa fa-save'></em></a>
                            <a id='eliminar".$item.$objProducto->id."' title='Eliminar' class='btn btn-danger btn-xs'><em class='fa fa-trash'></em></a>
                            <script type='text/javascript'>
                                $('#guardar".$item.$objProducto->id."').click(function() {
                                    $('#guardar".$item.$objProducto->id."').attr('disabled','disabled');
                                    $('#eliminar".$item.$objProducto->id."').attr('disabled','disabled');
                                    var cantidad = $('#cantidad".$item.$objProducto->id."').val();
                                    var comentario = $('#comentario".$item.$objProducto->id."').val();
                                    var producto = ".$objProducto->id.";
                                    if (cantidad === '') {
                                        document.getElementById('cantidad".$item.$objProducto->id."').focus();
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Ingrese la cantidad'
                    				    })
                                    } else if (isNaN(cantidad)) {
                                        $('#cantidad".$item.$objProducto->id."').val('');
                                        document.getElementById('cantidad".$item.$objProducto->id."').focus();
                                        Swal.fire({
    	    							    icon: 'warning',
    	    						        title: 'Ingrese cantidad válida'
                                        })
                                    } else {
                                        $.blockUI();
                                    	$.post('./?action=getdetailssale', {
                                            item: ".$item.",
                                            producto: producto,
                                            comentario: comentario,
                                            cantidad: cantidad,
                                            indicador: 3
                                        }, function (data) {
                                            $('#tabla').html('');
                                            $('#tabla').append(data);
                                            $.unblockUI();
                                        });
                                    }
                            	});
                                $('#eliminar".$item.$objProducto->id."').click(function() {
                                    $('#guardar".$item.$objProducto->id."').attr('disabled','disabled');
                                    $('#eliminar".$item.$objProducto->id."').attr('disabled','disabled');
                                	var producto = ".$objProducto->id.";
                                    $.blockUI();                                	
                                	$.post('./?action=getdetailssale', {
                                        item: ".$item.",
                                        producto: producto,                                        
                                        indicador: 2
                                    }, function (data) {
                                        $('#tabla').html('');
                                        $('#tabla').append(data);
                                        $.unblockUI();
                                    });
                            	});
                            </script>
                        </td>
                    </tr>";
                $item++;
            }   
        }
        $cadena .= "
                </tbody>
            </table>";
    } else {
        $cadena = "
            <table class='table table-hover'>
                <thead>
                    <tr class='btn-primary'>
                        <th scope='col'>Item</th>
                        <th scope='col'>Categoría</th>
                        <th scope='col'>Producto</th>
                        <th scope='col'>Tipo</th>";
        if ($credito == 0) {
            $cadena .= "         
                        <th scope='col'>Comentario</th>";
        }
        $cadena .= "
                        <th scope='col' style='text-align: right;'>Cantidad</th>";
        if ($credito == 1) {
            $cadena .= "
                        <th scope='col' style='text-align: right;'>Precio</th>
                        <th scope='col' style='text-align: right;'>Total</th>";
        }
        $cadena .= "
                        <th scope='col' style='text-align: center;'>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>";
        
        unset($_SESSION["productos"]);
        unset($_SESSION["tmp_productos"]);
    }
    echo $cadena;
?>