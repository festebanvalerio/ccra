<?php

class LogData
{

    public static $tablename = "log_sunat";

    public function __construct()
    {
        $this->id = "";
        $this->documento = "";
        $this->indicador = "";
        $this->mensaje = "";
        $this->fecha_creacion = "";
        $this->usuario_creacion = "";
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (documento,indicador,mensaje,fecha_creacion,usuario_creacion) ";
        $sql .= "value (\"$this->documento\",\"$this->indicador\",\"$this->mensaje\",\"$this->fecha_creacion\",\"$this->usuario_creacion\")";
        return Executor::doit($sql);
    }
    
    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new LogData());
    }

    public static function getAll()
    {
        $sql = "select * from " . self::$tablename . " order by fecha_creacion desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new LogData());
    }    
}