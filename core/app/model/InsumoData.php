<?php

class InsumoData
{

    public static $tablename = "insumos";

    public function __construct()
    {
        $this->id = "";
        $this->sede = "";
        $this->unidad = "";
        $this->nombre = "";
        $this->costo = "";
        $this->clasificacion = "";
        $this->indicador = "";
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
    
    public function getUnidad()
    {
        return UnidadData::getById($this->unidad);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }
    
    public function getClasificacion()
    {
        return ClasificacionData::getById($this->clasificacion);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (sede,unidad,nombre,costo,clasificacion,indicador,estado,fecha_creacion,usuario_creacion,usuario_actualizacion,";
        $sql .= "fecha_actualizacion) value (\"$this->sede\",\"$this->unidad\",\"$this->nombre\",\"$this->costo\",\"$this->clasificacion\",\"$this->indicador\",";
        $sql .= "\"$this->estado\",\"$this->fecha_creacion\",\"$this->usuario_creacion\",\"$this->usuario_actualizacion\",\"$this->fecha_actualizacion\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set unidad = \"$this->unidad\",nombre = \"$this->nombre\",costo = \"$this->costo\",indicador = \"$this->indicador\",";
        $sql .= "clasificacion = \"$this->clasificacion\",fecha_actualizacion = \"$this->fecha_actualizacion\",usuario_actualizacion = \"$this->usuario_actualizacion\" ";
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
        return Model::one($query[0], new InsumoData());
    }

    public static function getAll($estado = "", $sede = "", $indicador = "", $clasificacion = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($sede != "") {
            $sql .= "and sede = \"$sede\" ";
        }
        if ($indicador != "") {
            $sql .= "and indicador = \"$indicador\" ";
        }
        if ($clasificacion != "") {
            $sql .= "and clasificacion = \"$clasificacion\" ";
        }
        $sql .= "order by nombre asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new InsumoData());
    }
    
    public static function getAllInsumoRepetido($estado, $sede, $unidad, $nombre, $id = "")
    {
        $sql = "select * from " . self::$tablename . " where estado = \"$estado\" and sede = \"$sede\" and unidad = \"$unidad\" and nombre = \"$nombre\" ";
        if ($id != "") {
            $sql .= "and id not in (\"$id\")";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new InsumoData());
    }
}