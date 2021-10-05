<?php

$ruta = "/var/www/html/ccra/";

require_once ($ruta . "cpegeneracion/sunat/funciones.php");
require_once ($ruta . "cpegeneracion/sunat/toarray.php");
require_once ($ruta . "cpegeneracion/sunat/toxml.php");
require_once ($ruta . "formatos/numletras.php");
require_once ($ruta . "cpeconfig/datos.php");
require_once ($ruta . "core/app/model/ComprobanteData.php");
require_once ($ruta . "core/app/model/DetalleComprobanteData.php");
require_once ($ruta . "core/controller/Executor.php");
require_once ($ruta . "core/controller/Database.php");
require_once ($ruta . "core/controller/Core.php");
require_once ($ruta . "core/controller/Model.php");

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

file_put_contents("info" . date("Ymd") . ".log", "---------------------------------------------------------------\n", FILE_APPEND);

$lstComprobante = ComprobanteData::getByAllComprobante();
foreach ($lstComprobante as $objComprobante) {
    file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - Inicio : " . $objComprobante->fe_comprobante_id . "\n", FILE_APPEND);
    
    $header = array();
    $header[0]["idcomprobante"] = $objComprobante->cs_tipodocumento_cod;
    $header[0]["serie"] = $objComprobante->fe_comprobante_ser;
    $header[0]["numero"] = $objComprobante->fe_comprobante_cor;
    $header[0]["fechadoc"] = $objComprobante->fe_comprobante_reg;
    $header[0]["isomoneda"] = $objComprobante->cs_tipomoneda_cod;
    $header[0]["idtipodni"] = $objComprobante->cs_tipodocumentoidentidad_cod;
    $header[0]["identidad"] = $objComprobante->tb_cliente_numdoc;
    $header[0]["razon"] = $objComprobante->tb_cliente_nom;

    // total grav - dscto global
    $header[0]["totopgra"] = $objComprobante->fe_comprobante_totvengra;
    // total inaf - dscto global
    $header[0]["totopina"] = $objComprobante->fe_comprobante_totvenina;
    // total exon - dscto global
    $header[0]["totopexo"] = $objComprobante->fe_comprobante_totvenexo;
    // total grat - dscto global
    $header[0]["totopgrat"] = $objComprobante->fe_comprobante_totvengratui;
    // descto linea (o) + dscto global
    $header[0]["totdescto"] = $objComprobante->fe_comprobante_totdes;

    // tot ope grav *18%
    $header[0]["totigv"] = $objComprobante->fe_comprobante_sumigv;
    // sumatoria isc
    $header[0]["totisc"] = $objComprobante->fe_comprobante_sumisc;
    // sumatoria otros tributos
    $header[0]["tototh"] = $objComprobante->fe_comprobante_sumotrtri;
    // descuentos globales
    $header[0]["desctoglobal"] = $objComprobante->fe_comprobante_desglo;
    // otros cargos
    $header[0]["tototroca"] = $objComprobante->fe_comprobante_sumotrcar;
    // importe total
    $header[0]["importetotal"] = $objComprobante->fe_comprobante_imptot;

    // VENTA INTERNA
    $header[0]["idtoperacion"] = $objComprobante->cs_tipooperacion_cod;

    // si es mayor a cero considerar
    $header[0]["totanti"] = "0.00";

    // Número de cuenta de Detracción
    $header[0]["detnumcue"] = $cpe_detnumcue;
    // Código bien o servicio sujeto a detracción 022: otros servicios empresariales
    $header[0]["detcod"] = $objComprobante->fe_comprobante_detcod;
    // Detraccion Porcentaje
    $header[0]["detpor"] = $objComprobante->fe_comprobante_detpor;
    // Detraccion Monto
    $header[0]["detmon"] = $objComprobante->fe_comprobante_detmon;

    // CATALOGO 12
    $header[0]["iddoctributario"] = $objComprobante->cs_documentosrelacionados_cod;
    $header[0]["iddoctriref"] = $objComprobante->fe_comprobante_docrel;
    $header[0]["nroplaca"] = ""; // $objComprobante->fe_comprobante_numpla;

    $total_letras = numtoletras($objComprobante->fe_comprobante_imptot, 0);
    $header[0]["AdditionalProperty_Value"] = $total_letras;

    $documento_cod = $objComprobante->cs_tipodocumento_cod;
    $documento = $objComprobante->fe_comprobante_ser . "-" . $objComprobante->fe_comprobante_cor;
    $faucod = $objComprobante->fe_comprobante_faucod;

    $header[0]["referenceid"] = $objComprobante->tb_notacredeb_numdoc;
    $header[0]["referencedocumenttypecode"] = $objComprobante->tb_notacredeb_tipdoc;

    $header[0]["idtiponotacredito"] = $objComprobante->tb_notacredeb_tip;
    $header[0]["description"] = $objComprobante->tb_notacredeb_mot;

    $notacredeb_tipdoc = $objComprobante->tb_notacredeb_tipdoc;

    $autoin = 0;

    $detalle = array();
    $lstDetalle = DetalleComprobanteData::getById($objComprobante->fe_comprobante_id);
    foreach ($lstDetalle as $objDetalleComprobante) {
        /*
         * if ($objDetalleComprobante->fe_comprobantedetalle_cod == "") {
         * $codigo = $objDetalleComprobante->fe_comprobantedetalle_nro;
         * } else {
         * $codigo = $objDetalleComprobante->fe_comprobantedetalle_cod;
         * }
         */
        $codigo = "";

        // 10AFECTO 20EXONERADO 30INAFECTO
        if ($objDetalleComprobante->fe_comprobantedetalle_igv == 0) {
            $detalle[$autoin]["idafectaciond"] = 20;
        } else {
            $detalle[$autoin]["idafectaciond"] = $objDetalleComprobante->cs_tipoafectacionigv_cod;
        }
        $detalle[$autoin]["nro"] = $objDetalleComprobante->fe_comprobantedetalle_nro;
        $detalle[$autoin]["codigo"] = $codigo;
        $detalle[$autoin]["detalle"] = $objDetalleComprobante->fe_comprobantedetalle_nom;
        $detalle[$autoin]["idmedida"] = $objDetalleComprobante->cs_tipounidadmedida_cod;
        $detalle[$autoin]["cantidad"] = $objDetalleComprobante->fe_comprobantedetalle_can;

        // valor venta unitario- no incluye igv
        $detalle[$autoin]["valorunitario"] = $objDetalleComprobante->fe_comprobantedetalle_valuni;
        // precio unitario: valor unitario (- descuentos *sin descuento) + igv
        $detalle[$autoin]["preciounitario"] = $objDetalleComprobante->fe_comprobantedetalle_preuni;
        // para retio y gratuitas
        $detalle[$autoin]["valorrefunitario"] = $objDetalleComprobante->fe_comprobantedetalle_valrefuni;

        // sumatoria valor venta unitario x cantidad
        $detalle[$autoin]["valorventa"] = $objDetalleComprobante->fe_comprobantedetalle_valven;
        // decuento aplicado al valor venta (sin igv)
        $detalle[$autoin]["descto"] = $objDetalleComprobante->fe_comprobantedetalle_des;
        // sumatoria igv x cantidad
        $detalle[$autoin]["igv"] = $objDetalleComprobante->fe_comprobantedetalle_igv;

        $detalle[$autoin]["idtiposcisc"] = $objDetalleComprobante->cs_tiposistemacalculoisc_cod;
        $detalle[$autoin]["isc"] = $objDetalleComprobante->fe_comprobantedetalle_isc;

        $autoin ++;
    }
    $header[0]["LineCount"] = $autoin;

    $header = json_decode(json_encode($header));
    $detalle = json_decode(json_encode($detalle));

    if ($faucod == "0") {
        file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - Comprobante ya generado: " . $objComprobante->fe_comprobante_id ."\n", FILE_APPEND);
        continue;
    }

    if ($documento_cod != 1 and $documento_cod != 3 and $documento_cod != 7) {
        file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - Codigo de documento no existe: " . $objComprobante->fe_comprobante_id ."\n", FILE_APPEND);
        continue;
    }

    $estado_envsun = 0;
    $estado = $msj = "";

    // FACTURA
    if ($documento_cod == 1) {
        $enviar = true;
        $r = run(datatoarray($header, $detalle, $empresa, "Invoice"), $ruta . "cperepositorio/send/", $ruta . "cperepositorio/cdr/", "", "Invoice", $enviar);        
        if ($r["faultcode"] == "HTTP") {
            // $enviar_doble = 1;
            $estado = false;
            $msj = "No pudo hacer conexión. Enviar manualmente.";
            $estado_envsun = 0;
        }
        if ($r["faultcode"] == "0") {
            $msj = $documento . " ACEPTADO";
            //$estado = true;
            $estado_envsun = 1;
        }
        if ($r["faultcode"] >= 4000) {
            $msj = $documento . " ACEPTADO (!)";
            //$estado = true;
            $estado_envsun = 2;
        }
        if ($r["faultcode"] >= 2000 && $r["faultcode"] <= 3999) {
            $msj = $documento . " RECHAZADO";
            //$estado = false;
            $estado_envsun = 3;
        }
        if ($r["faultcode"] >= 100 && $r["faultcode"] <= 999) {
            $msj = $documento . " EXCP. SUNAT";
            //$estado = "";
            $estado_envsun = 4;
        }
        if ($r["faultcode"] >= 1000 && $r["faultcode"] <= 1999) {
            $msj = $documento . " EXCP. LOCAL";
            //$estado = "";
            $estado_envsun = 5;
        }
        file_put_contents("info" . date("Ymd") . ".log", "Envio Factura - Resultado : " . $r["faultcode"] . "\n", FILE_APPEND);
        
        if ($estado_envsun >= 0) {
            $objComprobante->fe_comprobante_faucod = $r["faultcode"];
            $objComprobante->fe_comprobante_digval = $r["digvalue"];
            $objComprobante->fe_comprobante_sigval = $r["signvalue"];
            $objComprobante->fe_comprobante_val = $r["valid"];
            $objComprobante->fe_comprobante_fecenvsun = date("Y-m-d H:i:s");
            $objComprobante->fe_comprobante_estsun = $estado_envsun;
            $resultado = $objComprobante->updateSunat();

            file_put_contents("info" . date("Ymd") . ".log", "Envio Factura - FAUCOD : " . $r["faultcode"] . "\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "Envio Factura - DIGVAL : " . $r["digvalue"] . "\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "Envio Factura - SIGVAL : " . $r["signvalue"] . "\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "Envio Factura - VALID : " . $r["valid"] . "\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "Envio Factura - ESTADO : " . $estado_envsun . "\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "Envio Factura Actualizado : " . $resultado[0] . "\n", FILE_APPEND);
        } else {
            file_put_contents("info" . date("Ymd") . ".log", "Envio Factura - MENSAJE : " . $msj . "\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "Envio Factura - ESTADO : " . $estado_envsun . "\n", FILE_APPEND);
        }
    }

    // BOLETA
    if ($documento_cod == 3) {
        $enviar = false;
        $r = run(datatoarray($header, $detalle, $empresa, "Invoice"), $ruta . "cperepositorio/send/", $ruta . "cperepositorio/cdr/", "", "Invoice", $enviar);
        
        $estado_envsun = 1;
        $estado = true;
        $msj = $documento . " REGISTRADO.";

        $objComprobante->fe_comprobante_faucod = $r["faultcode"];
        $objComprobante->fe_comprobante_digval = $r["digvalue"];
        $objComprobante->fe_comprobante_sigval = $r["signvalue"];
        $objComprobante->fe_comprobante_val = $r["valid"];
        $objComprobante->fe_comprobante_fecenvsun = date("Y-m-d H:i:s");
        $objComprobante->fe_comprobante_estsun = $estado_envsun;
        $resultado = $objComprobante->updateSunat();

        file_put_contents("info" . date("Ymd") . ".log", "Envio Boleta : " . $r["faultcode"] . "\n", FILE_APPEND);
        file_put_contents("info" . date("Ymd") . ".log", "Envio Boleta - FAUCOD : " . $r["faultcode"] . "\n", FILE_APPEND);
        file_put_contents("info" . date("Ymd") . ".log", "Envio Boleta - DIGVAL : " . $r["digvalue"] . "\n", FILE_APPEND);
        file_put_contents("info" . date("Ymd") . ".log", "Envio Boleta - SIGVAL : " . $r["signvalue"] . "\n", FILE_APPEND);
        file_put_contents("info" . date("Ymd") . ".log", "Envio Boleta - VALID : " . $r["valid"] . "\n", FILE_APPEND);
        file_put_contents("info" . date("Ymd") . ".log", "Envio Boleta - ESTADO : " . $estado_envsun . "\n", FILE_APPEND);
        file_put_contents("info" . date("Ymd") . ".log", "Envio Boleta Actualizado : " . $resultado[0] . "\n", FILE_APPEND);
    }

    // NOTA CREDITO
    if ($documento_cod == 7) {
        // FACTURA
        if ($notacredeb_tipdoc == 1) {
            $enviar = true;
            $r = run(datatoarray($header, $detalle, $empresa, "CreditNote"), $ruta . "cperepositorio/send/", $ruta . "cperepositorio/cdr/", "", "CreditNote", $enviar);

            if ($r["faultcode"] == "0") {
                $msj = "Enviado Correctamente a SUNAT";
                $estado = true;
                $estado_envsun = 1;
            } else {
                $msj = "Error: " . $r["faultcode"];
                if ($r["faultcode"] == "HTTP") {
                    $enviar = 1;
                }
                //$enviar_doble = 1;
                $estado = false;
                $msj = "No pudo hacer conexión. Enviar manualmente.";
                $estado_envsun = 0;
            }
            $objComprobante->fe_comprobante_faucod = $r["faultcode"];
            $objComprobante->fe_comprobante_digval = $r["digvalue"];
            $objComprobante->fe_comprobante_sigval = $r["signvalue"];
            $objComprobante->fe_comprobante_val = $r["valid"];
            $objComprobante->fe_comprobante_fecenvsun = date("Y-m-d H:i:s");
            $objComprobante->fe_comprobante_estsun = $estado_envsun;
        }
        // BOLETA
        if ($notacredeb_tipdoc == 3) {
            $enviar = false;
            $r = run(datatoarray($header, $detalle, $empresa, "CreditNote"), $ruta . "cperepositorio/send/", $ruta . "cperepositorio/cdr/", "", "CreditNote", $enviar);

            $estado = true;
            $estado_envsun = 10;

            $msj = "Registrado Correctamente.";

            $objComprobante->fe_comprobante_faucod = $r["faultcode"];
            $objComprobante->fe_comprobante_digval = $r["digvalue"];
            $objComprobante->fe_comprobante_sigval = $r["signvalue"];
            $objComprobante->fe_comprobante_val = $r["valid"];
            $objComprobante->fe_comprobante_fecenvsun = date("Y-m-d H:i:s");
            $objComprobante->fe_comprobante_estsun = $estado_envsun;
            $objComprobante->updateSunat();

            //$oEcomprobante->modificar_campo($fe_comprobante_id, "fe_comprobante_resbol", 1);
        }
    }

    file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - Fin : " . $objComprobante->fe_comprobante_id . "\n", FILE_APPEND);
}

?>
