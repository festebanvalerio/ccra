<?php

class EquivalenciaData
{

    public static $tablename = "equivalencias";
 
    public function __construct()
    {
        $this->id = "";
        $this->insumo = "";
        $this->unidad_base = "";
        $this->factor = "";
        $this->unidad_alternativa = "";
        $this->estado = "";
    }

    public function getInsumo()
    {
        return InsumoData::getById($this->insumo);
    }
    
    public function getUnidadBase()
    {
        return UnidadData::getById($this->unidad_base);
    }
     
    public function getUnidadAlternativa()
    {
        return UnidadData::getById($this->unidad_alternativa);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (insumo,unidad_base,factor,unidad_alternativa,estado) value (\"$this->insumo\",\"$this->unidad_base\",\"$this->factor\",";
        $sql .= "\"$this->unidad_alternativa\",\"$this->estado\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set factor = \"$this->factor\" where id = \"$this->id\"";
        return Executor::doit($sql);
    }
    
    public function delete()
    {
        $sql = "update " . self::$tablename . " set estado = \"$this->estado\" where id = \"$this->id\"";        
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new EquivalenciaData());
    }
    
    public static function getAllByInsumo($estado = "", $insumo = "", $unidadBase = "", $unidadAlternativa = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($insumo != "") {
            $sql .= "and insumo = \"$insumo\" ";
        }
        if ($unidadBase != "") {
            $sql .= "and unidad_base = \"$unidadBase\" ";
        }
        if ($unidadAlternativa != "") {
            $sql .= "and unidad_alternativa = \"$unidadAlternativa\" ";
        }
        $sql .= "order by insumo asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new EquivalenciaData());
    }
}