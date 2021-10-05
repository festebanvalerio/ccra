<?php

class AreaPisoSedeData
{

    public static $tablename = "areas_pisos_sede";

    public function __construct()
    {
        $this->id = "";
        $this->piso_sede = "";
        $this->area = "";
    }

    public function getPisoSede()
    {
        return PisoSedeData::getById($this->piso_sede);
    }
    
    public function getArea()
    {
        return AreaData::getById($this->area);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (piso_sede,area) value (\"$this->piso_sede\",\"$this->area\")";
        return Executor::doit($sql);
    }

    public function delete()
    {
        $sql = "delete from " . self::$tablename . " where piso_sede = \"$this->piso_sede\"";        
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new PisoSedeData());
    }

    public static function getAreaxPisoxSede($pisoSede = "", $area = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($pisoSede != "") {
            $sql .= "and piso_sede = \"$pisoSede\" ";
        }
        if ($area != "") {
            $sql .= "and area = \"$area\" ";
        }
        $sql .= "order by id asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new AreaPisoSedeData());
    }

}