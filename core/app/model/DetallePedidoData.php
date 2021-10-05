<?php

class DetallePedidoData
{

    public static $tablename = "detalle_pedidos";

    public function __construct()
    {
        $this->id = "";
        $this->pedido = "";
        $this->producto = "";
        $this->nom_producto = "";
        $this->tipo = "";
        $this->categoria = "";
        $this->comentario = "";
        $this->cantidad = "";
        $this->cantidad_pagada = "";
        $this->precio_costo = "";
        $this->precio_venta = "";
        $this->precio_real = "";
        $this->subtotal = "";
        $this->igv = "";
        $this->total = "";
        $this->estado = "";
        $this->usuario_creacion = "";
        $this->fecha_creacion = "";
        $this->usuario_actualizacion = "";
        $this->fecha_actualizacion = "";
        $this->fecha_comanda = "";   
    }

    public function getProducto()
    {
        return ProductoData::getById($this->producto);
    }
    
    public function getPedido()
    {
        return PedidoData::getById($this->pedido);
    }
    
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (pedido,producto,nom_producto,tipo,categoria,cantidad,comentario,precio_costo,precio_venta,precio_real,";
        $sql .= "subtotal,igv,total,estado,usuario_creacion,fecha_creacion,usuario_actualizacion,fecha_actualizacion) value (\"$this->pedido\",\"$this->producto\",";
        $sql .= "\"$this->nom_producto\",\"$this->tipo\",\"$this->categoria\",\"$this->cantidad\",\"$this->comentario\",\"$this->precio_costo\",\"$this->precio_venta\",";
        $sql .= "\"$this->precio_real\",\"$this->subtotal\",\"$this->igv\",\"$this->total\",\"$this->estado\",\"$this->usuario_creacion\",\"$this->fecha_creacion\",";
        $sql .= "\"$this->usuario_actualizacion\",\"$this->fecha_actualizacion\")";
        return Executor::doit($sql);
    }
    
    public function update()
    {
        $sql = "update " . self::$tablename . " set cantidad = \"$this->cantidad\",cantidad_pagada = \"$this->cantidad_pagada\",comentario = \"$this->comentario\",";
        $sql .= "subtotal = \"$this->subtotal\",igv = \"$this->igv\",total = \"$this->total\",fecha_comanda = \"$this->fecha_comanda\" where id = \"$this->id\"";
        return Executor::doit($sql);
    }
    
    public function delete()
    {
        $sql = "update " . self::$tablename . " set estado = \"$this->estado\", usuario_actualizacion = \"$this->usuario_actualizacion\", ";
        $sql .= "fecha_actualizacion = \"$this->fecha_actualizacion\" where id = \"$this->id\"";        
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new DetallePedidoData());
    }
    
    public static function getProductosXPedido($pedido, $producto = "")
    {
        $sql = "select * from " . self::$tablename . " where pedido = \"$pedido\" and estado = 1 ";
        if ($producto != "") {
            $sql .= "and nom_producto = \"$producto\" ";
        }
        $sql .= "order by id asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetallePedidoData());
    }
    
    public static function getAllProductoXArea($pedido, $area = "", $indicador = 0)
    {
        $sql = "select dp.id as id, dp.nom_producto as producto, dp.cantidad as cantidad, dp.total as total, dp.comentario as comentario ";
        if ($area != "") {
            $sql .= ",a.nombre as area ";
        }
        $sql .= "from " . self::$tablename . " dp ";
        $sql .= "join pedidos pe on pe.id = dp.pedido ";
        if ($area != "") {
            $sql .= "join productos_area pa on pa.producto = dp.producto ";        
            $sql .= "join areas a on a.id = pa.area ";
        }
        $sql .= "where pe.id = \"$pedido\" and dp.estado = 1 ";
        if ($indicador == 0) {
            $sql .= "and (dp.fecha_comanda is null or dp.fecha_comanda = '0000-00-00 00:00:00') ";
        }
        if ($area != "") {
            $sql .= "and a.id = \"$area\"";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetallePedidoData());
    }
    
    public static function getAllProductoEliminado($sede, $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select pe.id as id, dp.nom_producto as producto, dp.cantidad as cantidad, dp.precio_venta as precio_venta, dp.total as total, ";
        $sql .= "pe.fecha as fecha_creacion, dp.fecha_actualizacion as fecha_actualizacion, 'PRODUCTO ELIMINADO' as estado ";
        $sql .= " from " . self::$tablename . " dp ";
        $sql .= "join pedidos pe on pe.id = dp.pedido ";
        $sql .= "where pe.sede = \"$sede\" and dp.estado = 0 ";
        if ($fechaInicio != "") {
            $arrFecha = explode("/", $fechaInicio);
            $fecha = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(dp.fecha_actualizacion) >= \"$fecha\" ";
        }
        if ($fechaFin != "") {
            $arrFecha = explode("/", $fechaFin);
            $fecha = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(dp.fecha_actualizacion) >= \"$fecha\" ";
        }
        $sql .= "union ";
        $sql .= "select pe.id as id, dp.nom_producto as producto, dp.cantidad as cantidad, dp.precio_venta as precio_venta, dp.total as total, ";
        $sql .= "pe.fecha as fecha_creacion, pe.fecha_actualizacion as fecha_actualizacion, 'PEDIDO ELIMINADO' as estado ";
        $sql .= " from " . self::$tablename . " dp ";
        $sql .= "join pedidos pe on pe.id = dp.pedido ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado = 0 ";
        if ($fechaInicio != "") {
            $arrFecha = explode("/", $fechaInicio);
            $fecha = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(dp.fecha_actualizacion) >= \"$fecha\" ";
        }
        if ($fechaFin != "") {
            $arrFecha = explode("/", $fechaFin);
            $fecha = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(dp.fecha_actualizacion) >= \"$fecha\" ";
        }
        $sql .= "order by fecha_actualizacion desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetallePedidoData());
    }
}