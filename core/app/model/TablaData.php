<?php

class TablaData
{

    public static $tablename = "tablas";

    public function __construct()
    {
        $this->id = "";
        $this->nombre = "";        
        $this->estado = "";        
    }

    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (nombre,estado) value (\"$this->nombre\",\"$this->estado\")";
        return Executor::doit($sql);
    }

    public function delete()
    {
        $sql = "update " . self::$tablename . " set estado = \"$this->estado\" where id = \"$this->id\"";
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new TablaData());
    }

    public static function getByNombre($nombre)
    {
        $sql = "select * from " . self::$tablename . " where nombre = \"$nombre\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new TablaData());
    }
    
    public static function getAll($estado = "")
    {
        $sql = "select * from " . self::$tablename . " ";
        if ($estado != "") {
            $sql .= "where estado = \"$estado\" ";
        }
        $sql .= "order by nombre asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new TablaData());
    }
}