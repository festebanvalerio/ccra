<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            $lstInsumo = $_SESSION["insumos"];
            if (count($lstInsumo) == 0) {
                echo -1;
            } else {
                $objAjuste = new AjusteData();
                $objAjuste->fecha = date("Y-m-d H:i:s");
                $objAjuste->almacen = $_POST["almacen"];
                $objAjuste->observacion = strtoupper(trim($_POST["observacion"]));            
                $objAjuste->fecha_actualizacion = date("Y-m-d H:i:s");
                $objAjuste->usuario_actualizacion = $_SESSION["user"];
    
                if ($_POST["id"] == 0) {
                    $objAjuste->estado = 1;
                    $objAjuste->fecha_creacion = date("Y-m-d H:i:s");
                    $objAjuste->usuario_creacion = $_SESSION["user"];
                    $resultado = $objAjuste->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        $idAjuste = $resultado[1];
                        for ($indice = 0; $indice < count($lstInsumo); $indice ++) {
                            $data = explode("|", $lstInsumo[$indice]);
                            
                            $objDetalleAjuste = new DetalleAjusteData();
                            $objDetalleAjuste->ajuste = $idAjuste;
                            $objDetalleAjuste->insumo = $data[0];
                            $objDetalleAjuste->stock_actual = $data[3];
                            $objDetalleAjuste->cantidad = $data[1];
                            $objDetalleAjuste->tipo = $data[2];
                            $objDetalleAjuste->estado = 1;
                            $resultado = $objDetalleAjuste->add();
                            if (isset($resultado[1]) && $resultado[1] > 0) {
                                $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $data[0], $_POST["almacen"]);                                
                                if (count($lstInsumoAlmacen) > 0) {
                                    $stock = 0;
                                    if ($data[2] == 0) {
                                        // Salida
                                        $stock = $lstInsumoAlmacen[0]->stock - $data[1];
                                    } else if ($data[2] == 1) {
                                        // Ingreso
                                        if ($lstInsumoAlmacen[0]->stock < 0) {
                                            $stock = $data[1] + $lstInsumoAlmacen[0]->stock;
                                        } else {
                                            $stock = $data[1] - $lstInsumoAlmacen[0]->stock;
                                        }
                                    }
                                    $objInsumoAlmacen = $lstInsumoAlmacen[0];
                                    $objInsumoAlmacen->stock = $data[1];
                                    $resultado = $objInsumoAlmacen->updateStock();
                                    if (isset($resultado[0]) && $resultado[0] == 1) {
                                        $objAlmacen = AlmacenData::getById($_POST["almacen"]);
                                        
                                        $objMovimiento = new MovimientoData();
                                        $objMovimiento->sede = $objAlmacen->sede;
                                        $objMovimiento->insumo = $data[0];
                                        $objMovimiento->tipo = $data[2];
                                        $objMovimiento->cantidad = $stock;
                                        $objMovimiento->detalle = "AJUSTE INVENTARIO ".$idAjuste;
                                        $objMovimiento->modulo = "2";
                                        $objMovimiento->fecha = date("Y-m-d H:i:s");
                                        $objMovimiento->estado = 1;
                                        $objMovimiento->add();
                                    }
                                }
                            }
                        }
                        echo $idAjuste;
                    } else {
                        echo 0;
                    }
                }
            }
        }
        if ($_POST["accion"] == 2) {
            $objAjuste = AjusteData::getById($_POST["id"]);
            $objAjuste->estado = 0;
            $objAjuste->fecha_actualizacion = date("Y-m-d H:i:s");
            $objAjuste->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objAjuste->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>