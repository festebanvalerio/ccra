<?php

$ruta = "/var/www/html/ccra/";

require_once ($ruta . "cpegeneracion/sunat/funciones.php");
require_once ($ruta . "cpegeneracion/sunat/toarray.php");
require_once ($ruta . "cpegeneracion/sunat/toxml - REAL.php");
require_once ($ruta . "cpeconfig/datos.php");
require_once ($ruta . "core/app/model/ComBajaData.php");
require_once ($ruta . "core/app/model/ComBajaDetalleData.php");
require_once ($ruta . "core/app/model/LogData.php");
require_once ($ruta . "core/controller/Executor.php");
require_once ($ruta . "core/controller/Database.php");
require_once ($ruta . "core/controller/Core.php");
require_once ($ruta . "core/controller/Model.php");

file_put_contents("baja" . date("Ymd") . ".log", "----------------------------------------------------------------\n", FILE_APPEND);
file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja - Inicio\n", FILE_APPEND);

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

$lstComBaja = ComBajaData::getAllResumen();
foreach ($lstComBaja as $objComBaja) {
        
    file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja - IDCOMBAJA : " . $objComBaja->fe_combaja_id . "\n", FILE_APPEND);
    
    $objComprobante = ComBajaData::getById($objComBaja->fe_combaja_id);
    $faucod = $objComprobante->fe_combaja_faucod;
    
    $header = array();
    $header[0]["issuedate"] = $objComprobante->fe_combaja_fec; // GENERACION DEL RESUMEN
    $header[0]["referencedate"] = $objComprobante->fe_combaja_fecref; // EMISION DOCUMENTOS
    $header[0]["id"] = $objComprobante->fe_combaja_cod; // CODIGO
    $header = json_decode(json_encode($header));
    
    $autoin = 0;
    
    $detalle = array();
    $lstDetalle = ComBajaDetalleData::getById($objComBaja->fe_combaja_id);
    foreach ($lstDetalle as $objDetalle) {
        $detalle[$autoin]["lineid"] = $objDetalle->fe_combajadetalle_num;
        $detalle[$autoin]["idcomprobante"] = $objDetalle->cs_tipodocumento_cod;
        $detalle[$autoin]["serie"] = $objDetalle->fe_combajadetalle_ser;
        $detalle[$autoin]["numero"] = $objDetalle->fe_combajadetalle_cor;
        $detalle[$autoin]["voidreasondescription"] = $objDetalle->fe_combajadetalle_mot;
    
        $autoin ++;
    }
    $detalle = json_decode(json_encode($detalle));
    
    file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja - FAUCOD : " . $faucod . "\n", FILE_APPEND);
    
    if ($faucod == "0") {
        $objLog = new LogData();
        $objLog->documento = "FACTURA";
        $objLog->indicador = $objComBaja->fe_combaja_id;
        $objLog->mensaje = "Comunicación de Baja ya enviado";
        $objLog->fecha_creacion = date("Y-m-d H:i:s");
        $objLog->usuario_creacion = $_SESSION["user"];
        $objLog->add();
    } else {
        $estado = false;
        $msj = $estado_envsun = "";
    
        $r = run(datatoarray($header, $detalle, $empresa, "VoidedDocuments"), $ruta . "cperepositorio/send/", $ruta . "cperepositorio/cdr/", "", "VoidedDocuments", true);
        file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja - FAULTCODE : " . $r["faultcode"] . "\n", FILE_APPEND);
        if ($r["faultcode"] == "0") {
            $estado = true;
            $estado_envsun = 1;
        } else {
            $msj = "Error: " . $r["faultcode"];
            $estado = false;
            $estado_envsun = 0;
        }
        file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja - TICKET : " . $r["ticket"] . "\n", FILE_APPEND);
        if (isset($r["ticket"]) && $r["ticket"] != "" && $estado) {
            $idComBaja = $objComBaja->fe_combaja_id;
            
            $objComData = new ComBajaData();
            $objComData->fe_combaja_tic = $r["ticket"];
            $objComData->fe_combaja_faucod = $r["faultcode"];
            $objComData->fe_combaja_digval = $r["digvalue"];
            $objComData->fe_combaja_sigval = $r["signvalue"];
            $objComData->fe_combaja_val = $r["valid"];
            $objComData->fe_combaja_fecenvsun = date("Y-m-d H:i:s");
            $objComData->fe_combaja_estsun = $estado_envsun;
            $objComData->fe_combaja_id = $idComBaja;
            $resultado = $objComData->updateSunat();
    
            file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja - FAUCOD : " . $r["faultcode"] . "\n", FILE_APPEND);
            file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja - DIGVAL : " . $r["digvalue"] . "\n", FILE_APPEND);
            file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja - SIGVAL : " . $r["signvalue"] . "\n", FILE_APPEND);
            file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja - VALID : " . $r["valid"] . "\n", FILE_APPEND);
            file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja - ESTADO1 : " . $estado_envsun . "\n", FILE_APPEND);
            file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja Actualizado 1 " . $resultado[1] . "\n", FILE_APPEND);
    
            $estado = false;
            $msj = $estado_envsun2 = "";
    
            $arr = array(
                "usuario_sunat" => $cpe_usuario_sunat,
                "clave_sunat" => $cpe_clave_sunat
            );
            $res = send_sunat("none", $r["ticket"], $arr, $ruta . "cperepositorio/cdr/", "getStatus", "");
            file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja - RES : " . $res . "\n", FILE_APPEND);
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
            file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja - ESTADO2 : " . $estado_envsun2 . "\n", FILE_APPEND);
    
            $objComData = new ComBajaData();
            $objComData->fe_combaja_faucod2 = $res;
            $objComData->fe_combaja_fecenvsun2 = date("Y-m-d H:i:s");
            $objComData->fe_combaja_estsun2 = $estado_envsun2;
            $objComData->fe_combaja_id = $idComBaja;
            $resultado = $objComData->updateSunat2();
    
            file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja Actualizado 2 " . $resultado[1] . "\n", FILE_APPEND);
    
            if (!$estado || $estado == "") {
                $objLog = new LogData();
                $objLog->documento = "FACTURA";
                $objLog->indicador = $idComBaja;
                $objLog->mensaje = $msj;
                $objLog->fecha_creacion = date("Y-m-d H:i:s");
                $objLog->usuario_creacion = $_SESSION["user"];
                $objLog->add();
            }
        } else {
            $objLog = new LogData();
            $objLog->documento = "FACTURA";
            $objLog->indicador = $objComBaja->fe_combaja_id;
            $objLog->mensaje = $msj;
            $objLog->fecha_creacion = date("Y-m-d H:i:s");
            $objLog->usuario_creacion = $_SESSION["user"];
            $objLog->add();
        }
    }    
}
file_put_contents("baja" . date("Ymd") . ".log", "Comunicacion Baja - Fin\n", FILE_APPEND);

?>