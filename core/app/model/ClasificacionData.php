<?php

class ClasificacionData
{

    public static $tablename = "clasificaciones";

    public function __construct()
    {
        $this->id = "";
        $this->sede = "";
        $this->nombre = "";
        $this->estado = "";
        $this->fecha_creacion = "";
        $this->usuario_creacion = "";
        $this->fecha_actualizacion = "";
        $this->usuario_actualizacion = "";
    }

    public function getSede()
    {
        return SedeData::getById($this->sede);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (sede,nombre,estado,fecha_creacion,usuario_creacion,usuario_actualizacion,fecha_actualizacion) ";
        $sql .= "value (\"$this->sede\",\"$this->nombre\",\"$this->estado\",\"$this->fecha_creacion\",\"$this->usuario_creacion\",";
        $sql .= "\"$this->usuario_actualizacion\",\"$this->fecha_actualizacion\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set nombre = \"$this->nombre\",fecha_actualizacion = \"$this->fecha_actualizacion\",";
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
        return Model::one($query[0], new ClasificacionData());
    }

    public static function getAll($estado = "", $sede = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($sede != "") {
            $sql .= "and sede = \"$sede\" ";
        }
        $sql .= "order by nombre asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ClasificacionData());
    }
    
    public static function getAllClasificacionRepetida($estado, $sede, $nombre, $id = "")
    {
        $sql = "select * from " . self::$tablename . " where estado = \"$estado\" and sede = \"$sede\" and nombre = \"$nombre\" ";
        if ($id != "") {
            $sql .= "and id not in (\"$id\")";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new ClasificacionData());
    }
}