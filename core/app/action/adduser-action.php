<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            $objUsuario = new UsuarioData();
            $objUsuario->perfil = $_POST["perfil"];
            $objUsuario->sede = $_POST["sede"];
            $objUsuario->caja = 0;
            if (isset($_POST["caja"])) {
                $objUsuario->caja = $_POST["caja"];
            }
            $objUsuario->nombres = strtoupper(trim($_POST["nombres"]));
            $objUsuario->apellidos = strtoupper(trim($_POST["apellidos"]));
            $objUsuario->fecha_actualizacion = date("Y-m-d H:i:s");

            if ($_POST["id"] == 0) {
                $objUsuario->estado = 1;
                $objUsuario->fecha_creacion = date("Y-m-d H:i:s");
                
                $primerCaracter = substr($objUsuario->nombres, 0, 1);
                $arrApellidos = explode(" ", $objUsuario->apellidos);
                if (count($arrApellidos) == 2) {
                    $segundoCaracter = substr($arrApellidos[0], 0, 12);
                } else if (count($arrApellidos) > 2) {
                    $segundoCaracter = substr(($arrApellidos[0]."".$arrApellidos[1]), 0, 12);
                } else {
                    $segundoCaracter = substr($arrApellidos[0], 0, 12);
                }
                $username = trim(strtolower($primerCaracter . $segundoCaracter));
                
                $objUsuarioTmp = UsuarioData::getByUsername($username);
                if ($objUsuarioTmp->total > 0) {
                    if ($objUsuarioTmp->total == 0) {
                        $username = $username . "1";
                    } else {
                        $username = $username . ($objUsuarioTmp->total + 1);
                    }
                }
                $objUsuario->username = $username;
                $objUsuario->password = sha1(md5($username));
                $resultado = $objUsuario->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    echo $resultado[1];
                } else {
                    echo 0;
                }
            } else {
                $objUsuario->id = $_POST["id"];                
                $resultado = $objUsuario->update();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    echo $resultado[0];
                } else {
                    echo 0;
                }
            }            
        }
        if ($_POST["accion"] == 2) {
            $objUsuario = UsuarioData::getById($_POST["id"]);
            $objUsuario->estado = 0;
            $objUsuario->fecha_actualizacion = date("Y-m-d H:i:s");
            $resultado = $objUsuario->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>