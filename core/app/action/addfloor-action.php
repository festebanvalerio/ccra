<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {
        if ($_POST["accion"] == 1) {
            $objPiso = new PisoData();
            $objPiso->sede = $_POST["sede"];
            $objPiso->nombre = strtoupper(trim($_POST["nombre"]));
            $objPiso->indicador = $_POST["indicador"];
            $objPiso->fecha_actualizacion = date("Y-m-d H:i:s");
            $objPiso->usuario_actualizacion = $_SESSION["user"];

            if ($_POST["id"] == 0) {
                $objPiso->estado = 1;
                $objPiso->fecha_creacion = date("Y-m-d H:i:s");
                $objPiso->usuario_creacion = $_SESSION["user"];
                
                $lstPiso = PisoData::getAllPisoRepetido(1, $objPiso->sede, $objPiso->nombre);
                if (count($lstPiso) > 0) {
                    echo -1;
                } else {
                    $resultado = $objPiso->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        $idPiso = $resultado[1];
                        $objPisoSede = new PisoSedeData();
                        $objPisoSede->piso = $idPiso;
                        $objPisoSede->sede = $_POST["sede"];
                        $resultado = $objPisoSede->add();
                        if (isset($resultado[1]) && $resultado[1] > 0) {
                            echo $resultado[1];
                        } else {
                            echo 0;
                        }
                    } else {
                        echo 0;
                    }
                }
            } else {
                $objPiso->id = $_POST["id"];

                $lstPiso = PisoData::getAllPisoRepetido(1, $objPiso->sede, $objPiso->nombre, $objPiso->id);
                if (count($lstPiso) > 0) {
                    echo -1;
                } else {
                    $resultado = $objPiso->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        echo $resultado[0];
                    } else {
                        echo 0;
                    }
                }
            }
        }
        if ($_POST["accion"] == 2) {
            $objPiso = PisoData::getById($_POST["id"]);
            $objPiso->estado = 0;
            $objPiso->fecha_actualizacion = date("Y-m-d H:i:s");
            $objPiso->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objPiso->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>