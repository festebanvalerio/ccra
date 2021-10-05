<?php

class DetalleIngresoData
{

    public static $tablename = "detalle_ingresos";
    
    public function __construct()
    {
        $this->id = "";
        $this->ingreso = "";
        $this->insumo = "";
        $this->unidad = "";        
        $this->cantidad = "";
        $this->estado = "";
    }

    public function getIngreso()
    {
        return IngresoData::getById($this->oc);    
    }
    
    public function getInsumo()
    {
        return InsumoData::getById($this->insumo);
    }    
    
    public function getUnidad()
    {
        return UnidadData::getById($this->unidad);
    }
    
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (ingreso,insumo,unidad,cantidad,estado) value (\"$this->ingreso\",\"$this->insumo\",\"$this->unidad\",\"$this->cantidad\",";
        $sql .= "\"$this->estado\")";
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new DetalleIngresoData());
    }
    
    public static function getAllByIngreso($ingreso)
    {
        $sql = "select * from " . self::$tablename . " where ingreso = \"$ingreso\"";
        $sql .= "order by id asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleIngresoData());
    }
    
    public static function getAllByReport($sede = "", $almacen = "", $fechaInicio = "", $fechaFin = "", $estado = "")
    {
        $sql = "select ing.id as id,i.nombre as insumo,ding.cantidad as cantidad,u.nombre as unidad ";
        $sql .= "from " . self::$tablename . " ding ";
        $sql .= "join ingresos ing on ing.id = ding.ingreso ";
        $sql .= "join insumos i on i.id = ding.insumo ";
        $sql .= "join unidades u on u.id= ding.unidad ";
        $sql .= "where 1 = 1 and ding.estado = 1 ";
        if ($sede != "") {
            $sql .= "and ing.sede = \"$sede\" ";
        }
        if ($almacen != "") {
            $sql .= "and ing.almacen = \"$almacen\" ";
        }
        if ($fechaInicio != "") {
            $arrFecha = explode("/", $fechaInicio);
            $fechaInicio = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and ing.fecha >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $arrFecha = explode("/", $fechaFin);
            $fechaFin = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and ing.fecha <= \"$fechaFin\" ";
        }
        if ($estado != "") {
            $sql .= "and ing.estado = \"$estado\" ";
        }
        $sql .= "order by ing.fecha desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleIngresoData());
    }
}
