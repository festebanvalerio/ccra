<?php

if (count($_POST) > 0) {
    if (isset($_POST["opcion"]) && $_POST["opcion"] == "inits") {
        unset($_SESSION["inits_caja"]);
        unset($_SESSION["inits_estado"]);
        unset($_SESSION["inits_fechai"]);
        unset($_SESSION["inits_fechaf"]);
    }
    if (isset($_POST["opcion"]) && $_POST["opcion"] == "payments") {
        unset($_SESSION["payments_estado"]);
        unset($_SESSION["payments_fechai"]);
        unset($_SESSION["payments_fechaf"]);
    }
    if (isset($_POST["opcion"]) && $_POST["opcion"] == "products") {
        unset($_SESSION["products_estado"]);
        unset($_SESSION["products_categoria"]);
        unset($_SESSION["products_tipo"]);
    }
    if (isset($_POST["opcion"]) && $_POST["opcion"] == "rebates") {
        unset($_SESSION["rebates_estado"]);
        unset($_SESSION["rebates_fechai"]);
        unset($_SESSION["rebates_fechaf"]);
    }
    if (isset($_POST["opcion"]) && $_POST["opcion"] == "ocs") {
        unset($_SESSION["ocs_estado"]);
        unset($_SESSION["ocs_fechai"]);
        unset($_SESSION["ocs_fechaf"]);
    }
    if (isset($_POST["opcion"]) && $_POST["opcion"] == "trays") {
        unset($_SESSION["trays_tipo"]);
        unset($_SESSION["trays_piso"]);
        unset($_SESSION["trays_estado"]);
        unset($_SESSION["trays_fechai"]);
        unset($_SESSION["trays_fechaf"]);
        unset($_SESSION["trays_mesero"]);
    }
    if (isset($_POST["opcion"]) && $_POST["opcion"] == "adjustments") {
        unset($_SESSION["adjustments_estado"]);
        unset($_SESSION["adjustments_fechai"]);
        unset($_SESSION["adjustments_fechaf"]);
    }   
    if (isset($_POST["opcion"]) && $_POST["opcion"] == "transfers") {
        unset($_SESSION["transfers_estado"]);
        unset($_SESSION["transfers_fechai"]);
        unset($_SESSION["transfers_fechaf"]);
    }
    if (isset($_POST["opcion"]) && $_POST["opcion"] == "supplies") {
        unset($_SESSION["supplies_estado"]);
        unset($_SESSION["supplies_clasificacion"]);
    }
}

?>