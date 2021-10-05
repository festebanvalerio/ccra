<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            $objSubProducto = new SubProductoData();
            $objSubProducto->nombre = strtoupper(trim($_POST["nombre"]));            
            $objSubProducto->fecha_actualizacion = date("Y-m-d H:i:s");
            $objSubProducto->usuario_actualizacion = $_SESSION["user"];

            if ($_POST["id"] == 0) {
                $objSubProducto->estado = 1;
                $objSubProducto->fecha_creacion = date("Y-m-d H:i:s");
                $objSubProducto->usuario_creacion = $_SESSION["user"];
                $resultado = $objSubProducto->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    echo $resultado[1];
                } else {
                    echo 0;
                }
            } else {
                $objSubProducto->id = $_POST["id"];
                $resultado = $objSubProducto->update();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    echo $resultado[0];
                } else {
                    echo 0;
                }
            }            
        }
        if ($_POST["accion"] == 2) {
            $objSubProducto = SubProductoData::getById($_POST["id"]);
            $objSubProducto->estado = 0;
            $objSubProducto->fecha_actualizacion = date("Y-m-d H:i:s");
            $objSubProducto->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objSubProducto->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>