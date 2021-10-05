<?php
    $cadena = "";
    if ($_GET["opcion"] == 0) {
        $lstModulo = ModuloData::getSubModulo($_GET["modulo"]);
        if (count($lstModulo)) {    
            $cadena .= "<option value=''>-- SELECCIONE --</option>";
            foreach ($lstModulo as $objModulo) {        
                $cadena .= "<option value='".$objModulo->id."'>".$objModulo->nombre."</option>";
            }        
        }        
    } else if ($_GET["opcion"] == 1) {
        $idPerfil = $_GET["perfil"];
        $idModuloPadre = $_GET["moduloP"];
        $idModuloHijo = $_GET["moduloH"];
        
        // En caso agregue un modulo que no tiene hijos
        if ($idModuloHijo == "") {
            $lstModuloXPerfil = ModuloPerfilData::getAllByPerfil($idPerfil);
            foreach ($lstModuloXPerfil as $objModuloXPerfil) {
                if ($objModuloXPerfil->modulo == $idModuloPadre) {
                    $cadena = "existe";
                    break;
                }
            }
            if ($cadena == "") {
                $objModuloXPerfil = new ModuloPerfilData();
                $objModuloXPerfil->perfil = $idPerfil;
                $objModuloXPerfil->modulo = $idModuloPadre;
                $objModuloXPerfil->indicador = 0;
                $resultado = $objModuloXPerfil->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    $cadena = "ok";
                }
            }
        } else {
            $objModuloXPerfil = ModuloPerfilData::existModulo($idPerfil, $idModuloHijo, "1");
            if ($objModuloXPerfil) {
                $cadena = "existe";
            } else {
                $objModuloXPerfil = ModuloPerfilData::existModulo($idPerfil, $idModuloPadre, "0");
                if (!$objModuloXPerfil) {
                    $objModuloXPerfil = new ModuloPerfilData();
                    $objModuloXPerfil->perfil = $idPerfil;
                    $objModuloXPerfil->modulo = $idModuloPadre;
                    $objModuloXPerfil->indicador = 0;
                    $resultado = $objModuloXPerfil->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        $objModuloXPerfil->perfil = $idPerfil;
                        $objModuloXPerfil->modulo = $idModuloHijo;
                        $objModuloXPerfil->indicador = 1;
                        $resultado = $objModuloXPerfil->add();
                        if (isset($resultado[1]) > 0) {
                            $cadena = "ok";
                        } else {
                            $cadena = "error";
                        }
                    } else {
                        $cadena = "error";
                    }
                } else {
                    $objModuloXPerfil = new ModuloPerfilData();
                    $objModuloXPerfil->perfil = $idPerfil;
                    $objModuloXPerfil->modulo = $idModuloHijo;
                    $objModuloXPerfil->indicador = 1;
                    $resultado = $objModuloXPerfil->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        $cadena = "ok";
                    } else {
                        $cadena = "error";
                    }
                }
            }
        }
    } else if ($_GET["opcion"] == 2) {
        $id = $_GET["id"];
        $idPerfil = $_GET["perfil"];
        $idModulo = $_GET["modulo"];
        $indicador = $_GET["indicador"];
        
        // Si el módulo es padre        
        if ($indicador == 1) {
            $resultado = false;
            $lstModulo = ModuloPerfilData::getAllModuloHijos($idPerfil,$idModulo);
            if (count($lstModulo) > 0) {
                foreach ($lstModulo as $objModulo) {
                    // Elimino la relación de los modulos hijos
                    $objModuloXPerfil = new ModuloPerfilData();
                    $resultado = $objModuloXPerfil->deleteModulo($objModulo->id);
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        $resultado = true;
                    }
                }
            } else {
                $resultado = true;
            }
            if ($resultado) {
                // Elimino la relación del módulo padre
                $objModuloXPerfil = new ModuloPerfilData();
                $resultado = $objModuloXPerfil->deleteModulo($id);
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    echo "ok";
                } else {
                    echo "error";
                }
            } else {
                echo "error";
            }
        } else if ($indicador == 2) {
            // Si el módulo es hijo            
            $objModuloXPerfil = new ModuloPerfilData();
            $resultado = $objModuloXPerfil->deleteModulo($id);
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo "ok";
            } else {
                echo "error";
            }
        }
    }
    
    echo $cadena;
?>