<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {
        if ($_POST["accion"] == 1) {
            $objArea = new AreaData();
            $objArea->sede = $_POST["sede"];
            $objArea->nombre = strtoupper(trim($_POST["nombre"]));
            $objArea->impresora = trim($_POST["impresora"]);
            $objArea->fecha_actualizacion = date("Y-m-d H:i:s");
            $objArea->usuario_actualizacion = $_SESSION["user"];

            if ($_POST["id"] == 0) {
                $objArea->estado = 1;
                $objArea->fecha_creacion = date("Y-m-d H:i:s");
                $objArea->usuario_creacion = $_SESSION["user"];
                
                $lstArea = AreaData::getAllAreaRepetida(1, $objArea->sede, $objArea->nombre);
                if (count($lstArea) > 0) {
                    echo -1;
                } else {                    
                    $resultado = $objArea->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        echo $resultado[1];
                    } else {
                        echo 0;
                    }
                }
            } else {
                $objArea->id = $_POST["id"];

                $lstArea = AreaData::getAllAreaRepetida(1, $objArea->sede, $objArea->nombre, $objArea->id);
                if (count($lstArea) > 0) {
                    echo -1;
                } else {
                    $resultado = $objArea->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        echo $resultado[0];
                    } else {
                        echo 0;
                    }
                }
            }
        }
        if ($_POST["accion"] == 2) {
            $objArea = AreaData::getById($_POST["id"]);
            $objArea->estado = 0;
            $objArea->fecha_actualizacion = date("Y-m-d H:i:s");
            $objArea->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objArea->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>