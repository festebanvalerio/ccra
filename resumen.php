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

file_put_contents("resumen" . date("Ymd") . ".log", "----------------------------------------------------------------\n", FILE_APPEND);
file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta - Inicio\n", FILE_APPEND);

// EMPRESA
$empresa = array();
$empresa[0]["certificado"] = $cpe_certificado;
$empresa[0]["clave_certificado"] = $cpe_clave_certificado;
$empresa[0]["usuario_sunat"] = $cpe_usuario_sunat;
$empresa[0]["clave_sunat"] = $cpe_clave_sunat;
$empresa[0]["idempresa"] = $cpe_idempresa;
$empresa[0]["signature_id"] = $cpe_signature_id;
$empresa[0]["signature_id2"] = $cpe_signature_id2;
$empresa[0]["razon"] = $cpe_razon;
$empresa[0]["idtipodni"] = $cpe_idtipodni;
$empresa[0]["nomcomercial"] = $cpe_nomcomercial;
$empresa[0]["iddistrito"] = $cpe_iddistrito;
$empresa[0]["direccion"] = $cpe_direccion;
$empresa[0]["subdivision"] = $cpe_subdivision;
$empresa[0]["departamento"] = $cpe_departamento;
$empresa[0]["provincia"] = $cpe_provincia;
$empresa[0]["distrito"] = $cpe_distrito;
$empresa = json_decode(json_encode($empresa));

