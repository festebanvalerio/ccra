<?php

class PisoSedeData
{

    public static $tablename = "pisos_sede";

    public function __construct()
    {
        $this->id = "";
        $this->piso = "";
        $this->sede = "";
    }

    public function getPiso()
    {
        return PisoData::getById($this->piso);
    }
    
    public function getSede()
    {
        return SedeData::getById($this->sede);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (piso,sede) value (\"$this->piso\",\"$this->sede\")";
        return Executor::doit($sql);
    }

    public function delete()
    {
        $sql = "delete from " . self::$tablename . " where sede = \"$this->sede\"";
        echo $sql;
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new PisoSedeData());
    }

    public static function getPisoXSede($sede = "")
    {
        $sql = "select * from " . self::$tablename . " ";
        if ($sede != "") {
            $sql .= "where sede = \"$sede\" ";
        }
        $sql .= "order by id asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PisoSedeData());
    }
    
    public static function getInfo($sede, $piso)
    {
        $sql = "select * from " . self::$tablename . " where sede = \"$sede\" and piso = \"$piso\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new PisoSedeData());
    }
}