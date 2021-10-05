<?php

class ComBajaDetalleData
{

    public static $tablename = "combajadetalle";

    public function __construct()
    {        
        $this->fe_combajadetalle_id = "";
        $this->fe_combaja_id = "";
        $this->fe_combajadetalle_num = "";
        $this->cs_tipodocumento_cod = "";
        $this->fe_combajadetalle_ser = "";
        $this->fe_combajadetalle_cor = "";
        $this->fe_combajadetalle_mot = "";
        $this->fe_comprobante_id = "";        
    }
    
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (fe_combaja_id,fe_combajadetalle_num,cs_tipodocumento_cod,fe_combajadetalle_ser,fe_combajadetalle_cor,fe_combajadetalle_mot,";
        $sql .= "fe_comprobante_id) value (\"$this->fe_combaja_id\",\"$this->fe_combajadetalle_num\",\"$this->cs_tipodocumento_cod\",\"$this->fe_combajadetalle_ser\",";
        $sql .= "\"$this->fe_combajadetalle_cor\",\"$this->fe_combajadetalle_mot\",\"$this->fe_comprobante_id\")";
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where fe_combaja_id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ComBajaDetalleData());
    }
    
    public static function getDetalle($id)
    {
        $sql = "select fe_combajadetalle_id from " . self::$tablename . " cbd, combaja cb where cbd.fe_combaja_id = cb.fe_combaja_id and ";
        $sql .= "cb.fe_combaja_xac = '1' and cb.fe_combaja_est = 1 and cbd.fe_comprobante_id = \"$id\"";        
        $query = Executor::doit($sql);
        return Model::many($query[0], new ComBajaDetalleData());        
    }
}