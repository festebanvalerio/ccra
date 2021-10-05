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
                    $objIngreso = new IngresoData();
                    $objIngreso->fecha = date("Y-m-d H:i:s");
                    $objIngreso->sede = $_POST["idsede"];
                    $objIngreso->almacen = $_POST["almacen"];
                    $objIngreso->comentario = strtoupper(trim($_POST["comentario"]));
                    $objIngreso->fecha_actualizacion = date("Y-m-d H:i:s");
                    $objIngreso->usuario_actualizacion = $_SESSION["user"];
                    $objIngreso->estado = 1;
                    $objIngreso->fecha_creacion = date("Y-m-d H:i:s");
                    $objIngreso->usuario_creacion = $_SESSION["user"];                    
                    $resultado = $objIngreso->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        $idIngreso = $resultado[1];
                        
                        $item = 1;
                        for ($indice = 0; $indice < count($lstInsumo); $indice ++) {
                            $data = explode("|", $lstInsumo[$indice]);
                            
                            $texto = "cant".$item.$data[0];
                            if (isset($_POST[$texto])) {
                                $data[1] = str_replace(",", "", $_POST[$texto]);
                            }
                            
                            $objDetalleIngreso = new DetalleIngresoData();
                            $objDetalleIngreso->ingreso = $idIngreso;
                            $objDetalleIngreso->insumo = $data[0];
                            $objDetalleIngreso->unidad = $data[2];
                            $objDetalleIngreso->cantidad = $data[1];
                            $objDetalleIngreso->estado = 1;
                            $resultado = $objDetalleIngreso->add();
                            if (isset($resultado[1]) && $resultado[1] > 0) {
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
                                        $objMovimiento->detalle = "INGRESO " . str_pad($idIngreso, 8, "0", STR_PAD_LEFT);
                                        $objMovimiento->modulo = "5";
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
                                        $objMovimiento->detalle = "INGRESO " . str_pad($idIngreso, 8, "0", STR_PAD_LEFT);
                                        $objMovimiento->modulo = "5";
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
                            } else {
                                $existeError = 1;
                                break;                            
                            }
                        }
                        if (!$existeError) {
                            mysqli_commit(Database::getCon());
                            echo $idIngreso;
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
            $objIngreso = IngresoData::getById($_POST["id"]);
            $objIngreso->estado = 0;
            $objIngreso->fecha_actualizacion = date("Y-m-d H:i:s");
            $objIngreso->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objIngreso->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                $lstDetalleIngreso = DetalleIngresoData::getAllByIngreso($objIngreso->id);
                foreach ($lstDetalleIngreso as $objDetalleIngreso) {
                    // Almacen
                    $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objDetalleIngreso->insumo, $objIngreso->almacen);
                    if (count($lstInsumoAlmacen) > 0) {
                        $stock = $lstInsumoAlmacen[0]->stock - $objDetalleIngreso->cantidad;

                        $objAlmacen = AlmacenData::getById($lstInsumoAlmacen[0]->almacen);

                        $objInsumoAlmacen = $lstInsumoAlmacen[0];
                        $objInsumoAlmacen->stock = $stock;
                        $resultado = $objInsumoAlmacen->updateStock();
                        if (isset($resultado[0]) && $resultado[0] == 1) {
                            $objMovimiento = new MovimientoData();
                            $objMovimiento->sede = $objAlmacen->sede;
                            $objMovimiento->insumo = $objDetalleIngreso->insumo;
                            $objMovimiento->tipo = 0;
                            $objMovimiento->cantidad = $objDetalleIngreso->cantidad;
                            $objMovimiento->detalle = "INGRESO ANULADO " . str_pad($objIngreso->id, 8, "0", STR_PAD_LEFT);
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