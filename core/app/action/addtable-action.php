<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            $objMesa = new MesaData();
            $objMesa->sede = $_POST["sede"];
            $objMesa->nombre = strtoupper(trim($_POST["nombre"]));
            $objMesa->fecha_actualizacion = date("Y-m-d H:i:s");
            $objMesa->usuario_actualizacion = $_SESSION["user"];

            if ($_POST["id"] == 0) {
                $objMesa->estado = 1;
                $objMesa->fecha_creacion = date("Y-m-d H:i:s");
                $objMesa->usuario_creacion = $_SESSION["user"];
                
                $lstMesa = MesaData::getAllMesaRepetida(1, $objMesa->sede, $objMesa->nombre);
                if (count($lstMesa) > 0) {
                    echo -1;
                } else {
                    $resultado = $objMesa->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        echo $resultado[1];
                    } else {
                        echo 0;
                    }
                }
            } else {
                $objMesa->id = $_POST["id"];

                $lstMesa = MesaData::getAllMesaRepetida(1, $objMesa->sede, $objMesa->nombre, $objMesa->id);
                if (count($lstMesa) > 0) {
                    echo -1;
                } else {
                    $resultado = $objMesa->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        echo $resultado[0];
                    } else {
                        echo 0;
                    }
                }
            }            
        }
        if ($_POST["accion"] == 2) {
            $objMesa = MesaData::getById($_POST["id"]);
            $objMesa->estado = 0;
            $objMesa->fecha_actualizacion = date("Y-m-d H:i:s");
            $objMesa->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objMesa->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>