<?php
if (count($_POST) > 0) {    
    if (isset($_POST["accion"])) {  
        $error = 0;            
        if ($_POST["accion"] == 1) {            
            if ($_POST["id"] == 0) {
                $lstInsumo = $_SESSION["insumos"];
                if (count($lstInsumo) == 0) {
                    echo -1;
                } else {
                    mysqli_begin_transaction(Database::getCon());
                    
                    $objReceta = new RecetaData();
                    $objReceta->sede = $_POST["sede"];
                    $objReceta->producto = $_POST["producto"];
                    $objReceta->costo = str_replace(",", "", $_POST["costo"]);
                    $objReceta->descripcion = strtoupper(trim($_POST["descripcion"]));
                    $objReceta->estado = 1;
                    $objReceta->fecha_actualizacion = date("Y-m-d H:i:s");
                    $objReceta->usuario_actualizacion = $_SESSION["user"];                    
                    $objReceta->fecha_creacion = date("Y-m-d H:i:s");
                    $objReceta->usuario_creacion = $_SESSION["user"];
                    $resultado = $objReceta->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        $idReceta = $resultado[1];
                        for ($indice = 0; $indice < count($lstInsumo); $indice ++) {
                            $data = explode("|", $lstInsumo[$indice]);

                            if ($data[3] == 0) {
                                $objDetalleReceta = new DetalleRecetaData();
                                $objDetalleReceta->receta = $idReceta;
                                $objDetalleReceta->insumo = $data[0];
                                $objDetalleReceta->cantidad = $data[1];
                                $objDetalleReceta->precio = $data[2];
                                $objDetalleReceta->estado = 1;
                                $resultado = $objDetalleReceta->add();
                                if (!(isset($resultado[0]) && $resultado[0] > 0)) {
                                    $error = 1;
                                    break;
                                }
                            }
                        }
                        if ($error == 0) {
                            mysqli_commit(Database::getCon());
                            echo $idReceta;
                        } else {
                            mysqli_rollback(Database::getCon());
                            echo 0;
                        }
                    } else {
                        mysqli_rollback(Database::getCon());
                        echo 0;                        
                    }
                }
            } else {
                mysqli_begin_transaction(Database::getCon());
                
                $objReceta = RecetaData::getById($_POST["id"]);
                $objReceta->costo = str_replace(",", "", $_POST["costo"]);
                $objReceta->descripcion = strtoupper(trim($_POST["descripcion"]));
                $objReceta->fecha_actualizacion = date("Y-m-d H:i:s");
                $objReceta->usuario_actualizacion = $_SESSION["user"];
                $resultado = $objReceta->update();
                if (isset($resultado[0]) && $resultado[0] > 0) {
                    $lstInsumo = $_SESSION["insumos"];
                    if (count($lstInsumo) > 0) {
                        for ($indice = 0; $indice < count($lstInsumo); $indice ++) {
                            $data = explode("|", $lstInsumo[$indice]);
                            if ($data[3] == 0) {
                                $objDetalleReceta = new DetalleRecetaData();
                                $objDetalleReceta->receta = $objReceta->id;
                                $objDetalleReceta->insumo = $data[0];
                                $objDetalleReceta->cantidad = $data[1];
                                $objDetalleReceta->precio = $data[2];
                                $objDetalleReceta->estado = 1;
                                $resultado = $objDetalleReceta->add();
                                if (!(isset($resultado[0]) && $resultado[0] > 0)) {
                                    $error = 1;
                                    break;
                                }
                            }
                        }
                    }
                    if ($error == 0) {
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
        if ($_POST["accion"] == 2) {
            $objReceta = RecetaData::getById($_POST["id"]);
            $objReceta->estado = 0;
            $objReceta->fecha_actualizacion = date("Y-m-d H:i:s");
            $objReceta->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objReceta->update();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }        
        if ($_POST["accion"] == 3) {
            // Eliminar insumo de una receta
            $objDetalleReceta = DetalleRecetaData::getById($_POST["id"]);
            $objDetalleReceta->estado = 0;
            $resultado = $objDetalleReceta->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                $_SESSION["insumos"] = array();
                $lstDetalleReceta = DetalleRecetaData::getAllByReceta($objDetalleReceta->receta);
                foreach ($lstDetalleReceta as $objDetalleReceta) {
                    $_SESSION["insumos"][] = $objDetalleReceta->insumo."|".$objDetalleReceta->cantidad."|".$objDetalleReceta->precio."|".$objDetalleReceta->id;
                }                
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>