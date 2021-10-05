<?php

class UnidadData
{

    public static $tablename = "unidades";

    public function __construct()
    {
        $this->id = "";
        $this->abreviatura = "";
        $this->nombre = "";        
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
        $sql = "insert into " . self::$tablename . " (abreviatura,nombre,estado,fecha_creacion,usuario_creacion,usuario_actualizacion,fecha_actualizacion) ";
        $sql .= "value (\"$this->abreviatura\",\"$this->nombre\",\"$this->estado\",\"$this->fecha_creacion\",\"$this->usuario_creacion\",\"$this->usuario_actualizacion\",\"$this->fecha_actualizacion\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set abreviatura = \"$this->abreviatura\",nombre = \"$this->nombre\",fecha_actualizacion = \"$this->fecha_actualizacion\",";
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
        return Model::one($query[0], new UnidadData());
    }

    public static function getAll($estado = "")
    {
        $sql = "select * from " . self::$tablename . " ";
        if ($estado != "") {
            $sql .= "where estado = \"$estado\" ";
        }
        $sql .= "order by nombre asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new UnidadData());
    }
    
    public static function getAllUnidadRepetida($estado, $abreviatura, $nombre, $id = "")
    {
        $sql = "select * from " . self::$tablename . " where estado = \"$estado\" and abreviatura = \"$abreviatura\" and nombre = \"$nombre\" ";
        if ($id != "") {
            $sql .= "and id not in (\"$id\")";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new UnidadData());
    }
}