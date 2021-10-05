<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            $arrFecha = explode("/", $_POST["fechainicio"]);
            $fechaInicio = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            
            $arrFecha = explode("/", $_POST["fechafin"]);
            $fechaFin = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            
            $objProducto = ProductoData::getById($_POST["producto"]);
            
            $objDescuentoProgramado = new DescuentoProgramadoData();
            $objDescuentoProgramado->sede = $_POST["sede"];
            $objDescuentoProgramado->fecha = date("Y-m-d");
            $objDescuentoProgramado->fecha_inicio = $fechaInicio;
            $objDescuentoProgramado->fecha_fin = $fechaFin;
            $objDescuentoProgramado->producto = $_POST["producto"];
            $objDescuentoProgramado->precio_actual = $objProducto->precio1;            
            $objDescuentoProgramado->porcentaje = str_replace(",", "", trim($_POST["porcentaje"]));
            $objDescuentoProgramado->precio_descuento = $objProducto->precio1 - ($objProducto->precio1 * ($objDescuentoProgramado->porcentaje / 100));
            $objDescuentoProgramado->fecha_actualizacion = date("Y-m-d H:i:s");
            $objDescuentoProgramado->usuario_actualizacion = $_SESSION["user"];

            if ($_POST["id"] == 0) {
                $objDescuentoProgramado->estado = 1;
                $objDescuentoProgramado->fecha_creacion = date("Y-m-d H:i:s");
                $objDescuentoProgramado->usuario_creacion = $_SESSION["user"];
                $resultado = $objDescuentoProgramado->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    echo $resultado[1];
                } else {
                    echo 0;
                }
            } else {
                $objDescuentoProgramado->id = $_POST["id"];
                $resultado = $objDescuentoProgramado->update();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    echo $resultado[0];
                } else {
                    echo 0;
                }
            }            
        }
        if ($_POST["accion"] == 2) {
            $objDescuentoProgramado = DescuentoProgramadoData::getById($_POST["id"]);
            $objDescuentoProgramado->estado = 0;
            $objDescuentoProgramado->fecha_actualizacion = date("Y-m-d H:i:s");
            $objDescuentoProgramado->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objDescuentoProgramado->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>