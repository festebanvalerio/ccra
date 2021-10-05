<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            $objClasificacion = new ClasificacionData();
            $objClasificacion->sede = $_POST["sede"];
            $objClasificacion->nombre = strtoupper(trim($_POST["nombre"]));
            $objClasificacion->fecha_actualizacion = date("Y-m-d H:i:s");
            $objClasificacion->usuario_actualizacion = $_SESSION["user"];

            if ($_POST["id"] == 0) {
                $objClasificacion->estado = 1;
                $objClasificacion->fecha_creacion = date("Y-m-d H:i:s");
                $objClasificacion->usuario_creacion = $_SESSION["user"];
                
                $lstCategoria = ClasificacionData::getAllClasificacionRepetida(1, $objClasificacion->sede, $objClasificacion->nombre);
                if (count($lstCategoria) > 0) {
                    echo -1;
                } else { 
                    $resultado = $objClasificacion->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        echo $resultado[1];
                    } else {
                        echo 0;
                    }
                }
            } else {
                $objClasificacion->id = $_POST["id"];
                
                $lstCategoria = ClasificacionData::getAllClasificacionRepetida(1, $objClasificacion->sede, $objClasificacion->nombre, $objClasificacion->id);
                if (count($lstCategoria) > 0) {
                    echo -1;
                } else {
                    $resultado = $objClasificacion->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        echo $resultado[0];
                    } else {
                        echo 0;
                    }
                }
            }            
        }
        if ($_POST["accion"] == 2) {
            $objClasificacion = ClasificacionData::getById($_POST["id"]);
            $objClasificacion->estado = 0;
            $objClasificacion->fecha_actualizacion = date("Y-m-d H:i:s");
            $objClasificacion->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objClasificacion->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>