<?php
    $orden = 1;
    $idModuloPadre = $icono = $nombre = $url = "";
    $texto = "Registrar Módulo";
    $lstModulo = ModuloData::getAllPrincipal();
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
    
        $objModulo = ModuloData::getById($id);
        $idModuloPadre = $objModulo->id_padre;
        $icono = $objModulo->icono;
        $nombre = $objModulo->nombre;
        $url = $objModulo->url;
        if ($url != "") {
            $url = str_replace("./index.php?view=", "", $url);
        }
        $orden = $objModulo->orden;
    }