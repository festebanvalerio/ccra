<?php

class DetalleTransferenciaData
{

    public static $tablename = "detalle_transferencias";
    
    public function __construct()
    {
        $this->id = "";
        $this->transferencia = "";
        $this->insumo = "";
        $this->cantidad = "";
        $this->tipo = "";
        $this->estado = "";
    }

    public function getTransferencia()
    {
        return TransferenciaData::getById($this->transferencia);    
    }
    
    public function getInsumo()
    {
        return InsumoData::getById($this->insumo);
    }    
    
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (transferencia,insumo,cantidad,tipo,estado) value (\"$this->transferencia\",\"$this->insumo\",";
        $sql .= "\"$this->cantidad\",\"$this->tipo\",\"$this->estado\")";
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new DetalleTransferenciaData());
    }
    
    public static function getAllByTransferencia($transferencia)
    {
        $sql = "select * from " . self::$tablename . " where transferencia = \"$transferencia\"";
        $sql .= "order by id asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleTransferenciaData());
    }

}