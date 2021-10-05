<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            $objSede = new SedeData();
            $objSede->nombre = strtoupper(trim($_POST["nombre"]));
            $objSede->direccion = strtoupper(trim($_POST["direccion"]));
            $objSede->fecha_actualizacion = date("Y-m-d H:i:s");
            $objSede->usuario_actualizacion = $_SESSION["user"];

            if ($_POST["id"] == 0) {
                $objSede->estado = 1;
                $objSede->fecha_creacion = date("Y-m-d H:i:s");
                $objSede->usuario_creacion = $_SESSION["user"];
                $resultado = $objSede->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    $idSede = $resultado[1];
                    $lstPisoXSede = $_POST["piso"];
                    if (count($lstPisoXSede) > 0) {
                        $objPisoSede = new PisoSedeData();
                        $objPisoSede->sede = $idSede;
                        $resultado = $objPisoSede->delete();
                        if (isset($resultado[0]) && $resultado[0] == 1) {
                            for ($index=0;$index<count($lstPisoXSede);$index++) {
                                $objPisoSede->sede = $idSede;
                                $objPisoSede->piso = $lstPisoXSede[$index];
                                $resultado = $objPisoSede->add();
                            }
                            echo $resultado[0];
                        } else {
                            echo 0;
                        }
                    } else {
                        echo 0;
                    }
                } else {
                    echo 0;
                }
            } else {
                $objSede->id = $_POST["id"];
                $resultado = $objSede->update();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    $lstPisoXSede = $_POST["piso"];
                    if (count($lstPisoXSede) > 0) {
                        $objPisoSede = new PisoSedeData();
                        $objPisoSede->sede = $_POST["id"];
                        $resultado = $objPisoSede->delete();
                        if (isset($resultado[0]) && $resultado[0] == 1) {
                            
                            for ($index=0;$index<count($lstPisoXSede);$index++) {
                                $objPisoSede->sede = $_POST["id"];
                                $objPisoSede->piso = $lstPisoXSede[$index];
                                $resultado = $objPisoSede->add();
                            }
                            echo $resultado[0];
                        } else {
                            echo 0;
                        }
                    } else {
                        echo 0;
                    }
                } else {
                    echo 0;
                }
            }            
        }
        if ($_POST["accion"] == 2) {
            $objSede = SedeData::getById($_POST["id"]);
            $objSede->estado = 0;
            $objSede->fecha_actualizacion = date("Y-m-d H:i:s");
            $objSede->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objSede->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
        if ($_POST["accion"] == 3) {            
            $lstMesaXPisoXSede = $_POST["mesa"];
            if (count($lstMesaXPisoXSede) > 0) {
                $objMesaPisoSede = new MesaPisoSedeData();
                $objMesaPisoSede->piso_sede = $_POST["piso"];
                $resultado = $objMesaPisoSede->delete();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    for ($index=0;$index<count($lstMesaXPisoXSede);$index++) {
                        $objMesaPisoSede->piso_ede = $_POST["piso"];
                        $objMesaPisoSede->mesa = $lstMesaXPisoXSede[$index];
                        $resultado = $objMesaPisoSede->add();
                    }
                    echo $resultado[0];
                } else {
                    echo 0;
                }
            } else {
                echo 0;
            }            
        } else {
            echo 0;
        }
        if ($_POST["accion"] == 4) {
            $lstAreaXPisoXSede = $_POST["area"];
            if (count($lstAreaXPisoXSede) > 0) {
                $objAreaPisoSede = new AreaPisoSedeData();
                $objAreaPisoSede->piso_sede = $_POST["piso"];
                $resultado = $objAreaPisoSede->delete();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    for ($index=0;$index<count($lstAreaXPisoXSede);$index++) {
                        $objAreaPisoSede->piso_sede = $_POST["piso"];
                        $objAreaPisoSede->area = $lstAreaXPisoXSede[$index];
                        $resultado = $objAreaPisoSede->add();
                    }
                    echo $resultado[0];
                } else {
                    echo 0;
                }
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    }
}

?>