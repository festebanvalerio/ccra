<?php

class CreditoData
{

    public static $tablename = "creditos";

    public function __construct()
    {
        $this->id = "";
        $this->num_documento = "";
        $this->datos = "";
        $this->monto = "";
        $this->abono = "";        
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (num_documento,datos,monto,abono) value (\"$this->num_documento\",\"$this->datos\",\"$this->monto\",\"$this->abono\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set monto = \"$this->monto\",abono = \"$this->abono\" where id = \"$this->id\"";
        return Executor::doit($sql);
    }
    
    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new CreditoData());
    }

    public static function getByNumDoc($numDoc = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($numDoc != "") {
            $sql .= "and num_documento = \"$numDoc\" ";
        }
        $query = Executor::doit($sql);
        return Model::one($query[0], new CreditoData());
    }
    
    public static function getAll($numDocumento = "", $datos = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($numDocumento != "") {
            $sql .= "and num_documento = \"$numDocumento\" ";
        }
        if ($datos != "") {
            $sql .= "and datos like \"%$datos%\" ";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new CreditoData());
    }
}