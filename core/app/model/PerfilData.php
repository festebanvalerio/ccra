<?php

class PerfilData
{

    public static $tablename = "perfiles";

    public function __construct()
    {
        $this->id = "";
        $this->nombre = "";
        $this->indicador = "";
        $this->estado = "";
        $this->fecha_creacion = "";
        $this->usuario_creacion = "";
        $this->fecha_actualizacion = "";
        $this->usuario_actualizacion = "";
    }

    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (nombre,indicador,estado,fecha_creacion,usuario_creacion,usuario_actualizacion,fecha_actualizacion) ";
        $sql .= "value (\"$this->nombre\",\"$this->indicador\",\"$this->estado\",\"$this->fecha_creacion\",\"$this->usuario_creacion\",\"$this->usuario_actualizacion\",";
        $sql .= "\"$this->fecha_actualizacion\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set nombre = \"$this->nombre\",indicador = \"$this->indicador\",fecha_actualizacion = \"$this->fecha_actualizacion\",";
        $sql .= "usuario_actualizacion = \"$this->usuario_actualizacion\" where id = \"$this->id\"";
        return Executor::doit($sql);
    }

    public function delete()
    {
        $sql = "update " . self::$tablename . " set estado = \"$this->estado\",fecha_actualizacion = \"$this->fecha_actualizacion\",";
        $sql .= "usuario_actualizacion = \"$this->usuario_actualizacion\" where id = \"$this->id\"";
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new PerfilData());
    }

    public static function getAll($estado = "")
    {
        $sql = "select * from " . self::$tablename . " ";
        if ($estado != "") {
            $sql .= "where estado = \"$estado\" ";
        }
        $sql .= "order by nombre asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PerfilData());
    }
    
    public static function getInfoPerfil($estado, $indicador)
    {
        $sql = "select * from " . self::$tablename . " where estado = \"$estado\" and indicador = \"$indicador\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new PerfilData());
    }
    
    public static function getAllPerfilRepetido($estado, $nombre, $id = "")
    {
        $sql = "select * from " . self::$tablename . " where estado = \"$estado\" and nombre = \"$nombre\" ";
        if ($id != "") {
            $sql .= "and id not in (\"$id\")";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new PerfilData());
    }
}