<?php

if (!isset($_SESSION["user_id"])) {
    if (count($_POST) > 0) {
        $usuario = $_POST["username"];
        $password = sha1(md5($_POST["password"]));
        
        $objUsuario = UsuarioData::validate($usuario, $password);
        if ($objUsuario) {
            if ($objUsuario->estado == 1) {
                $_SESSION['user'] = $objUsuario->id;
                $_SESSION["perfil"] = $objUsuario->perfil;
                $_SESSION["sede"] = $objUsuario->sede;
                $_SESSION["nom_sede"] = $objUsuario->nom_sede;
                $_SESSION["caja"] = $objUsuario->caja;
                $_SESSION["empresa"] = $objUsuario->empresa;
                $_SESSION["ruc"] = $objUsuario->ruc;
                
                $lstParametro = ParametroData::getAll(1, "OPCIONES GENERALES", "EXONERADO");                
                $_SESSION["exonerado"] = $lstParametro[0]->valor1;
                
                $lstParametro = ParametroData::getAll(1, "OPCIONES GENERALES", "FACT. ELECTRONICA");
                $_SESSION["factel"] = $lstParametro[0]->valor1;
                
                $objUsuario->fecha_ultimo_ingreso = date("Y-m-d H:i:s");
                $objUsuario->updateAccess();
                
                $objPerfil = PerfilData::getById($objUsuario->perfil);
                if ($objPerfil->indicador == 0) {
                    print "<script>window.location='index.php?view=home';</script>";
                } else if ($objPerfil->indicador == 1) {
                    print "<script>window.location='index.php?view=trays';</script>";
                } else if ($objPerfil->indicador == 2 || $objPerfil->indicador == 5) {
                    print "<script>window.location='index.php?view=sales';</script>";
                } else if ($objPerfil->indicador == 3) {
                    print "<script>window.location='index.php?view=stocks';</script>";
                } else if ($objPerfil->indicador == 4) {
                    print "<script>window.location='index.php?view=report2';</script>";
                }
            } else {
                print "<script>window.location='index.php?view=login&error=1';</script>";
            }
        } else {
            print "<script>window.location='index.php?view=login&error=0';</script>";
        }
    } else {
        print "<script>window.location='index.php?view=login';</script>";
    }
} else {
    print "<script>window.location='index.php?view=home';</script>";
}

?>