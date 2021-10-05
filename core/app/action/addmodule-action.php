<?php

if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            if ($_POST["id"] == 0) {
                $objModulo = new ModuloData();
                $objModulo->id_padre = 0;
                $objModulo->url = "";
                if ($_POST["modulo"] != "") {   
                    $objModulo->id_padre = $_POST["modulo"];
                    $objModulo->url = "./index.php?view=".strtolower(trim($_POST["url"]));
                }
                $objModulo->icono = "<i class='fa ".trim($_POST["icono"])."'></i>";
                $objModulo->nombre = strtoupper(trim($_POST["nombre"]));
                $objModulo->orden = $_POST["orden"];
                $objModulo->estado = 1;
                $objModulo->fecha_creacion = date("Y-m-d H:i:s");
                $objModulo->usuario_creacion = $_SESSION["user"];
                $objModulo->fecha_actualizacion = date("Y-m-d H:i:s");
                $objModulo->usuario_actualizacion = $_SESSION["user"];
                $resultado = $objModulo->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    echo $resultado[1];
                } else {
                    echo 0;
                }
            } else {
                $objModulo = ModuloData::getById($_POST["id"]);
                if ($_POST["modulo"] != "") {
                    $objModulo->id_padre = $_POST["modulo"];
                    $objModulo->url = "./index.php?view=".strtolower(trim($_POST["url"]));
                } else {
                    $objModulo->id_padre = 0;
                    $objModulo->url = "";
                }
                $objModulo->icono = "<i class='fa ".trim($_POST["icono"])."'></i>";
                $objModulo->nombre = strtoupper(trim($_POST["nombre"]));
                $objModulo->orden = $_POST["orden"];
                $objModulo->fecha_actualizacion = date("Y-m-d H:i:s");
                $objModulo->usuario_actualizacion = $_SESSION["user"];
                $resultado = $objModulo->update();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    echo $resultado[0];
                } else {
                    echo 0;
                }                
            }
        }
        if ($_POST["accion"] == 2) {
            $objModulo = ModuloData::getById($_POST["id"]);
            
            // Si es módulo hijo
            if ($objModulo->id_padre > 0) {
                $objModulo->estado = 0;
                $objModulo->fecha_actualizacion = date("Y-m-d H:i:s");
                $objModulo->usuario_actualizacion = $_SESSION["user"];
                $resultado = $objModulo->delete();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    echo $resultado[0];
                } else {
                    echo 0;
                }
            } else {
                $lstModulo = ModuloData::getSubModulo($objModulo->id);
                if (count($lstModulo) > 0) {
                    foreach ($lstModulo as $objModuloHijo) {
                        $objModuloHijo->estado = 0;
                        $objModuloHijo->fecha_actualizacion = date("Y-m-d H:i:s");
                        $objModuloHijo->usuario_actualizacion = $_SESSION["user"];
                        $resultado = $objModuloHijo->delete();
                    }
                }
                $objModulo->estado = 0;
                $objModulo->fecha_actualizacion = date("Y-m-d H:i:s");
                $objModulo->usuario_actualizacion = $_SESSION["user"];
                $resultado = $objModulo->delete();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    echo $resultado[0];
                } else {
                    echo 0;
                }
            }
        }
    }
}

?>