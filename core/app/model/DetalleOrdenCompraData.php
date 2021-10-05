<?php

class DetalleOrdenCompraData
{

    public static $tablename = "detalle_ocs";
    
    public function __construct()
    {
        $this->id = "";
        $this->oc = "";
        $this->insumo = "";
        $this->unidad_almacen = "";
        $this->unidad_compra = "";
        $this->costo = "";
        $this->cantidad = "";
        $this->indicador = "";
        $this->estado = "";
    }

    public function getOrdenCompra()
    {
        return OrdenCompraData::getById($this->oc);    
    }
    
    public function getInsumo()
    {
        return InsumoData::getById($this->insumo);
    }    
    
    public function getUnidadAlmacen()
    {
        return UnidadData::getById($this->unidad_almacen);
    }
    
    public function getUnidadCompra()
    {
        return UnidadData::getById($this->unidad_compra);
    }
    
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (oc,insumo,unidad_almacen,unidad_compra,costo,cantidad,indicador,estado) value (\"$this->oc\",";
        $sql .= "\"$this->insumo\",\"$this->unidad_almacen\",\"$this->unidad_compra\",\"$this->costo\",\"$this->cantidad\",\"$this->indicador\",\"$this->estado\")";
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new DetalleOrdenCompraData());
    }
    
    public static function getAllByOrdenCompra($ordenCompra)
    {
        $sql = "select * from " . self::$tablename . " where oc = \"$ordenCompra\"";
        $sql .= "order by id asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleOrdenCompraData());
    }
    
    public static function getAllByReport($sede = "", $almacen = "", $fechaInicio = "", $fechaFin = "", $estado = "")
    {
        $sql = "select o.id as id,pa.nombre as tipo_documento,o.num_documento as num_documento,o.ruc as ruc,o.razon_social as razon_social,";
        $sql .= "i.nombre as insumo,doc.costo as costo,doc.cantidad as cantidad,u.nombre as unidad ";
        $sql .= "from " . self::$tablename . " doc ";
        $sql .= "join ocs o on o.id = doc.oc ";
        $sql .= "join parametros pa on pa.id = o.tipo_documento ";
        $sql .= "join insumos i on i.id = doc.insumo ";
        $sql .= "join unidades u on u.id = doc.unidad_compra ";
        $sql .= "where 1 = 1 and doc.estado = 1 ";
        if ($sede != "") {
            $sql .= "and o.sede = \"$sede\" ";
        }
        if ($almacen != "") {
            $sql .= "and o.almacen = \"$almacen\" ";
        }
        if ($fechaInicio != "") {
            $arrFecha = explode("/", $fechaInicio);
            $fechaInicio = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and o.fecha >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $arrFecha = explode("/", $fechaFin);
            $fechaFin = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and o.fecha <= \"$fechaFin\" ";
        }
        if ($estado != "") {
            $sql .= "and o.estado = \"$estado\" ";
        }
        $sql .= "order by o.fecha desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleOrdenCompraData());
    }
}
