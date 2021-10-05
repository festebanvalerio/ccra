<?php

class ProductoAreaData
{

    public static $tablename = "productos_area";

    public function __construct()
    {
        $this->id = "";
        $this->producto = "";
        $this->area = "";
    }

    public function getProducto()
    {
        return ProductoData::getById($this->producto);
    }
    
    public function getArea()
    {
        return AreaData::getById($this->area);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (producto,area) value (\"$this->producto\",\"$this->area\")";
        return Executor::doit($sql);
    }

    public function delete()
    {
        $sql = "delete from " . self::$tablename . " where producto = \"$this->producto\"";        
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new PisoSedeData());
    }

    public static function getProductoXArea($producto = "", $area = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($producto != "") {
            $sql .= "and producto = \"$producto\" ";
        }
        if ($area != "") {
            $sql .= "and area = \"$area\" ";
        }
        $sql .= "order by id asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ProductoAreaData());
    }

}