<?php

class CorrelativoData
{

    public static $tablename = "correlativos";

    public function __construct()
    {
        $this->id = "";
        $this->sede = "";
        $this->anho = "";
        $this->tipo_documento = "";
        $this->serie = "";
        $this->secuencia = "";
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (sede,anho,tipo_documento,serie,secuencia) value (\"$this->sede\",\"$this->anho\",\"$this->tipo_documento\",";
        $sql .= "\"$this->serie\",\"$this->secuencia\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set secuencia = \"$this->secuencia\" where id = \"$this->id\"";
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new CorrelativoData());
    }

    public static function getBySecuencia($sede, $anho, $tipoDocumento)
    {
        $sql = "select * from " . self::$tablename . " where sede = \"$sede\" and anho = \"$anho\" and tipo_documento = \"$tipoDocumento\"";        
        $query = Executor::doit($sql);
        return Model::one($query[0], new CorrelativoData());
    }
}