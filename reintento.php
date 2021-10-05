<?php

$ruta = "/var/www/html/ccra/";

require_once ($ruta . "cpegeneracion/sunat/funciones.php");
require_once ($ruta . "cpegeneracion/sunat/toarray.php");
require_once ($ruta . "cpegeneracion/sunat/toxml - REAL.php");
require_once ($ruta . "cpeconfig/datos.php");
require_once ($ruta . "core/app/model/ResumenBoletaData.php");
require_once ($ruta . "core/app/model/ResumenBoletaDetalleData.php");
require_once ($ruta . "core/app/model/LogData.php");
require_once ($ruta . "core/controller/Executor.php");
require_once ($ruta . "core/controller/Database.php");
require_once ($ruta . "core/controller/Core.php");
require_once ($ruta . "core/controller/Model.php");

file_put_contents("reintento" . date("Ymd") . ".log", "----------------------------------------------------------------\n", FILE_APPEND);
file_put_contents("reintento" . date("Ymd") . ".log", "Reintento Resumen Boleta - Inicio\n", FILE_APPEND);

$idResumenBoleta = 7;

$r = array();
$r["ticket"] = "202107172649678";

$arr = array(
    "usuario_sunat" => $cpe_usuario_sunat,
    "clave_sunat" => $cpe_clave_sunat
);

$estado_envsun2 = 0;
$estado = true;
$msj = "";

$res = send_sunat("none", $r["ticket"], $arr, $ruta . "cperepositorio/cdr/", "getStatus", "");
if ($res == "0") {
    $msj = "OK";
    $estado_envsun2 = 1;    
    $estado = true;
} elseif ($res == "98") {
    $msj = "EN PROCESO.";
    $estado_envsun2 = 1;
    $estado = "";
} elseif ($res == "99") {
    $msj = "PROCESO CON ERRORES.";
    $estado_envsun2 = 1;
    $estado = "";
} else {
    $msj = "CODE: " . $res;
    $estado_envsun2 = 0;
    $estado = false;
}
file_put_contents("reintento" . date("Ymd") . ".log", "Reintento Resumen Boleta - RES : " . $res . "\n", FILE_APPEND);
file_put_contents("reintento" . date("Ymd") . ".log", "Reintento Resumen Boleta - MENSAJE : " . $msj . "\n", FILE_APPEND);
file_put_contents("reintento" . date("Ymd") . ".log", "Reintento Resumen Boleta - ESTADO : " . $estado . "\n", FILE_APPEND);
file_put_contents("reintento" . date("Ymd") . ".log", "Reintento Resumen Boleta - ESTADO2 : " . $estado_envsun2 . "\n", FILE_APPEND);

$objResumenBoleta = new ResumenBoletaData();
$objResumenBoleta->fe_resumenboleta_faucod2 = $res;
$objResumenBoleta->fe_resumenboleta_fecenvsun2 = date("Y-m-d H:i:s");
$objResumenBoleta->fe_resumenboleta_estsun2 = $estado_envsun2;
$objResumenBoleta->fe_resumenboleta_id = $idResumenBoleta;
$resultado = $objResumenBoleta->updateSunat2();
    
file_put_contents("reintento" . date("Ymd") . ".log", "Reintento Resumen Boleta Actualizado : " . $resultado[0] . "\n", FILE_APPEND);
file_put_contents("reintento" . date("Ymd") . ".log", "Reintento Resumen Boleta - Fin\n", FILE_APPEND);
file_put_contents("reintento" . date("Ymd") . ".log", "----------------------------------------------------------------\n", FILE_APPEND);

?>