<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {
        if ($_POST["accion"] == 1) {
            $lstInsumo = $_SESSION["insumos"];
            if (count($lstInsumo) == 0) {
                echo -1;
            } else {
                $objTransferencia = new TransferenciaData();
                $objTransferencia->fecha = date("Y-m-d H:i:s");
                $objTransferencia->sede_origen = $_POST["idsedeorigen"];
                $objTransferencia->almacen_origen = $_POST["idalmacenorigen"];
                $objTransferencia->sede_destino = $_POST["idsededestino"];
                $objTransferencia->almacen_destino = $_POST["almacendestino"];
                $objTransferencia->observacion = strtoupper(trim($_POST["observacion"]));
                $objTransferencia->fecha_actualizacion = date("Y-m-d H:i:s");
                $objTransferencia->usuario_actualizacion = $_SESSION["user"];

                if ($_POST["id"] == 0) {
                    $objTransferencia->estado = 1;
                    $objTransferencia->fecha_creacion = date("Y-m-d H:i:s");
                    $objTransferencia->usuario_creacion = $_SESSION["user"];
                    $resultado = $objTransferencia->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        $idTransferencia = $resultado[1];
                        for ($indice = 0; $indice < count($lstInsumo); $indice ++) {
                            $data = explode("|", $lstInsumo[$indice]);

                            $objDetalleTransferencia = new DetalleTransferenciaData();
                            $objDetalleTransferencia->transferencia = $idTransferencia;
                            $objDetalleTransferencia->insumo = $data[0];
                            $objDetalleTransferencia->cantidad = $data[1];
                            $objDetalleTransferencia->tipo = $data[2];
                            $objDetalleTransferencia->estado = 1;
                            $resultado = $objDetalleTransferencia->add();
                            if (isset($resultado[1]) && $resultado[1] > 0) {
                                // Almacen Origen
                                $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $data[0], $_POST["idalmacenorigen"]);
                                if (count($lstInsumoAlmacen) > 0) {
                                    $stock = $lstInsumoAlmacen[0]->stock - $data[1];

                                    $objAlmacenDestino = AlmacenData::getById($_POST["almacendestino"]);

                                    $detalle = " (A " . $objAlmacenDestino->nombre . ")";

                                    $objInsumoAlmacen = $lstInsumoAlmacen[0];
                                    $objInsumoAlmacen->stock = $stock;
                                    $resultado = $objInsumoAlmacen->updateStock();
                                    if (isset($resultado[0]) && $resultado[0] == 1) {
                                        $objMovimiento = new MovimientoData();
                                        $objMovimiento->sede = $_POST["idsedeorigen"];
                                        $objMovimiento->insumo = $data[0];
                                        $objMovimiento->tipo = $data[2];
                                        $objMovimiento->cantidad = $data[1];
                                        $objMovimiento->detalle = "TRANSFERENCIA " . str_pad($idTransferencia, 8, "0", STR_PAD_LEFT) . $detalle;
                                        $objMovimiento->modulo = "2";
                                        $objMovimiento->fecha = date("Y-m-d H:i:s");
                                        $objMovimiento->estado = 1;
                                        $objMovimiento->add();
                                    }
                                }

                                // Almacen Destino
                                $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $data[0], $_POST["almacendestino"]);
                                if (count($lstInsumoAlmacen) > 0) {
                                    $stock = $lstInsumoAlmacen[0]->stock + $data[1];

                                    $objAlmacenOrigen = AlmacenData::getById($_POST["idalmacenorigen"]);
                                    
                                    $detalle = " (DESDE " . $objAlmacenOrigen->nombre . ")";
                                    
                                    $objInsumoAlmacen = $lstInsumoAlmacen[0];
                                    $objInsumoAlmacen->stock = $stock;
                                    $resultado = $objInsumoAlmacen->updateStock();
                                    if (isset($resultado[0]) && $resultado[0] == 1) {
                                        $objMovimiento = new MovimientoData();
                                        $objMovimiento->sede = $_POST["idsededestino"];
                                        $objMovimiento->insumo = $data[0];
                                        $objMovimiento->tipo = 1;
                                        $objMovimiento->cantidad = $data[1];
                                        $objMovimiento->detalle = "TRANSFERENCIA " . str_pad($idTransferencia, 8, "0", STR_PAD_LEFT) . $detalle;
                                        $objMovimiento->modulo = "2";
                                        $objMovimiento->fecha = date("Y-m-d H:i:s");
                                        $objMovimiento->estado = 1;
                                        $objMovimiento->add();
                                    }
                                } else {
                                    $objInsumoAlmacen = new InsumoAlmacenData();
                                    $objInsumoAlmacen->almacen = $_POST["almacendestino"];
                                    $objInsumoAlmacen->insumo = $data[0];
                                    $objInsumoAlmacen->stock = $data[1];
                                    $objInsumoAlmacen->stock_minimo = 0.00;
                                    $objInsumoAlmacen->stock_maximo = 0.00;
                                    $objInsumoAlmacen->estado = 1;
                                    $resultado = $objInsumoAlmacen->add();
                                    if (isset($resultado[1]) && $resultado[1] > 0) {
                                        $objAlmacenOrigen = AlmacenData::getById($_POST["almacen"]);
                                        $objAlmacenDestino = AlmacenData::getById($_POST["almacendestino"]);
                                        
                                        $detalle = " (DESDE " . $objAlmacenOrigen->nombre . ")";
                                        
                                        $objMovimiento = new MovimientoData();
                                        $objMovimiento->sede = $objAlmacenDestino->sede;
                                        $objMovimiento->insumo = $data[0];
                                        $objMovimiento->tipo = 1;
                                        $objMovimiento->cantidad = $data[1];
                                        $objMovimiento->detalle = "TRANSFERENCIA " . str_pad($idTransferencia, 8, "0", STR_PAD_LEFT) . $detalle;
                                        $objMovimiento->modulo = "2";
                                        $objMovimiento->fecha = date("Y-m-d H:i:s");
                                        $objMovimiento->estado = 1;
                                        $objMovimiento->add();
                                    }
                                }
                            }
                        }
                        echo $idTransferencia;
                    } else {
                        echo 0;
                    }
                }
            }
        }
        if ($_POST["accion"] == 2) {
            $objTransferencia = TransferenciaData::getById($_POST["id"]);
            $objTransferencia->estado = 0;
            $objTransferencia->fecha_actualizacion = date("Y-m-d H:i:s");
            $objTransferencia->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objTransferencia->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                $lstDetalleTransferencia = DetalleTransferenciaData::getAllByTransferencia($objTransferencia->id);
                foreach ($lstDetalleTransferencia as $objDetalleTransferencia) {
                    // Almacen Origen
                    $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objDetalleTransferencia->insumo, $objTransferencia->almacen_origen);
                    if (count($lstInsumoAlmacen) > 0) {
                        $stock = $lstInsumoAlmacen[0]->stock + $objDetalleTransferencia->cantidad;

                        $objAlmacenOrigen = AlmacenData::getById($lstInsumoAlmacen[0]->almacen);

                        $objInsumoAlmacen = $lstInsumoAlmacen[0];
                        $objInsumoAlmacen->stock = $stock;
                        $resultado = $objInsumoAlmacen->updateStock();
                        if (isset($resultado[0]) && $resultado[0] == 1) {
                            $objMovimiento = new MovimientoData();
                            $objMovimiento->sede = $objAlmacenOrigen->sede;
                            $objMovimiento->insumo = $objDetalleTransferencia->insumo;
                            $objMovimiento->tipo = 1;
                            $objMovimiento->cantidad = $objDetalleTransferencia->cantidad;
                            $objMovimiento->detalle = "TRANSFERENCIA ANULADA " . str_pad($objTransferencia->id, 8, "0", STR_PAD_LEFT);
                            $objMovimiento->modulo = "2";
                            $objMovimiento->fecha = date("Y-m-d H:i:s");
                            $objMovimiento->estado = 1;
                            $objMovimiento->add();
                        }
                    }

                    // Almacen Destino
                    $lstInsumoAlmacen = InsumoAlmacenData::getAllByInsumo(1, $objDetalleTransferencia->insumo, $objTransferencia->almacen_destino);
                    if (count($lstInsumoAlmacen) > 0) {
                        $stock = $lstInsumoAlmacen[0]->stock - $objDetalleTransferencia->cantidad;

                        $objAlmacenDestino = AlmacenData::getById($lstInsumoAlmacen[0]->almacen);

                        $objInsumoAlmacen = $lstInsumoAlmacen[0];
                        $objInsumoAlmacen->stock = $stock;
                        $resultado = $objInsumoAlmacen->updateStock();
                        if (isset($resultado[0]) && $resultado[0] == 1) {
                            $objMovimiento = new MovimientoData();
                            $objMovimiento->sede = $objAlmacenDestino->sede;
                            $objMovimiento->insumo = $objDetalleTransferencia->insumo;
                            $objMovimiento->tipo = 0;
                            $objMovimiento->cantidad = $objDetalleTransferencia->cantidad;
                            $objMovimiento->detalle = "TRANSFERENCIA ANULADA " . str_pad($objTransferencia->id, 8, "0", STR_PAD_LEFT);
                            $objMovimiento->modulo = "2";
                            $objMovimiento->fecha = date("Y-m-d H:i:s");
                            $objMovimiento->estado = 1;
                            $objMovimiento->add();
                        }
                    }
                }
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>