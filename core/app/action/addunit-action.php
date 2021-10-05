<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            $objUnidad = new UnidadData();
            $objUnidad->abreviatura = strtoupper(trim($_POST["abreviatura"]));
            $objUnidad->nombre = strtoupper(trim($_POST["nombre"]));
            $objUnidad->fecha_actualizacion = date("Y-m-d H:i:s");
            $objUnidad->usuario_actualizacion = $_SESSION["user"];

            if ($_POST["id"] == 0) {
                $objUnidad->estado = 1;
                $objUnidad->fecha_creacion = date("Y-m-d H:i:s");
                $objUnidad->usuario_creacion = $_SESSION["user"];
                
                $lstUnidad = UnidadData::getAllUnidadRepetida(1, $objUnidad->abreviatura, $objUnidad->nombre);
                if (count($lstUnidad) > 0) {
                    echo -1;
                } else {
                    $resultado = $objUnidad->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        echo $resultado[1];
                    } else {
                        echo 0;
                    }
                }
            } else {
                $objUnidad->id = $_POST["id"];

                $lstUnidad = UnidadData::getAllUnidadRepetida(1, $objUnidad->abreviatura, $objUnidad->nombre, $objUnidad->id);
                if (count($lstUnidad) > 0) {
                    echo -1;
                } else {
                    $resultado = $objUnidad->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        echo $resultado[0];
                    } else {
                        echo 0;
                    }
                }
            }            
        }
        if ($_POST["accion"] == 2) {
            $objUnidad = UnidadData::getById($_POST["id"]);
            $objUnidad->estado = 0;
            $objUnidad->fecha_actualizacion = date("Y-m-d H:i:s");
            $objUnidad->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objUnidad->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>