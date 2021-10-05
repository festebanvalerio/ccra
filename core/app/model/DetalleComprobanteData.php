<?php

class DetalleComprobanteData
{

    public static $tablename = "detalle_comprobantes";

    public function __construct()
    {
        $this->fe_comprobantedetalle_id = "";
        $this->fe_comprobantedetalle_nro = "";
        $this->fe_comprobantedetalle_cod = "";
        $this->fe_comprobantedetalle_nom = "";
        $this->cs_tipoafectacionigv_cod = "";
        $this->cs_tipounidadmedida_cod = "";
        $this->fe_comprobantedetalle_can = "";
        $this->fe_comprobantedetalle_valuni = "";
        $this->fe_comprobantedetalle_preuni = "";
        $this->fe_comprobantedetalle_valrefuni = "";
        $this->fe_comprobantedetalle_valven = "";
        $this->fe_comprobantedetalle_des = "";
        $this->fe_comprobantedetalle_igv = "";
        $this->cs_tiposistemacalculoisc_cod = "";
        $this->fe_comprobantedetalle_isc = "";
        $this->fe_comprobante_id = "";
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (fe_comprobantedetalle_nro,fe_comprobantedetalle_cod,fe_comprobantedetalle_nom,cs_tipoafectacionigv_cod,";
        $sql .= "cs_tipounidadmedida_cod,fe_comprobantedetalle_can,fe_comprobantedetalle_valuni,fe_comprobantedetalle_preuni,fe_comprobantedetalle_valrefuni,";
        $sql .= "fe_comprobantedetalle_valven,fe_comprobantedetalle_des,fe_comprobantedetalle_igv,cs_tiposistemacalculoisc_cod,fe_comprobantedetalle_isc,";
        $sql .= "fe_comprobante_id) values (\"$this->fe_comprobantedetalle_nro\",\"$this->fe_comprobantedetalle_cod\",\"$this->fe_comprobantedetalle_nom\",";
        $sql .= "\"$this->cs_tipoafectacionigv_cod\",\"$this->cs_tipounidadmedida_cod\",\"$this->fe_comprobantedetalle_can\",\"$this->fe_comprobantedetalle_valuni\",";
        $sql .= "\"$this->fe_comprobantedetalle_preuni\",\"$this->fe_comprobantedetalle_valrefuni\",\"$this->fe_comprobantedetalle_valven\",";
        $sql .= "\"$this->fe_comprobantedetalle_des\",\"$this->fe_comprobantedetalle_igv\",\"$this->cs_tiposistemacalculoisc_cod\",\"$this->fe_comprobantedetalle_isc\",";
        $sql .= "\"$this->fe_comprobante_id\")";
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where fe_comprobante_id = \"$id\"";        
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleComprobanteData());
    }
}