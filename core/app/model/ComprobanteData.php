<?php

class ComprobanteData
{

    public static $tablename = "comprobantes";

    public function __construct()
    {
        $this->fe_comprobante_id = "";
        $this->fe_comprobante_xac = "";
        $this->fe_comprobante_reg = "";
        $this->cs_tipodocumento_cod = "";
        $this->fe_comprobante_ser = "";
        $this->fe_comprobante_cor = "";
        $this->fe_comprobante_fec = "";
        $this->cs_tipomoneda_cod = "";
        $this->cs_tipodocumentoidentidad_cod = "";
        $this->tb_cliente_numdoc = "";
        $this->tb_cliente_nom = "";
        $this->tb_cliente_dir = "";
        $this->fe_comprobante_totvengra = "";
        $this->fe_comprobante_totvenina = "";
        $this->fe_comprobante_totvenexo = "";
        $this->fe_comprobante_totvengratui = "";
        $this->fe_comprobante_totdes = "";
        $this->fe_comprobante_sumigv = "";
        $this->fe_comprobante_sumisc = "";
        $this->fe_comprobante_sumotrtri = "";
        $this->fe_comprobante_desglo = "";
        $this->fe_comprobante_sumotrcar = "";
        $this->fe_comprobante_imptot = "";
        $this->cs_tipooperacion_cod = "";
        $this->fe_comprobante_detcod = "";
        $this->fe_comprobante_detpor = "";
        $this->fe_comprobante_detmon = "";
        $this->cs_documentosrelacionados_cod = "";
        $this->fe_comprobante_docrel = "";
        $this->fe_comprobante_tipcam = "";
        $this->tb_notacredeb_tip = "";
        $this->tb_notacredeb_mot = "";
        $this->tb_notacredeb_tipdoc = "";
        $this->tb_notacredeb_numdoc = "";
        $this->fe_comprobante_faucod = "";
        $this->fe_comprobante_digval = "";
        $this->fe_comprobante_sigval = "";
        $this->fe_comprobante_val = "";
        $this->fe_comprobante_fecenvsun = "";
        $this->fe_comprobante_resbol = "";
        $this->fe_comprobante_combaj = "";
        $this->fe_comprobante_estsun = "";
        $this->fe_comprobante_est = "";
        $this->historial_pago = "";
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (fe_comprobante_reg,cs_tipodocumento_cod,fe_comprobante_ser,fe_comprobante_cor,fe_comprobante_fec,cs_tipomoneda_cod,";
        $sql .= "cs_tipodocumentoidentidad_cod,tb_cliente_numdoc,tb_cliente_nom,tb_cliente_dir,fe_comprobante_totvengra,fe_comprobante_totvenina,fe_comprobante_totvenexo,";
        $sql .= "fe_comprobante_totvengratui,fe_comprobante_totdes,fe_comprobante_sumigv,fe_comprobante_sumisc,fe_comprobante_sumotrtri,fe_comprobante_desglo,";
        $sql .= "fe_comprobante_sumotrcar,fe_comprobante_imptot,cs_tipooperacion_cod,fe_comprobante_detcod,fe_comprobante_detpor,fe_comprobante_detmon,";
        $sql .= "cs_documentosrelacionados_cod,fe_comprobante_docrel,fe_comprobante_tipcam,tb_notacredeb_tip,tb_notacredeb_mot,tb_notacredeb_tipdoc,";
        $sql .= "tb_notacredeb_numdoc,fe_comprobante_faucod,fe_comprobante_digval,fe_comprobante_sigval,fe_comprobante_val,fe_comprobante_fecenvsun,";
        $sql .= "fe_comprobante_estsun,fe_comprobante_resbol,fe_comprobante_est,historial_pago) values (\"$this->fe_comprobante_reg\",\"$this->cs_tipodocumento_cod\",";
        $sql .= "\"$this->fe_comprobante_ser\",\"$this->fe_comprobante_cor\",\"$this->fe_comprobante_fec\",\"$this->cs_tipomoneda_cod\",";
        $sql .= "\"$this->cs_tipodocumentoidentidad_cod\",\"$this->tb_cliente_numdoc\",\"$this->tb_cliente_nom\",\"$this->tb_cliente_dir\",\"$this->fe_comprobante_totvengra\",";
        $sql .= "\"$this->fe_comprobante_totvenina\",\"$this->fe_comprobante_totvenexo\",\"$this->fe_comprobante_totvengratui\",\"$this->fe_comprobante_totdes\",";
        $sql .= "\"$this->fe_comprobante_sumigv\",\"$this->fe_comprobante_sumisc\",\"$this->fe_comprobante_sumotrtri\",\"$this->fe_comprobante_desglo\",";
        $sql .= "\"$this->fe_comprobante_sumotrcar\",\"$this->fe_comprobante_imptot\",\"$this->cs_tipooperacion_cod\",\"$this->fe_comprobante_detcod\",";
        $sql .= "\"$this->fe_comprobante_detpor\",\"$this->fe_comprobante_detmon\",\"$this->cs_documentosrelacionados_cod\",\"$this->fe_comprobante_docrel\",";
        $sql .= "\"$this->fe_comprobante_tipcam\",\"$this->tb_notacredeb_tip\",\"$this->tb_notacredeb_mot\",\"$this->tb_notacredeb_tipdoc\",";
        $sql .= "\"$this->tb_notacredeb_numdoc\",\"$this->fe_comprobante_faucod\",\"$this->fe_comprobante_digval\",\"$this->fe_comprobante_sigval\",";
        $sql .= "\"$this->fe_comprobante_val\",\"$this->fe_comprobante_fecenvsun\",\"$this->fe_comprobante_estsun\",\"$this->fe_comprobante_resbol\",";
        $sql .= "\"$this->fe_comprobante_est\",\"$this->historial_pago\")";        
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set fe_comprobante_est = \"$this->fe_comprobante_est\", fe_comprobante_combaj = if(cs_tipodocumento_cod = 1,1,0) ";
        $sql .= "where fe_comprobante_id = \"$this->fe_comprobante_id\"";
        return Executor::doit($sql);
    }
    
    public function updateSunat()
    {
        $sql = "update " . self::$tablename . " set fe_comprobante_faucod = \"$this->fe_comprobante_faucod\", fe_comprobante_digval = \"$this->fe_comprobante_digval\",";
        $sql .= "fe_comprobante_sigval = \"$this->fe_comprobante_sigval\", fe_comprobante_val = \"$this->fe_comprobante_sigval\", ";
        $sql .= "fe_comprobante_fecenvsun = \"$this->fe_comprobante_fecenvsun\", fe_comprobante_estsun = \"$this->fe_comprobante_estsun\" ";
        $sql .= "where fe_comprobante_id = \"$this->fe_comprobante_id\"";
        return Executor::doit($sql);
    }    

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where fe_comprobante_id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ComprobanteData());
    }

    public static function getByAllComprobante()
    {
        $sql = "select * from " . self::$tablename . " where cs_tipodocumento_cod in (1,3) and fe_comprobante_estsun = 0 and ";
        $sql .= "fe_comprobante_fecenvsun = '0000-00-00 00:00:00' order by fe_comprobante_reg desc limit 5";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ComprobanteData());
    }
    
    public static function getByPago($comprobante, $historialPago)
    {
        $sql = "select * from " . self::$tablename . " where fe_comprobante_id = \"$comprobante\" and historial_pago = \"$historialPago\" and fe_comprobante_est = 1";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ComprobanteData());
    }

    public static function getAllSummaryByFecha($fecha)
    {
        $sql = "select * from " . self::$tablename . " where fe_comprobante_xac = '1' and cs_tipodocumento_cod = 3 and fe_comprobante_resbol = 1 and fe_comprobante_est <> 3 ";
        if ($fecha != "") {
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
            
            $sql .= "and fe_comprobante_fec = \"$fecha\" "; 
        }
        $sql .= "order by fe_comprobante_fec, fe_comprobante_ser, fe_comprobante_cor";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ComprobanteData());
    }
    
    public static function getAllBill($fecha)
    {
        $sql = "select * from " . self::$tablename . " c ";
        $sql .= "where c.fe_comprobante_est = '2' and c.fe_comprobante_resbol = 0 ";
        if ($fecha) {
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
            
            $sql .= "and c.fe_comprobante_fec = \"$fecha\" ";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new ComprobanteData());
    }

    public static function getAllSummary($fecha, $indicador = 0)
    {
        $sql = "select * from " . self::$tablename . " c ";
        $sql .= "where c.cs_tipodocumento_cod = 3 ";
        if ($fecha) {
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
            
            $sql .= "and c.fe_comprobante_fec = \"$fecha\" ";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new ComprobanteData());
    }
    
    public static function getAllBillByFecha($fecha)
    {
        $sql = "select * from " . self::$tablename . " where fe_comprobante_xac = '1' and fe_comprobante_combaj = 1 and fe_comprobante_est <> 3 ";
        if ($fecha != "") {
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
            
            $sql .= "and fe_comprobante_fec = \"$fecha\" ";
        }
        $sql .= "order by fe_comprobante_fec, fe_comprobante_ser, fe_comprobante_cor";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ComprobanteData());
    }
    
    public static function getReportContabilidad($tipo = "", $fechai = "", $fechaf = "")
    {
        $sql = "";
        if ($tipo == "1") {
            $sql = "select c.fe_comprobante_id as id,c.fe_comprobante_fec as fecha_emision,c.cs_tipodocumento_cod as tipo,c.fe_comprobante_ser as serie,";
            $sql .= "c.fe_comprobante_cor as numero,c.cs_tipodocumentoidentidad_cod as doc_entidad,c.tb_cliente_numdoc as ruc,c.tb_cliente_nom as denominacion,";
            $sql .= "c.cs_tipomoneda_cod as moneda,c.fe_comprobante_totvengra as gravada,c.fe_comprobante_totvenexo as exonerada,c.fe_comprobante_totvenina as inafecta,";
            $sql .= "c.fe_comprobante_sumisc as isc,c.fe_comprobante_sumigv as igv,c.fe_comprobante_imptot as total,c.fe_comprobante_estsun as estado_sunat,";
            $sql .= "c.fe_comprobante_est as estado,c.fe_comprobante_resbol,c.fe_comprobante_combaj,c.fe_comprobante_faucod as codigo ";
            $sql .= "from " . self::$tablename . " c where 1 = 1 ";
        } else if ($tipo == "2") {
            $sql = "select c.fe_comprobante_id as id,c.fe_comprobante_fec as fecha_emision,c.cs_tipodocumento_cod as tipo,c.fe_comprobante_ser as serie,";
            $sql .= "c.fe_comprobante_cor as numero,c.cs_tipodocumentoidentidad_cod as doc_entidad,c.tb_cliente_numdoc as ruc,c.tb_cliente_nom as denominacion,";
            $sql .= "c.cs_tipomoneda_cod as moneda,c.fe_comprobante_totvengra as gravada,c.fe_comprobante_totvenexo as exonerada,c.fe_comprobante_totvenina as inafecta,";
            $sql .= "c.fe_comprobante_sumisc as isc,c.fe_comprobante_sumigv as igv,c.fe_comprobante_imptot as total,c.fe_comprobante_estsun as estado_sunat,";
            $sql .= "c.fe_comprobante_est as estado,c.fe_comprobante_resbol,c.fe_comprobante_combaj,c.fe_comprobante_faucod as codigo,";
            $sql .= "dc.fe_comprobantedetalle_cod as cod_producto,dc.fe_comprobantedetalle_nom as producto,dc.cs_tipounidadmedida_cod as unidad,";
            $sql .= "dc.fe_comprobantedetalle_can as cantidad,dc.fe_comprobantedetalle_valuni as valuni,dc.fe_comprobantedetalle_preuni as precio,";
            $sql .= "dc.fe_comprobantedetalle_des as descuento,dc.fe_comprobantedetalle_valven as subtotal,dc.fe_comprobantedetalle_igv as igv ";
            $sql .= "from " . self::$tablename . " c, detalle_comprobantes dc where c.fe_comprobante_id = dc.fe_comprobante_id ";
        }
        if ($fechai != "") {
            $arrFecha = explode("/", $fechai);
            $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];

            $sql .= "and c.fe_comprobante_fec >= \"$fecha\" ";
        }
        if ($fechaf != "") {
            $arrFecha = explode("/", $fechaf);
            $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];

            $sql .= "and c.fe_comprobante_fec <= \"$fecha\" ";
        }
        $sql .= "order by c.fe_comprobante_fec desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ComprobanteData());
    }
    
    public static function getTotalComprobanteXFecha($sede = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "SELECT c.fe_comprobante_fec as fecha,sum(c.fe_comprobante_imptot) as total ";
        $sql .= "from " . self::$tablename . " c ";
        $sql .= "join historial_pagos hp on hp.id = c.historial_pago ";
        $sql .= "join pagos pa on pa.id = hp.pago ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado > 0 and pa.estado = 1 and hp.estado = 1 and c.fe_comprobante_est = 1 ";
        if ($fechaInicio != "") {
            $sql .= "and c.fe_comprobante_fec >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $sql .= "and c.fe_comprobante_fec <= \"$fechaFin\" ";
        }
        $sql .= "group by c.fe_comprobante_fec";        
        $query = Executor::doit($sql);
        return Model::one($query[0], new ComprobanteData());        
    }
    
    public static function getNumTotalComprobanteXFecha($sede = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "SELECT c.fe_comprobante_fec as fecha,count(1) as total ";
        $sql .= "from " . self::$tablename . " c ";
        $sql .= "join historial_pagos hp on hp.id = c.historial_pago ";
        $sql .= "join pagos pa on pa.id = hp.pago ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado > 0 and pa.estado = 1 and hp.estado = 1 and c.fe_comprobante_est = 1 ";
        if ($fechaInicio != "") {
            $sql .= "and c.fe_comprobante_fec >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $sql .= "and c.fe_comprobante_fec <= \"$fechaFin\" ";
        }
        $sql .= "group by c.fe_comprobante_fec";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ComprobanteData());
    }
}