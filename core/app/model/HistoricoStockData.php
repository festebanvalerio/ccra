<?php

class HistoricoStockData
{

    public static $tablename = "historicos_stock";

    public function __construct()
    {
        $this->id = "";
        $this->fecha_stock = "";
        $this->sede = "";
        $this->nom_sede = "";
        $this->almacen = "";
        $this->nom_almacen = "";
        $this->fecha_actualizacion = "";
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (fecha_stock,sede,nom_sede,almacen,nom_almacen,fecha_actualizacion) ";
        $sql .= "value (\"$this->fecha_stock\",\"$this->sede\",\"$this->nom_sede\",\"$this->almacen\",\"$this->nom_almacen\",\"$this->fecha_actualizacion\")";
        return Executor::doit($sql);
    }
}