$lstResumenBoleta = ResumenBoletaData::getAllResumen();
foreach ($lstResumenBoleta as $objResumenBoleta) {
    file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta - IDBOLETA : " . $objResumenBoleta->fe_resumenboleta_id . "\n", FILE_APPEND);
    
    $objResumenBoleta = ResumenBoletaData::getById($objResumenBoleta->fe_resumenboleta_id);
    $faucod = $objResumenBoleta->fe_resumenboleta_faucod;
    if ($faucod == "") {
        $faucod = -1;
    }
    
    $header = array();
    $header[0]["issuedate"] = $objResumenBoleta->fe_resumenboleta_fec; // GENERACION DEL RESUMEN
    $header[0]["referencedate"] = $objResumenBoleta->fe_resumenboleta_fecref; // EMISION DOCUMENTOS
    $header[0]["id"] = $objResumenBoleta->fe_resumenboleta_cod; // CODIGO
    $header = json_decode(json_encode($header));
    
    $autoin = 0;
    
    $detalle = array();
    $lstDetalle = ResumenBoletaDetalleData::getById($objResumenBoleta->fe_resumenboleta_id);
    foreach ($lstDetalle as $objDetalle) {
        $detalle[$autoin]["nro"] = $objDetalle->fe_resumenboletadetalle_num;
        $detalle[$autoin]["idcomprobante"] = $objDetalle->cs_tipodocumento_cod;
        $detalle[$autoin]["serie"] = $objDetalle->fe_resumenboletadetalle_ser;
        $detalle[$autoin]["numero"] = $objDetalle->fe_resumenboletadetalle_cor;
        $detalle[$autoin]["idtipodni"] = $objDetalle->cs_tipodocumentoidentidad_cod;
        $detalle[$autoin]["identidad"] = $objDetalle->tb_cliente_numdoc;
        $detalle[$autoin]["conditioncode"] = $objDetalle->fe_resumenboletadetalle_est;
        $detalle[$autoin]["isomoneda"] = $objDetalle->cs_tipomoneda_cod;
        $detalle[$autoin]["totopgra"] = $objDetalle->fe_resumenboletadetalle_totvengra;
        $detalle[$autoin]["totopina"] = $objDetalle->fe_resumenboletadetalle_totvenina;
        $detalle[$autoin]["totopexo"] = $objDetalle->fe_resumenboletadetalle_totvenexo;
        $detalle[$autoin]["tototroca"] = $objDetalle->fe_resumenboletadetalle_sumotrcar;
        $detalle[$autoin]["totisc"] = $objDetalle->fe_resumenboletadetalle_sumisc;
        $detalle[$autoin]["totigv"] = $objDetalle->fe_resumenboletadetalle_sumigv;
        $detalle[$autoin]["importetotal"] = $objDetalle->fe_resumenboletadetalle_imptot;
        $detalle[$autoin]["documenttypecode"] = $objDetalle->fe_resumenboletadetalle_tipdocrel;
        $detalle[$autoin]["invoicedocumentreference"] = $objDetalle->fe_resumenboletadetalle_docrelser . "-" . $objDetalle->fe_resumenboletadetalle_docrelcor;
    
        $autoin ++;
    }
    $detalle = json_decode(json_encode($detalle));
    
    file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta - FAUCOD : " . $faucod . "\n", FILE_APPEND);
    
    if ($faucod == "0") {
        $objLog = new LogData();
        $objLog->documento = "BOLETA";
        $objLog->indicador = $objResumenBoleta->fe_resumenboleta_id;
        $objLog->mensaje = "Resumen ya enviado";
        $objLog->fecha_creacion = date("Y-m-d H:i:s");
        $objLog->usuario_creacion = $_SESSION["user"];
        $objLog->add();
    } else {
        $estado = false;
        $msj = $estado_envsun = "";
    
        $r = run(datatoarray($header, $detalle, $empresa, "SummaryDocuments"), $ruta . "cperepositorio/send/", $ruta . "cperepositorio/cdr/", "", "SummaryDocuments", true);
        file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta - FAULTCODE : " . $r["faultcode"] . "\n", FILE_APPEND);
        if ($r["faultcode"] == "0") {
            $estado = true;
            $estado_envsun = 1;
        } else {
            $msj = "Error: " . $r["faultcode"];
            $estado = false;
            $estado_envsun = 0;
        }
        file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta - TICKET : " . $r["ticket"] . "\n", FILE_APPEND);
        if ($r["ticket"] != "" && $estado) {
            $idResumenBoleta = $objResumenBoleta->fe_resumenboleta_id;
            
            $objResumenBoleta = new ResumenBoletaData();
            $objResumenBoleta->fe_resumenboleta_tic = $r["ticket"];
            $objResumenBoleta->fe_resumenboleta_faucod = $r["faultcode"];
            $objResumenBoleta->fe_resumenboleta_digval = $r["digvalue"];
            $objResumenBoleta->fe_resumenboleta_sigval = $r["signvalue"];
            $objResumenBoleta->fe_resumenboleta_val = $r["valid"];
            $objResumenBoleta->fe_resumenboleta_fecenvsun = date("Y-m-d H:i:s");
            $objResumenBoleta->fe_resumenboleta_estsun = $estado_envsun;
            $objResumenBoleta->fe_resumenboleta_id = $idResumenBoleta;
            $resultado = $objResumenBoleta->updateSunat();
    
            file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta - FAUCOD : " . $r["faultcode"] . "\n", FILE_APPEND);
            file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta - DIGVAL : " . $r["digvalue"] . "\n", FILE_APPEND);
            file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta - SIGVAL : " . $r["signvalue"] . "\n", FILE_APPEND);
            file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta - VALID : " . $r["valid"] . "\n", FILE_APPEND);
            file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta - ESTADO1 : " . $estado_envsun . "\n", FILE_APPEND);
            file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta Actualizado 1 : " . $resultado[0] . "\n", FILE_APPEND);
    
            $estado = false;
            $msj = $estado_envsun2 = "";
    
            $arr = array(
                "usuario_sunat" => $cpe_usuario_sunat,
                "clave_sunat" => $cpe_clave_sunat
            );
            $res = send_sunat("none", $r["ticket"], $arr, $ruta . "cperepositorio/cdr/", "getStatus", "");
            if ($res == "0") {
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
            file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta - RES : " . $res . "\n", FILE_APPEND);
            file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta - ESTADO2 : " . $estado_envsun2 . "\n", FILE_APPEND);

            $objResumenBoleta = new ResumenBoletaData();
            $objResumenBoleta->fe_resumenboleta_faucod2 = $res;
            $objResumenBoleta->fe_resumenboleta_fecenvsun2 = date("Y-m-d H:i:s");
            $objResumenBoleta->fe_resumenboleta_estsun2 = $estado_envsun2;
            $objResumenBoleta->fe_resumenboleta_id = $idResumenBoleta;
            $resultado = $objResumenBoleta->updateSunat2();
    
            file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta Actualizado 2 : " . $resultado[0] . "\n", FILE_APPEND);
    
            if (!$estado || $estado == "") {
                $objLog = new LogData();
                $objLog->documento = "BOLETA";
                $objLog->indicador = $objResumenBoleta->fe_resumenboleta_id;
                $objLog->mensaje = $msj;
                $objLog->fecha_creacion = date("Y-m-d H:i:s");
                $objLog->usuario_creacion = $_SESSION["user"];
                $objLog->add();
            }
        } else {
            $objLog = new LogData();
            $objLog->documento = "BOLETA";
            $objLog->indicador = $objResumenBoleta->fe_resumenboleta_id;
            $objLog->mensaje = $msj;
            $objLog->fecha_creacion = date("Y-m-d H:i:s");
            $objLog->usuario_creacion = $_SESSION["user"];
            $objLog->add();
        }
    }
}
file_put_contents("resumen" . date("Ymd") . ".log", "Resumen Boleta - Fin\n", FILE_APPEND);

?>
