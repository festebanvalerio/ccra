<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {
        if ($_POST["accion"] == 1) {
            if (!isset($_SESSION["insumos"])) {
                echo -1;
            } else {
                $lstInsumo = $_SESSION["insumos"];
                if (count($lstInsumo) == 0) {
                    echo -1;
                } else {
                    mysqli_begin_transaction(Database::getCon());
                    
                    $existeError = 0;
                    $objOrdenCompra = new OrdenCompraData();
                    $objOrdenCompra->fecha = date("Y-m-d H:i:s");
                    $objOrdenCompra->sede = $_POST["idsede"];
                    $objOrdenCompra->almacen = $_POST["almacen"];
                    $objOrdenCompra->tipo_documento = $_POST["tipodoc"];
                    $objOrdenCompra->num_documento = strtoupper(trim($_POST["numdoc"]));                
                    $objOrdenCompra->ruc = trim($_POST["ruc"]);
                    $objOrdenCompra->razon_social = strtoupper(trim($_POST["razon"]));
                    $objOrdenCompra->monto = str_replace(",", "", $_POST["monto"]);                
                    $objOrdenCompra->fecha_actualizacion = date("Y-m-d H:i:s");
                    $objOrdenCompra->usuario_actualizacion = $_SESSION["user"];
                    $objOrdenCompra->estado = 1;
                    $objOrdenCompra->fecha_creacion = date("Y-m-d H:i:s");
                    $objOrdenCompra->usuario_creacion = $_SESSION["user"];                    
                    $resultado = $objOrdenCompra->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        $idOrdenCompra = $resultado[1];
                        
                        $item = 1;
                        for ($indice = 0; $indice < count($lstInsumo); $indice ++) {
                            $data = explode("|", $lstInsumo[$indice]);
                            
                            $texto = "cant".$item.$data[0];
                            if (isset($_POST[$texto])) {
                                $data[1] = str_replace(",", "", $_POST[$texto]);
                            }
                            
                            $objDetalleOrdenCompra = new DetalleOrdenCompraData();
                            $objDetalleOrdenCompra->oc = $idOrdenCompra;
                            $objDetalleOrdenCompra->insumo = $data[0];
                            $objDetalleOrdenCompra->unidad_almacen = $data[2];
                            $objDetalleOrdenCompra->unidad_compra = $data[4];
                            $objDetalleOrdenCompra->cantidad = $data[1];
                            $objDetalleOrdenCompra->costo = $data[3];
                            $objDetalleOrdenCompra->indicador = $data[5];
                            $objDetalleOrdenCompra->estado = 1;
                            $resultado = $objDetalleOrdenCompra->add();
                            if (isset($resultado[1]) && $resultado[1] > 0) {
                                // En el caso que la unidad de medida de compra es distinto a la unidad de medida de almacen
                                if ($data[5] == 0) {
                                    $lstEquivalencia = EquivalenciaData::getAllByInsumo(1, $data[0], $data[2], $data[4]);
                                    if (count($lstEquivalencia) > 0) {
                                        // Actualizar la cantidad con el factor de equivalencia
                                        $objEquivalencia = $lstEquivalencia[0];
                                        $factor = $objEquivalencia->factor;
                                        $data[1] = $data[1] * $factor;
                                        $data[5] = 1;
                                    }
                                }                            
                                // En el caso que la unidad de medida de compra es igual a la unidad de medida de almacen 
                                if ($data[5] == 1) {
                                    // Almacen
                                    $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $data[0], $_POST["almacen"]);
                                    if (count($lstInsumoAlmacen) > 0) {
                                        $stock = $lstInsumoAlmacen[0]->stock + $data[1];
    
                                        $objAlmacen = AlmacenData::getById($lstInsumoAlmacen[0]->almacen);
                                        
                                        $objInsumoAlmacen = $lstInsumoAlmacen[0];
                                        $objInsumoAlmacen->stock = $stock;
                                        $resultado = $objInsumoAlmacen->updateStock();
                                        if (isset($resultado[0]) && $resultado[0] == 1) {
                                            $objMovimiento = new MovimientoData();
                                            $objMovimiento->sede = $objAlmacen->sede;
                                            $objMovimiento->insumo = $data[0];
                                            $objMovimiento->tipo = 1;
                                            $objMovimiento->cantidad = $data[1];
                                            $objMovimiento->detalle = "ORDEN COMPRA " . str_pad($idOrdenCompra, 8, "0", STR_PAD_LEFT);
                                            $objMovimiento->modulo = "4";
                                            $objMovimiento->fecha = date("Y-m-d H:i:s");
                                            $objMovimiento->estado = 1;
                                            $resultado = $objMovimiento->add();
                                            if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                                $existeError = 1;
                                                break;
                                            }
                                        }
                                    } else {
                                        $objInsumoAlmacen = new InsumoAlmacenData();
                                        $objInsumoAlmacen->almacen = $_POST["almacen"];
                                        $objInsumoAlmacen->insumo = $data[0];
                                        $objInsumoAlmacen->stock = $data[1];
                                        $objInsumoAlmacen->stock_minimo = 0.00;
                                        $objInsumoAlmacen->stock_maximo = 0.00;
                                        $objInsumoAlmacen->estado = 1;
                                        $resultado = $objInsumoAlmacen->add();
                                        if (isset($resultado[1]) && $resultado[1] > 0) {
                                            $objAlmacen = AlmacenData::getById($_POST["almacen"]);
                                            
                                            $objMovimiento = new MovimientoData();
                                            $objMovimiento->sede = $objAlmacen->sede;
                                            $objMovimiento->insumo = $data[0];
                                            $objMovimiento->tipo = 1;
                                            $objMovimiento->cantidad = $data[1];
                                            $objMovimiento->detalle = "ORDEN COMPRA " . str_pad($idOrdenCompra, 8, "0", STR_PAD_LEFT);
                                            $objMovimiento->modulo = "4";
                                            $objMovimiento->fecha = date("Y-m-d H:i:s");
                                            $objMovimiento->estado = 1;
                                            $resultado = $objMovimiento->add();
                                            if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                                $existeError = 1;
                                                break;
                                            }
                                        } else {
                                            $existeError = 1;
                                            break;
                                        }
                                    }
                                }
                            } else {
                                $existeError = 1;
                                break;                            
                            }
                        }
                        if (!$existeError) {
                            mysqli_commit(Database::getCon());
                            echo $idOrdenCompra;
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
        if ($_POST["accion"] == 2) {
            mysqli_begin_transaction(Database::getCon());
            
            $existeError = 0;
            $objOrdenCompra = OrdenCompraData::getById($_POST["id"]);
            $objOrdenCompra->estado = 0;
            $objOrdenCompra->fecha_actualizacion = date("Y-m-d H:i:s");
            $objOrdenCompra->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objOrdenCompra->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                $lstDetalleOrdenCompra = DetalleOrdenCompraData::getAllByOrdenCompra($objOrdenCompra->id);
                foreach ($lstDetalleOrdenCompra as $objDetalleOrdenCompra) {
                    // Almacen
                    $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objDetalleOrdenCompra->insumo, $objOrdenCompra->almacen);
                    if (count($lstInsumoAlmacen) > 0) {
                        $stock = $lstInsumoAlmacen[0]->stock - $objDetalleOrdenCompra->cantidad;

                        $objAlmacen = AlmacenData::getById($lstInsumoAlmacen[0]->almacen);

                        $objInsumoAlmacen = $lstInsumoAlmacen[0];
                        $objInsumoAlmacen->stock = $stock;
                        $resultado = $objInsumoAlmacen->updateStock();
                        if (isset($resultado[0]) && $resultado[0] == 1) {
                            $objMovimiento = new MovimientoData();
                            $objMovimiento->sede = $objAlmacen->sede;
                            $objMovimiento->insumo = $objDetalleOrdenCompra->insumo;
                            $objMovimiento->tipo = 0;
                            $objMovimiento->cantidad = $objDetalleOrdenCompra->cantidad;
                            $objMovimiento->detalle = "ORDEN COMPRA ANULADA " . str_pad($objOrdenCompra->id, 8, "0", STR_PAD_LEFT);
                            $objMovimiento->modulo = "4";
                            $objMovimiento->fecha = date("Y-m-d H:i:s");
                            $objMovimiento->estado = 1;
                            $resultado = $objMovimiento->add();
                            if (!(isset($resultado[1]) && $resultado[1] > 0)) {
                                $existeError = 1;
                                break;
                            }
                        } else {
                            $existeError = 1;
                            break;
                        }
                    }
                }
                if (!$existeError) {
                    mysqli_commit(Database::getCon());
                    echo 1;
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