<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            $objParametro = new ParametroData();
            $objParametro->tabla = $_POST["tabla"];
            $objParametro->nombre = strtoupper(trim($_POST["nombre"]));
            $objParametro->valor1 = $_POST["valor"];
            $objParametro->valor2 = "";
            $objParametro->valor3 = "";
            
            if ($_POST["id"] == 0) {
                $objParametro->estado = 1;
                $resultado = $objParametro->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    echo $resultado[1];
                } else {
                    echo 0;
                }
            } else {
                $objParametro->id = $_POST["id"];
                $resultado = $objParametro->update();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    echo $resultado[0];
                } else {
                    echo 0;
                }
            }            
        }
        if ($_POST["accion"] == 2) {
            $objParametro = ParametroData::getById($_POST["id"]);
            $objParametro->estado = 0;
            $resultado = $objParametro->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>