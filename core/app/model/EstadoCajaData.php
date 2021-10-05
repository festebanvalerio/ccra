<?php

class EstadoCajaData
{

    public static $tablename = "estados_caja";

    public function __construct()
    {
        $this->id = ""; 
        $this->nombre = "";
        $this->opcion = "";
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new EstadoCajaData());
    }

    public static function getAll()
    {
        $sql = "select * from " . self::$tablename . " order by id asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new EstadoCajaData());
    }
}