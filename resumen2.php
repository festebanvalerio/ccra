<?php

$ruta = "/var/www/html/ccra/";

require_once ($ruta . "cpegeneracion/sunat/funciones.php");
require_once ($ruta . "cpegeneracion/sunat/toarray.php");
require_once ($ruta . "cpegeneracion/sunat/toxml - REAL.php");
require_once ($ruta . "cpeconfig/datos.php");
require_once ($ruta . "core/app/model/ComprobanteData.php");
require_once ($ruta . "core/app/model/ResumenBoletaData.php");
require_once ($ruta . "core/app/model/ResumenBoletaDetalleData.php");
require_once ($ruta . "core/app/model/LogData.php");
require_once ($ruta . "core/controller/Executor.php");
require_once ($ruta . "core/controller/Database.php");
require_once ($ruta . "core/controller/Core.php");
require_once ($ruta . "core/controller/Model.php");

echo "----------------------------------------------------------------\n";
echo "PROCESO ENVIO BOLETA - INICIO " . date("d/m/Y H:i") . "\n";

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

$fechaActual = date("d/m/Y");
$generarBoleta = true;
while ($generarBoleta) {
    $contador = 0;
    $lstDocumento = ComprobanteData::getAllSummaryByFecha($fechaActual);
    if (count($lstDocumento) > 0) {
        foreach ($lstDocumento as $objDocumento) {
            $lstResumenBoletaDetalle = ResumenBoletaDetalleData::getDetalle($objDocumento->fe_comprobante_id);
            if (count($lstResumenBoletaDetalle) == 0) {
                $contador++;
                echo "PROCESO ENVIO BOLETA - BOLETA : " . $objDocumento->fe_comprobante_ser . "-" . $objDocumento->fe_comprobante_cor . "(REGISTRADO)\n";
            } else if (count($lstResumenBoletaDetalle) == 1) {
                if ($objDocumento->fe_comprobante_est == 2) {
                    $contador++;
                    echo "PROCESO ENVIO BOLETA - BOLETA : " . $objDocumento->fe_comprobante_ser . "-" . $objDocumento->fe_comprobante_cor . "(ANULADO)\n";
                }
            }
        }
    }
    
    if ($contador > 0) {
        $fechaBoleta = $numero = $codigo = "";
        $objResumenBoleta = ResumenBoletaData::getLastId($fechaActual);
        if ($objResumenBoleta) {
            $arrFecha = explode("/", $fechaActual);
            $fechaBoleta = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
            
            $numero = $objResumenBoleta->ultimo_numero + 1;
            $codigo = "RC-".str_replace("-", "", $fechaBoleta)."-".str_pad($numero, 3, "0", STR_PAD_LEFT);
            
            echo "PROCESO ENVIO BOLETA - CODIGO RESUMEN BOLETA : " . $codigo . "\n";
        } else {
            $arrFecha = explode("/", $fechaActual);
            $fechaBoleta = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
            
            $numero = 1;
            $codigo = "RC-".str_replace("-", "", $fechaBoleta)."-".str_pad($numero, 3, "0", STR_PAD_LEFT);
            
            echo "PROCESO ENVIO BOLETA - CODIGO RESUMEN BOLETA : " . $codigo . "\n";
        }
        $arrFecha = explode("/", $fechaActual);
        $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
        
        $objResumenBoleta = new ResumenBoletaData();
        $objResumenBoleta->fe_resumenboleta_reg = date("Y-m-d H:i:s");
        $objResumenBoleta->fe_resumenboleta_usureg = 1;
        $objResumenBoleta->fe_resumenboleta_fec = $fechaBoleta;
        $objResumenBoleta->fe_resumenboleta_fecref = $fecha;
        $objResumenBoleta->fe_resumenboleta_cod = $codigo;
        $objResumenBoleta->fe_resumenboleta_num = $numero;
        $resultado = $objResumenBoleta->add();
        if (isset($resultado[1]) && $resultado[1] > 0) {
            $idResumenBoleta = $resultado[1];
    
            echo "PROCESO ENVIO BOLETA - CREAR RESUMEN BOLETA : " . $idResumenBoleta . "\n";                
            
            $item = 0;
            foreach ($lstDocumento as $objDocumento) {
                $estado = "";
                $insertar = 0;
                
                $lstResumenBoletaDetalle = ResumenBoletaDetalleData::getDetalle($objDocumento->fe_comprobante_id);
                if (count($lstResumenBoletaDetalle) == 0) {
                    $item++;
                    $estado = 1;
                    $insertar = 1;
                }
                if (count($lstResumenBoletaDetalle) == 1) {
                    if ($objDocumento->fe_comprobante_est == 2) {
                        $item++;
                        $estado = 3;
                        $insertar = 1;
                    }
                }
                
                if ($insertar == 1) {
                    List($docrelser, $docrelcor) = explode("-", $objDocumento->tb_notacredeb_numdoc);
                    
                    $objResumenBoletaDetalle = new ResumenBoletaDetalleData();
                    $objResumenBoletaDetalle->fe_resumenboleta_id = $idResumenBoleta;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_num = $item;
                    $objResumenBoletaDetalle->cs_tipodocumento_cod = $objDocumento->cs_tipodocumento_cod;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_ser = $objDocumento->fe_comprobante_ser;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_cor = $objDocumento->fe_comprobante_cor;
                    $objResumenBoletaDetalle->cs_tipodocumentoidentidad_cod = $objDocumento->cs_tipodocumentoidentidad_cod;
                    $objResumenBoletaDetalle->tb_cliente_numdoc = $objDocumento->tb_cliente_numdoc;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_tipdocrel = $objDocumento->tb_notacredeb_tipdoc;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_docrelser = $docrelser;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_docrelcor = $docrelcor;
                    $objResumenBoletaDetalle->cs_tipomoneda_cod = $objDocumento->cs_tipomoneda_cod;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_totvengra = $objDocumento->fe_comprobante_totvengra;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_totvenina = $objDocumento->fe_comprobante_totvenina;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_totvenexo = $objDocumento->fe_comprobante_totvenexo;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_totvengratui = $objDocumento->fe_comprobante_totvengratui;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_sumotrcar = $objDocumento->fe_comprobante_sumotrcar;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_sumisc = $objDocumento->fe_comprobante_sumisc;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_sumigv = $objDocumento->fe_comprobante_sumigv;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_sumotrtri = $objDocumento->fe_comprobante_sumotrtri;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_imptot = $objDocumento->fe_comprobante_imptot;
                    $objResumenBoletaDetalle->fe_comprobante_id = $objDocumento->fe_comprobante_id;
                    $objResumenBoletaDetalle->fe_resumenboletadetalle_est = $estado;
                    $resultado = $objResumenBoletaDetalle->add();
                    if (isset($resultado[1]) && $resultado[1] > 0) {
                        $idResumenBoletaDetalle = $resultado[1];
                        
                        echo "PROCESO ENVIO BOLETA - CREAR RESUMEN BOLETA DETALLE : " . $idResumenBoletaDetalle . " - COMPROBANTE : " . $objResumenBoletaDetalle->fe_comprobante_id . "\n";
                    } else {
                        echo "PROCESO ENVIO BOLETA - ERROR CREAR RESUMEN BOLETA DETALLE - COMPROBANTE : " . $objResumenBoletaDetalle->fe_comprobante_id . "\n";
                    }
                }
                //solo envia 500 por lote
                if ($item == 500) {
                    break;
                }
            }            
            
            $objResumenBoleta = ResumenBoletaData::getById($idResumenBoleta);
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
            
            echo "PROCESO ENVIO BOLETA - FAUCOD : " . $faucod . "\n";
            
            if ($faucod == "0") {
                $objLog = new LogData();
                $objLog->documento = "BOLETA";
                $objLog->indicador = $objResumenBoleta->fe_resumenboleta_id;
                $objLog->mensaje = "Resumen ya enviado";
                $objLog->fecha_creacion = date("Y-m-d H:i:s");
                $objLog->usuario_creacion = 1;
                $resultadoLog = $objLog->add();
                if (isset($resultadoLog[1]) && $resultadoLog[1] > 0) {
                    echo "PROCESO ENVIO BOLETA - CREAR LOG : " . $resultadoLog[1] . "\n";
                } else {
                    echo "PROCESO ENVIO BOLETA - ERROR CREAR LOG\n";
                }
            } else {
                $estado = false;
                $msj = $estado_envsun = "";

                $indicadorEnvioSunat = true;
                $recorridoEnvioSunat = 1;
                while ($indicadorEnvioSunat && $recorridoEnvioSunat <= 5) {                
                    $r = run(datatoarray($header, $detalle, $empresa, "SummaryDocuments"), $ruta . "cperepositorio/send/", $ruta . "cperepositorio/cdr/", "", "SummaryDocuments", true);
                    echo "PROCESO ENVIO BOLETA - FAULTCODE : " . $r["faultcode"] . "\n";
                    if ($r["faultcode"] == "0") {
                        $estado = true;
                        $estado_envsun = 1;
                        $indicadorEnvioSunat = false;
                    } else {
                        $msj = "Error: " . $r["faultcode"];
                        $estado_envsun = 0;
                    }
                    echo "PROCESO ENVIO BOLETA - INTENTO : " . $recorridoEnvioSunat++ . "\n";
                    echo "PROCESO ENVIO BOLETA - TICKET : " . $r["ticket"] . "\n";
                    if ($r["ticket"] != "" && $estado) {
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
                        
                        echo "PROCESO ENVIO BOLETA - FAUCOD : " . $r["faultcode"] . "\n";
                        echo "PROCESO ENVIO BOLETA - DIGVAL : " . $r["digvalue"] . "\n";
                        echo "PROCESO ENVIO BOLETA - SIGVAL : " . $r["signvalue"] . "\n";
                        echo "PROCESO ENVIO BOLETA - VALID : " . $r["valid"] . "\n";
                        echo "PROCESO ENVIO BOLETA - ESTADO1 : " . $estado_envsun . "\n";
                        echo "PROCESO ENVIO BOLETA ACTUALIZADO 1 : " . $resultado[0] . "\n";
                        
                        $estado = false;
                        $msj = $estado_envsun2 = "";
                        $indicador = true;
                        
                        $arr = array(
                                "usuario_sunat" => $cpe_usuario_sunat,
                                "clave_sunat" => $cpe_clave_sunat
                        );
                        $recorrido = 1;
                        while ($indicador && $recorrido <= 5) {
                            $res = send_sunat("none", $r["ticket"], $arr, $ruta . "cperepositorio/cdr/", "getStatus", "");
                            if ($res == "0") {
                                $msj = "OK";
                                $estado_envsun2 = 1;
                                $estado = true;
                                $indicador = false;
                            } elseif ($res == "98") {
                                $msj = "EN PROCESO.";
                                $estado_envsun2 = 1;                                
                            } elseif ($res == "99") {
                                $msj = "PROCESO CON ERRORES.";
                                $estado_envsun2 = 1;
                            } else {
                                $msj = "CODE: " . $res;
                                $estado_envsun2 = 0;
                            }
                            echo "PROCESO ENVIO BOLETA - INTENTO : " . $recorrido++ . "\n";
                            echo "PROCESO ENVIO BOLETA - RES : " . $res . "\n";
                            echo "PROCESO ENVIO BOLETA - MENSAJE : " . $msj . "\n";
                            echo "PROCESO ENVIO BOLETA - ESTADO2 : " . $estado_envsun2 . "\n";
                        }
                        
                        $objResumenBoleta = new ResumenBoletaData();
                        $objResumenBoleta->fe_resumenboleta_faucod2 = $res;
                        $objResumenBoleta->fe_resumenboleta_fecenvsun2 = date("Y-m-d H:i:s");
                        $objResumenBoleta->fe_resumenboleta_estsun2 = $estado_envsun2;
                        $objResumenBoleta->fe_resumenboleta_id = $idResumenBoleta;
                        $resultado = $objResumenBoleta->updateSunat2();
                        
                        echo "PROCESO ENVIO BOLETA ACTUALIZADO 2 : " . $resultado[0] . "\n";
                        
                        if (!$estado) {
                            $objLog = new LogData();
                            $objLog->documento = "BOLETA";
                            $objLog->indicador = $objResumenBoleta->fe_resumenboleta_id;
                            $objLog->mensaje = $msj;
                            $objLog->fecha_creacion = date("Y-m-d H:i:s");
                            $objLog->usuario_creacion = 1;
                            $resultadoLog = $objLog->add();
                            if (isset($resultadoLog[1]) && $resultadoLog[1] > 0) {
                                echo "PROCESO ENVIO BOLETA - CREAR LOG : " . $resultadoLog[1] . "\n";
                            } else {
                                echo "PROCESO ENVIO BOLETA - ERROR CREAR LOG\n";
                            }
                        }
                    } else {
                        $objLog = new LogData();
                        $objLog->documento = "BOLETA";
                        $objLog->indicador = $objResumenBoleta->fe_resumenboleta_id;
                        $objLog->mensaje = $msj;
                        $objLog->fecha_creacion = date("Y-m-d H:i:s");
                        $objLog->usuario_creacion = 1;
                        $resultadoLog = $objLog->add();
                        if (isset($resultadoLog[1]) && $resultadoLog[1] > 0) {
                            echo "PROCESO ENVIO BOLETA - CREAR LOG : " . $resultadoLog[1] . "\n";
                        } else {
                            echo "PROCESO ENVIO BOLETA - ERROR CREAR LOG\n";
                        }
                    }
                }
            }            
        } else {
            echo "PROCESO ENVIO BOLETA - ERROR RESUMEN BOLETA\n";
            $generarBoleta = false;
        }
    } else {
        echo "PROCESO ENVIO BOLETA - NO HAY BOLETAS PARA ENVIAR A SUNAT\n";
        $generarBoleta = false;
    }
}
echo "PROCESO ENVIO BOLETA - FIN " . date("d/m/Y H:i") . "\n";

?>