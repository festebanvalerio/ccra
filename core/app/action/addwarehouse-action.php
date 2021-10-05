<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {
        if ($_POST["accion"] == 1) {
            $objAlmacen = new AlmacenData();
            $objAlmacen->empresa = $_SESSION["empresa"];
            $objAlmacen->sede = $_POST["sede"];
            $objAlmacen->nombre = strtoupper(trim($_POST["nombre"]));
            $objAlmacen->fecha_actualizacion = date("Y-m-d H:i:s");
            $objAlmacen->usuario_actualizacion = $_SESSION["user"];

            if ($_POST["id"] == 0) {
                $objAlmacen->estado = 1;
                $objAlmacen->fecha_creacion = date("Y-m-d H:i:s");
                $objAlmacen->usuario_creacion = $_SESSION["user"];
                
                $lstAlmacen = AlmacenData::getAllAlmacenRepetido(1, $objAlmacen->sede);
                if (count($lstAlmacen) > 0) {
                    echo -1;
                } else {
                    $resultado = $objAlmacen->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        echo $resultado[1];
                    } else {
                        echo 0;
                    }
                }
            } else {
                $objAlmacen->id = $_POST["id"];

                $lstAlmacen = AlmacenData::getAllAlmacenRepetido(1, $objAlmacen->sede, "", $objAlmacen->id);
                if (count($lstAlmacen) > 0) {
                    echo -1;
                } else {
                    $resultado = $objAlmacen->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        echo $resultado[0];
                    } else {
                        echo 0;
                    }
                }
            }
        }
        if ($_POST["accion"] == 2) {
            $objAlmacen = CategoriaData::getById($_POST["id"]);
            $objAlmacen->estado = 0;
            $objAlmacen->fecha_actualizacion = date("Y-m-d H:i:s");
            $objAlmacen->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objAlmacen->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>