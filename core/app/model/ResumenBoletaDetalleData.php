<?php

class ResumenBoletaDetalleData
{

    public static $tablename = "resumenboletadetalle";

    public function __construct()
    {        
        $this->fe_resumenboletadetalle_id = "";
        $this->fe_resumenboleta_id = "";
        $this->fe_resumenboletadetalle_num = "";
        $this->cs_tipodocumento_cod = "";
        $this->fe_resumenboletadetalle_ser = "";
        $this->fe_resumenboletadetalle_cor = "";
        $this->cs_tipodocumentoidentidad_cod = "";
        $this->tb_cliente_numdoc = "";
        $this->fe_resumenboletadetalle_tipdocrel = "";
        $this->fe_resumenboletadetalle_docrelser = "";
        $this->fe_resumenboletadetalle_docrelcor = "";
        $this->cs_tipomoneda_cod = "";
        $this->fe_resumenboletadetalle_totvengra = "";
        $this->fe_resumenboletadetalle_totvenina = "";
        $this->fe_resumenboletadetalle_totvenexo = "";
        $this->fe_resumenboletadetalle_totvengratui = "";
        $this->fe_resumenboletadetalle_sumotrcar = "";
        $this->fe_resumenboletadetalle_sumisc = "";
        $this->fe_resumenboletadetalle_sumigv = "";
        $this->fe_resumenboletadetalle_sumotrtri = "";
        $this->fe_resumenboletadetalle_imptot = "";
        $this->fe_comprobante_id = "";
        $this->fe_resumenboletadetalle_est = "";
    }
    
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (fe_resumenboleta_id,fe_resumenboletadetalle_num, cs_tipodocumento_cod,fe_resumenboletadetalle_ser,";
        $sql .= "fe_resumenboletadetalle_cor,cs_tipodocumentoidentidad_cod,tb_cliente_numdoc,fe_resumenboletadetalle_tipdocrel,fe_resumenboletadetalle_docrelser,";
        $sql .= "fe_resumenboletadetalle_docrelcor,cs_tipomoneda_cod,fe_resumenboletadetalle_totvengra,fe_resumenboletadetalle_totvenina,fe_resumenboletadetalle_totvenexo,";
        $sql .= "fe_resumenboletadetalle_totvengratui,fe_resumenboletadetalle_sumotrcar,fe_resumenboletadetalle_sumisc,fe_resumenboletadetalle_sumigv,";
        $sql .= "fe_resumenboletadetalle_sumotrtri,fe_resumenboletadetalle_imptot,fe_comprobante_id,fe_resumenboletadetalle_est) value ";
        $sql .= "(\"$this->fe_resumenboleta_id\",\"$this->fe_resumenboletadetalle_num\",\"$this->cs_tipodocumento_cod\",\"$this->fe_resumenboletadetalle_ser\",";
        $sql .= "\"$this->fe_resumenboletadetalle_cor\",\"$this->cs_tipodocumentoidentidad_cod\",\"$this->tb_cliente_numdoc\",\"$this->fe_resumenboletadetalle_tipdocrel\",";
        $sql .= "\"$this->fe_resumenboletadetalle_docrelser\",\"$this->fe_resumenboletadetalle_docrelcor\",\"$this->cs_tipomoneda_cod\",\"$this->fe_resumenboletadetalle_totvengra\",";
        $sql .= "\"$this->fe_resumenboletadetalle_totvenina\",\"$this->fe_resumenboletadetalle_totvenexo\",\"$this->fe_resumenboletadetalle_totvengratui\",";
        $sql .= "\"$this->fe_resumenboletadetalle_sumotrcar\",\"$this->fe_resumenboletadetalle_sumisc\",\"$this->fe_resumenboletadetalle_sumigv\",";
        $sql .= "\"$this->fe_resumenboletadetalle_sumotrtri\",\"$this->fe_resumenboletadetalle_imptot\",\"$this->fe_comprobante_id\",\"$this->fe_resumenboletadetalle_est\")";
        return Executor::doit($sql);
    }
    
    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where fe_resumenboleta_id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ResumenBoletaDetalleData());
    }

    public static function getDetalle($id)
    {
        $sql = "select fe_resumenboletadetalle_id from " . self::$tablename . " rbd, resumenboleta rb where rbd.fe_resumenboleta_id = rb.fe_resumenboleta_id and ";
        $sql .= "rb.fe_resumenboleta_xac = '1' and rb.fe_resumenboleta_est = 1 and rbd.fe_comprobante_id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ResumenBoletaDetalleData());        
    }
    
    public static function getBoleta($numBoleta)
    {
        $sql = "select rb.* from " . self::$tablename . " rbd ";
        $sql .=" join resumenboleta rb on rb.fe_resumenboleta_id = rbd.fe_resumenboleta_id "; 
        $sql .= "where concat(rbd.fe_resumenboletadetalle_ser,'-',rbd.fe_resumenboletadetalle_cor) = \"$numBoleta\" and rbd.fe_resumenboletadetalle_est = 1 and ";
        $sql .= "rb.fe_resumenboleta_est = 1";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ResumenBoletaDetalleData());
    }
}