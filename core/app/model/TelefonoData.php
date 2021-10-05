<?php

class TelefonoData
{

    public static $tablename = "telefonos";

    public function __construct()
    {
        $this->id = "";
        $this->telefono = "";
        $this->contacto = "";
        $this->direccion = "";
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (telefono,contacto,direccion) value (\"$this->telefono\",\"$this->contacto\",\"$this->direccion\")";
        return Executor::doit($sql);
    }
    
    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new TelefonoData());
    }

    public static function getAll()
    {
        $sql = "select * from " . self::$tablename . " order by telefono asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new TelefonoData());
    }
    
    public static function getByTelefono($telefono)
    {
        $sql = "select * from " . self::$tablename . " where telefono = \"$telefono\" ";        
        $query = Executor::doit($sql);
        return Model::many($query[0], new TelefonoData());
    }
}