<?php

class DetalleAjusteData
{

    public static $tablename = "detalle_ajustes";
    
    public function __construct()
    {
        $this->id = "";
        $this->ajuste = "";
        $this->insumo = "";
        $this->stock_actual = "";
        $this->cantidad = "";
        $this->tipo = "";
        $this->estado = "";
    }

    public function getAjuste()
    {
        return AjusteData::getById($this->ajuste);    
    }
    
    public function getInsumo()
    {
        return InsumoData::getById($this->insumo);
    }    
    
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (ajuste,insumo,stock_actual,cantidad,tipo,estado) value (\"$this->ajuste\",\"$this->insumo\",";
        $sql .= "\"$this->stock_actual\",\"$this->cantidad\",\"$this->tipo\",\"$this->estado\")";
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new DetalleAjusteData());
    }
    
    public static function getAllByAjuste($ajuste)
    {
        $sql = "select * from " . self::$tablename . " where ajuste = \"$ajuste\"";
        $sql .= "order by id asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleAjusteData());
    }

}