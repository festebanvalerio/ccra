<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {
        if ($_POST["accion"] == 1) {
            $objProducto = new ProductoData();
            $objProducto->sede = $_POST["sede"];
            $objProducto->categoria = $_POST["categoria"];
            $objProducto->tipo = $_POST["tipo"];
            $objProducto->nombre = strtoupper(trim($_POST["nombre"]));            
            $objProducto->costo = str_replace(",", "", trim($_POST["costo"]));
            $objProducto->precio1 = str_replace(",", "", trim($_POST["precio1"]));
            $objProducto->precio2 = str_replace(",", "", trim($_POST["precio2"]));
            $objProducto->fecha_actualizacion = date("Y-m-d H:i:s");
            $objProducto->usuario_actualizacion = $_SESSION["user"];
            
            if ($_POST["id"] == 0) {
                $objProducto->estado = 1;
                $objProducto->fecha_creacion = date("Y-m-d H:i:s");
                $objProducto->usuario_creacion = $_SESSION["user"];
                $resultado = $objProducto->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    $idProducto = $resultado[1];
                    if (isset($_POST["area"])) {
                        $lstArea = $_POST["area"];
                        if (count($lstArea) > 0) {
                            for ($index=0;$index<count($lstArea);$index++) {
                                $objProductoAreaData = new ProductoAreaData();
                                $objProductoAreaData->producto = $idProducto;
                                $objProductoAreaData->area = $lstArea[$index];
                                $resultado = $objProductoAreaData->add();
                            }
                            echo $resultado[0];
                        } else {
                            echo -1;
                        }
                    } else {
                        echo $idProducto;
                    }
                } else {
                    echo 0;
                }
            } else {
                $objProducto->id = $_POST["id"];
                $resultado = $objProducto->update();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    $lstArea = $_POST["area"];
                    if (count($lstArea) > 0) {
                        $objProductoAreaData = new ProductoAreaData();
                        $objProductoAreaData->producto = $_POST["id"];
                        $resultado = $objProductoAreaData->delete();
                        if (isset($resultado[0]) && $resultado[0] == 1) {
                            for ($index=0;$index<count($lstArea);$index++) {
                                $objProductoAreaData->producto = $_POST["id"];
                                $objProductoAreaData->area = $lstArea[$index];
                                $resultado = $objProductoAreaData->add();
                            }
                            echo $resultado[0];
                        } else {
                            echo 0;
                        }
                    } else {
                        echo 0;
                    }
                } else {
                    echo 0;
                }
            }            
        }
        if ($_POST["accion"] == 2) {
            $objProducto = ProductoData::getById($_POST["id"]);
            $objProducto->estado = 0;
            $objProducto->fecha_actualizacion = date("Y-m-d H:i:s");
            $objProducto->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objProducto->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>