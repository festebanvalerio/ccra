<?php
if (count($_POST) > 0) {
    if (isset($_POST["accion"])) {        
        if ($_POST["accion"] == 1) {
            $objCaja = new CajaData();
            $objCaja->sede = $_POST["sede"];
            $objCaja->piso = $_POST["piso"];
            $objCaja->nombre = strtoupper(trim($_POST["nombre"]));            
            $objCaja->fecha_actualizacion = date("Y-m-d H:i:s");
            $objCaja->usuario_actualizacion = $_SESSION["user"];

            if ($_POST["id"] == 0) {
                $objCaja->estado = 1;
                $objCaja->fecha_creacion = date("Y-m-d H:i:s");
                $objCaja->usuario_creacion = $_SESSION["user"];
                
                $lstCaja = CajaData::getAllCajaRepetida(1, $objCaja->sede, $objCaja->piso);
                if (count($lstCaja) > 0) {
                    echo -1;
                } else {
                    $resultado = $objCaja->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        echo $resultado[1];
                    } else {
                        echo 0;
                    }
                }
            } else {
                $objCaja->id = $_POST["id"];
                
                $lstCaja = CajaData::getAllCajaRepetida(1, $objCaja->sede, $objCaja->piso, "", $objCaja->id);
                if (count($lstCaja) > 0) {
                    echo -1;
                } else {
                    $resultado = $objCaja->update();
                    if (isset($resultado[0]) && $resultado[0] == 1) {
                        echo $resultado[0];
                    } else {
                        echo 0;
                    }
                }
            }            
        }
        if ($_POST["accion"] == 2) {
            $objCaja = CajaData::getById($_POST["id"]);
            $objCaja->estado = 0;
            $objCaja->fecha_actualizacion = date("Y-m-d H:i:s");
            $objCaja->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objCaja->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                mysqli_rollback(Database::getCon());
                echo 0;
            }
        }
        if ($_POST["accion"] == 3) {
            // Registrar apertura/cierre caja
            if ($_POST["historial"] == 0) {
                $objHistorialCaja = new HistorialCajaData();
                $objHistorialCaja->sede = $_POST["sede"];
                $objHistorialCaja->piso = $_POST["piso"];
                $objHistorialCaja->caja = $_POST["id"];
                $objHistorialCaja->estado = 1;
                $objHistorialCaja->fecha_apertura = date("Y-m-d H:i:s");
                $objHistorialCaja->monto_apertura = str_replace(",", "", $_POST["montoapertura"]);
                $objHistorialCaja->fecha_actualizacion = date("Y-m-d H:i:s");
                $objHistorialCaja->usuario_actualizacion = $_SESSION["user"];            
                $objHistorialCaja->fecha_creacion = date("Y-m-d H:i:s");
                $objHistorialCaja->usuario_creacion = $_SESSION["user"];
                $resultado = $objHistorialCaja->add();
                if (isset($resultado[1]) && $resultado[1] > 0) {
                    echo $resultado[1];
                } else {
                    echo 0;
                }
            } else {                
                mysqli_begin_transaction(Database::getCon());
                
                $error = 0;
                $objHistorialCaja = HistorialCajaData::getById($_POST["historial"]);
                $objHistorialCaja->fecha_cierre = date("Y-m-d H:i:s");
                $objHistorialCaja->monto_cierre = str_replace(",", "", $_POST["montocierre"]);
                $objHistorialCaja->estado = 2;
                $objHistorialCaja->fecha_actualizacion = date("Y-m-d H:i:s");
                $objHistorialCaja->usuario_actualizacion = $_SESSION["user"];
                $resultado = $objHistorialCaja->update();
                if (isset($resultado[0]) && $resultado[0] == 1) {
                    // Obtener los pagos considerados en el cierre
                    $lstHistorialPago = HistorialPagoData::getAllByCierre(1, $objHistorialCaja->caja, $_POST["fecha"], 0);
                    foreach ($lstHistorialPago as $objHistorialPago) {
                        $objHistorialPago->indicador_cierre = $objHistorialCaja->id;
                        $resultado = $objHistorialPago->update();
                        if (!(isset($resultado[0]) && $resultado[0] == 1)) {
                            $error = 1;
                            mysqli_rollback(Database::getCon());
                            echo 0;
                            break;
                        }
                    }
                    if ($error == 0) {
                        mysqli_commit(Database::getCon());
                        echo $resultado[0];
                    }
                } else {
                    mysqli_rollback(Database::getCon());
                    echo 0;
                }
            }
        }
        if ($_POST["accion"] == 4) {
            // Anular apertura caja
            $objHistorialCaja = HistorialCajaData::getById($_POST["id"]);
            $objHistorialCaja->estado = 0;
            $objHistorialCaja->fecha_actualizacion = date("Y-m-d H:i:s");
            $objHistorialCaja->usuario_actualizacion = $_SESSION["user"];
            $resultado = $objHistorialCaja->delete();
            if (isset($resultado[0]) && $resultado[0] == 1) {
                echo $resultado[0];
            } else {
                echo 0;
            }
        }
    }
}

?>