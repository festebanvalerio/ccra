<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            $objInsumo = new InsumoData();
            $objInsumo->sede = $_POST["sede"];
            $objInsumo->unidad = $_POST["unidad"];
            $objInsumo->costo = str_replace(",", "", $_POST["costo"]);
            $objInsumo->clasificacion = $_POST["clasificacion"];
            $objInsumo->indicador = $_POST["indicador"];
            $objInsumo->nombre = strtoupper(trim($_POST["nombre"]));
            $objInsumo->fecha_actualizacion = date("Y-m-d H:i:s");
            $objInsumo->usuario_actualizacion = $_SESSION["user"];

            if ($_POST["id"] == 0) {
                $objInsumo->estado = 1;
                $objInsumo->fecha_creacion = date("Y-m-d H:i:s");
                $objInsumo->usuario_creacion = $_SESSION["user"];
                
                $lstInsumo = InsumoData::getAllInsumoRepetido(1, $objInsumo->sede, $objInsumo->unidad, $objInsumo->nombre);
                if (count($lstInsumo) > 0) {
                    echo -1;
                } else {
                    $resultado = $objInsumo->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        echo $resultado[1];
                    } else {
                        echo 0;
                    }
                }
            } else {
                $objInsumo->id = $_POST["id"];
                
                $lstInsumo = InsumoData::getAllInsumoRepetido(1, $objInsumo->sede, $objInsumo->unidad, $objInsumo->nombre, $objInsumo->id);
                if (count($lstInsumo) > 0) {
                    echo -1;
                } else {
                    $resultado = $objInsumo->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        echo $resultado[0];
                    } else {
                        echo 0;
                    }
                }
            }
        } else if ($_POST["accion"] == 2) {
            $objInsumo = InsumoData::getById($_POST["id"]);
            $objInsumo->estado = 0;
            $objInsumo->fecha_actualizacion = date("Y-m-d H:i:s");
            $objInsumo->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objInsumo->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>