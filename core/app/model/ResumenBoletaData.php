<?php

class ResumenBoletaData
{

    public static $tablename = "resumenboleta";

    public function __construct()
    {
        $this->fe_resumenboleta_id = "";
        $this->fe_resumenboleta_xac = "";
        $this->fe_resumenboleta_reg = "";
        $this->fe_resumenboleta_usureg = "";
        $this->fe_resumenboleta_fec = "";
        $this->fe_resumenboleta_fecref = "";
        $this->fe_resumenboleta_cod = "";
        $this->fe_resumenboleta_num = "";
        $this->fe_resumenboleta_tic = "";
        $this->fe_resumenboleta_faucod = "";
        $this->fe_resumenboleta_digval = "";
        $this->fe_resumenboleta_sigval = "";
        $this->fe_resumenboleta_val = "";
        $this->fe_resumenboleta_fecenvsun = "";
        $this->fe_resumenboleta_estsun = "";
        $this->fe_resumenboleta_faucod2 = "";
        $this->fe_resumenboleta_fecenvsun2 = "";
        $this->fe_resumenboleta_estsun2 = "";
        $this->fe_resumenboleta_est = "";
    }

    public function getDetalle()
    {
        return ResumenBoletaDetalleData::getById($this->fe_resumenboleta_id);
    }
        
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (fe_resumenboleta_reg,fe_resumenboleta_usureg,fe_resumenboleta_fec,fe_resumenboleta_fecref,fe_resumenboleta_cod,";
        $sql .= "fe_resumenboleta_num) value (\"$this->fe_resumenboleta_reg\",\"$this->fe_resumenboleta_usureg\",\"$this->fe_resumenboleta_fec\",";
        $sql .= "\"$this->fe_resumenboleta_fecref\",\"$this->fe_resumenboleta_cod\",\"$this->fe_resumenboleta_num\")";        
        return Executor::doit($sql);
    }

    public function updateSunat()
    {
        $sql = "update " . self::$tablename . " set fe_resumenboleta_tic = \"$this->fe_resumenboleta_tic\", fe_resumenboleta_faucod = \"$this->fe_resumenboleta_faucod\",";
        $sql .= "fe_resumenboleta_digval = \"$this->fe_resumenboleta_digval\", fe_resumenboleta_sigval = \"$this->fe_resumenboleta_sigval\", ";
        $sql .= "fe_resumenboleta_val = \"$this->fe_resumenboleta_val\", fe_resumenboleta_fecenvsun = \"$this->fe_resumenboleta_fecenvsun\", ";
        $sql .= "fe_resumenboleta_estsun = \"$this->fe_resumenboleta_estsun\" where fe_resumenboleta_id = \"$this->fe_resumenboleta_id\"";        
        return Executor::doit($sql);
    }
    
    public function updateSunat2()
    {
        $sql = "update " . self::$tablename . " set fe_resumenboleta_faucod2 = \"$this->fe_resumenboleta_faucod2\", fe_resumenboleta_fecenvsun2 = \"$this->fe_resumenboleta_fecenvsun2\",";
        $sql .= "fe_resumenboleta_estsun2 =  \"$this->fe_resumenboleta_estsun2\" where fe_resumenboleta_id = \"$this->fe_resumenboleta_id\"";        
        return Executor::doit($sql);
    }
    
    public function delete()
    {
        $sql = "update " . self::$tablename . " set fe_resumenboleta_est = \"$this->fe_resumenboleta_est\" where fe_resumenboleta_id = \"$this->fe_resumenboleta_id\"";
        return Executor::doit($sql);
    }
    
    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where fe_resumenboleta_id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ResumenBoletaData());
    }
    
    public static function getLastId($fecha)
    {
        $sql = "select ifnull(max(fe_resumenboleta_num),0) as ultimo_numero from " . self::$tablename . " where 1=1 ";
        if ($fecha != "") {
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
            
            $sql .= "and fe_resumenboleta_fec = \"$fecha\"";
        }        
        $query = Executor::doit($sql);
        return Model::one($query[0], new ResumenBoletaData()); 
    }

    public static function getAllResumen()
    {
        $sql = "select * from " . self::$tablename . " where fe_resumenboleta_estsun is NULL order by fe_resumenboleta_reg asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ResumenBoletaData());
    }
    
    public static function getAllResumenActual($fecha)
    {        
        if ($fecha != "") {
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
            
        }
        $sql = "select * from " . self::$tablename . " where fe_resumenboleta_fec = \"$fecha\" and fe_resumenboleta_estsun is NULL order by fe_resumenboleta_reg asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ResumenBoletaData());
    }
    
    public static function getAll($fecha = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($fecha != "") {
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
            
            $sql .= "and fe_resumenboleta_fecref = \"$fecha\"";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new ResumenBoletaData());
    }
    
    public static function getValidateDoc($id)
    {
        $sql = "select * from " . self::$tablename . " rb, resumenboletadetalle rbd ";
        $sql .= "where rb.fe_resumenboleta_id = rbd.fe_resumenboleta_id and rbd.fe_comprobante_id = \"$id\" and ";
        $sql .= "fe_resumenboleta_est = 1";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ResumenBoletaData());
    }
}