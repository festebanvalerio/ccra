<?php

if (count($_POST) > 0) {        
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            $objPerfil = new PerfilData();
            $objPerfil->nombre = strtoupper(trim($_POST["nombre"]));
            $objPerfil->indicador = $_POST["indicador"];
            $objPerfil->fecha_actualizacion = date("Y-m-d H:i:s");
            $objPerfil->usuario_actualizacion = $_SESSION["user"];
            
            if ($_POST["id"] == 0) {
                $objPerfil->estado = 1;
                $objPerfil->fecha_creacion = date("Y-m-d H:i:s");
                $objPerfil->usuario_creacion = $_SESSION["user"];
                
                $lstPerfil = PerfilData::getAllPerfilRepetido(1, $objPerfil->nombre);
                if (count($lstPerfil) > 0) {
                    echo -1;
                } else {
                    $resultado = $objPerfil->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        echo $resultado[1];
                    } else {
                        echo 0;
                    }
                }
            } else {
                $objPerfil->id = $_POST["id"];
                
                $lstPerfil = PerfilData::getAllPerfilRepetido(1, $objPerfil->nombre, $objPerfil->id);
                if (count($lstPerfil) > 0) {
                    echo -1;
                } else {
                    $resultado = $objPerfil->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        echo $resultado[0];
                    }  else {
                        echo 0;
                    }
                }
            }
        }
        if ($_POST["accion"] == 2) {
            $objPerfil = PerfilData::getById($_POST["id"]);
            $objPerfil->estado = 0;
            $objPerfil->fecha_actualizacion = date("Y-m-d H:i:s");
            $objPerfil->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objPerfil->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>