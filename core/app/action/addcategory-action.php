<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            $objCategoria = new CategoriaData();
            $objCategoria->sede = $_POST["sede"];
            $objCategoria->nombre = strtoupper(trim($_POST["nombre"]));
            $objCategoria->indicador = $_POST["indicador"];
            $objCategoria->fecha_actualizacion = date("Y-m-d H:i:s");
            $objCategoria->usuario_actualizacion = $_SESSION["user"];

            if ($_POST["id"] == 0) {
                $objCategoria->estado = 1;
                $objCategoria->fecha_creacion = date("Y-m-d H:i:s");
                $objCategoria->usuario_creacion = $_SESSION["user"];
                
                $lstCategoria = CategoriaData::getAllCategoriaRepetida(1, $objCategoria->sede, $objCategoria->nombre);
                if (count($lstCategoria) > 0) {
                    echo -1;
                } else { 
                    $resultado = $objCategoria->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        echo $resultado[1];
                    } else {
                        echo 0;
                    }
                }
            } else {
                $objCategoria->id = $_POST["id"];
                
                $lstCategoria = CategoriaData::getAllCategoriaRepetida(1, $objCategoria->sede, $objCategoria->nombre, $objCategoria->id);
                if (count($lstCategoria) > 0) {
                    echo -1;
                } else {
                    $resultado = $objCategoria->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        echo $resultado[0];
                    } else {
                        echo 0;
                    }
                }
            }            
        }
        if ($_POST["accion"] == 2) {
            $objCategoria = CategoriaData::getById($_POST["id"]);
            $objCategoria->estado = 0;
            $objCategoria->fecha_actualizacion = date("Y-m-d H:i:s");
            $objCategoria->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objCategoria->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>