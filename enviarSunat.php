<?php

$idComprobante = "";
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $idComprobante = $_GET["id"];
    
    include "core/controller/Core.php";
    include "core/controller/Database.php";
    include "core/controller/Executor.php";
    include "core/controller/Model.php";
    
    include_once("sunat.php");
    
    Core::alert("Datos correctos, se procedió enviar a la SUNAT");
} else {
    Core::alert("Datos incorrectos, no se procederá enviar a la SUNAT");    
}

?>