<?php

class ModuloData
{

    public static $tablename = "modulos";

    public function __construct()
    {
        $this->id = "";
        $this->id_padre = "";
        $this->icono = "";
        $this->nombre = "";
        $this->url = "";
        $this->orden = "";
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

    public function getModuloPadre()
    {
        return ModuloData::getById($this->id_padre);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (id_padre,icono,nombre,url,orden,estado,fecha_creacion,usuario_creacion,usuario_actualizacion,fecha_actualizacion) ";
        $sql .= "value (\"$this->id_padre\",\"$this->icono\",\"$this->nombre\",\"$this->url\",\"$this->orden\",\"$this->estado\",\"$this->fecha_creacion\",\"$this->usuario_creacion\",";
        $sql .= "\"$this->usuario_actualizacion\",\"$this->fecha_actualizacion\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set set id_padre = \"$this->id_padre\",icono = \"$this->icono\",nombre = \"$this->nombre\",url = \"$this->url\",";
        $sql .= "orden = \"$this->orden\",fecha_actualizacion = \"$this->fecha_actualizacion\",usuario_actualizacion = \"$this->usuario_actualizacion\" ";
        $sql .= "where id = \"$this->id\"";
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
        return Model::one($query[0], new ModuloData());
    }

    public static function getAll($estado = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        $sql .= "order by nombre asc,orden asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ModuloData());
    }

    public static function getAllPrincipal()
    {
        $sql = "select * from " . self::$tablename . " where estado = 1 and id_padre = 0 order by nombre asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ModuloData());
    }

    public static function getSubModulo($idModulo)
    {
        $sql = "select * from " . self::$tablename . " where estado = 1 and id_padre = \"$idModulo\" order by orden asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ModuloData());
    }
}