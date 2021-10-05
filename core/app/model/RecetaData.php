<?php

class RecetaData
{

    public static $tablename = "recetas";

    public function __construct()
    {
        $this->id = "";
        $this->sede = "";
        $this->producto = "";
        $this->costo = "";
        $this->descripcion = "";
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
    
    public function getProducto()
    {
        return ProductoData::getById($this->producto);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (sede,producto,costo,descripcion,estado,fecha_creacion,usuario_creacion,usuario_actualizacion,fecha_actualizacion) value ";
        $sql .= "(\"$this->sede\",\"$this->producto\",\"$this->costo\",\"$this->descripcion\",\"$this->estado\",\"$this->fecha_creacion\",\"$this->usuario_creacion\",";
        $sql .= "\"$this->usuario_actualizacion\",\"$this->fecha_actualizacion\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set costo = \"$this->costo\",estado = \"$this->estado\",fecha_actualizacion = \"$this->fecha_actualizacion\",";
        $sql .= "usuario_actualizacion = \"$this->usuario_actualizacion\" where id = \"$this->id\"";
        return Executor::doit($sql);
    }

    public function delete()
    {
        $sql = "delete from " . self::$tablename . " where id = \"$this->id\"";
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new RecetaData());
    }

    public static function getByProducto($estado = "", $producto = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($producto != "") {
            $sql .= "and producto = \"$producto\" ";
        }        
        $query = Executor::doit($sql);
        return Model::one($query[0], new RecetaData());
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
        $query = Executor::doit($sql);
        return Model::many($query[0], new RecetaData());
    }
}