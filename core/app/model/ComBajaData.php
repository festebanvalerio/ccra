<?php

class ComBajaData
{

    public static $tablename = "combaja";

    public function __construct()
    {
        $this->fe_combaja_id = "";
        $this->fe_combaja_xac = "";
        $this->fe_combaja_reg = "";
        $this->fe_combaja_usureg = "";
        $this->fe_combaja_fec = "";
        $this->fe_combaja_fecref = "";
        $this->fe_combaja_cod = "";
        $this->fe_combaja_num = "";
        $this->fe_combaja_tic = "";
        $this->fe_combaja_faucod = "";
        $this->fe_combaja_digval = "";
        $this->fe_combaja_sigval = "";
        $this->fe_combaja_val = "";
        $this->fe_combaja_fecenvsun = "";
        $this->fe_combaja_estsun = "";
        $this->fe_combaja_faucod2 = "";
        $this->fe_combaja_fecenvsun2 = "";
        $this->fe_combaja_estsun2 = "";
        $this->fe_combaja_est = "";
    }

    public function getDetalle()
    {
        return ComBajaDetalleData::getById($this->fe_combaja_id);
    }
        
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (fe_combaja_reg,fe_combaja_usureg,fe_combaja_fec,fe_combaja_fecref,fe_combaja_cod,";
        $sql .= "fe_combaja_num) value (\"$this->fe_combaja_reg\",\"$this->fe_combaja_usureg\",\"$this->fe_combaja_fec\",";
        $sql .= "\"$this->fe_combaja_fecref\",\"$this->fe_combaja_cod\",\"$this->fe_combaja_num\")";        
        return Executor::doit($sql);
    }

    public function updateSunat()
    {
        $sql = "update " . self::$tablename . " set fe_combaja_tic = \"$this->fe_combaja_tic\", fe_combaja_faucod = \"$this->fe_combaja_faucod\",";
        $sql .= "fe_combaja_digval = \"$this->fe_combaja_digval\", fe_combaja_sigval = \"$this->fe_combaja_sigval\", ";
        $sql .= "fe_combaja_val = \"$this->fe_combaja_val\", fe_combaja_fecenvsun = \"$this->fe_combaja_fecenvsun\", ";
        $sql .= "fe_combaja_estsun = \"$this->fe_combaja_estsun\" where fe_combaja_id = \"$this->fe_combaja_id\"";        
        return Executor::doit($sql);
    }
    
    public function updateSunat2()
    {
        $sql = "update " . self::$tablename . " set fe_combaja_faucod2 = \"$this->fe_combaja_faucod2\", fe_combaja_fecenvsun2 = \"$this->fe_combaja_fecenvsun2\",";
        $sql .= "fe_combaja_estsun2 =  \"$this->fe_combaja_estsun2\" where fe_combaja_id = \"$this->fe_combaja_id\"";
        return Executor::doit($sql);
    }
    
    public function delete()
    {
        $sql = "update " . self::$tablename . " set fe_combaja_est = \"$this->fe_combaja_est\" where fe_combaja_id = \"$this->fe_combaja_id\"";
        return Executor::doit($sql);
    }
    
    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where fe_combaja_id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ComBajaData());
    }
    
    public static function getLastId($fecha)
    {
        $sql = "select ifnull(max(fe_combaja_num),0) as ultimo_numero from " . self::$tablename . " where 1=1 ";
        if ($fecha != "") {
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
            
            $sql .= "and fe_combaja_fec = \"$fecha\"";
        }        
        $query = Executor::doit($sql);
        return Model::one($query[0], new ComBajaData()); 
    }

    public static function getAllResumen()
    {
        $sql = "select * from " . self::$tablename . " where fe_combaja_estsun is NULL order by fe_combaja_reg desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ComBajaData());
    }
    
    public static function getAll($fecha = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($fecha != "") {
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2] . "-" . $arrFecha[1] . "-" . $arrFecha[0];
            
            $sql .= "and fe_combaja_fecref = \"$fecha\"";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new ComBajaData());
    }
}