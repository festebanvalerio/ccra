<?php

class DetalleHistorialPagoData
{

    public static $tablename = "detalle_historial_pagos";

    public function __construct()
    {
        $this->id = "";
        $this->historial_pago = "";        
        $this->nom_categoria = "";
        $this->producto = "";
        $this->nom_producto = "";
        $this->precio = "";
        $this->cantidad = "";
        $this->total = "";        
        $this->estado = "";
    }

    public function getHistorialPago()
    {
        return HistorialPagoData::getById($this->historial_pago);    
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }
        
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (historial_pago,nom_categoria,producto,nom_producto,precio,cantidad,total,estado) value ";
        $sql .= "(\"$this->historial_pago\",\"$this->nom_categoria\",\"$this->producto\",\"$this->nom_producto\",\"$this->precio\",\"$this->cantidad\",\"$this->total\",";
        $sql .= "\"$this->estado\")";
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
        return Model::one($query[0], new DetalleHistorialPagoData());
    }
    
    public static function getAllByHistorialPago($historialPago, $estado = "", $producto = "")
    {
        $sql = "select * from " . self::$tablename . " where historial_pago = \"$historialPago\" ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\"  "; 
        } else {
            $sql .= "and estado = 1 ";
        }
        if ($producto != "") {
            $sql .= "and producto = \"$producto\" ";
        }        
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleHistorialPagoData());
    }
    
    public static function getAllPagoXPedido($estado = "", $pedido = "")
    {        
        $sql = "select hp.* from " . self::$tablename . " hp ";
        $sql .= "join pagos pa on pa.id = hp.pago where 1=1 ";
        if ($estado != "") {
            $sql .= "and hp.estado = \"$estado\" ";
        }
        if ($pedido != "") {
            $sql .= "and pa.pedido = \"$pedido\" ";
        }
        $sql .= "order by hp.fecha desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleHistorialPagoData());
    }
    
    public static function getAllByCierre($estado = "", $caja = "", $fecha = "", $indicador = "", $formaPago = "")
    {
        $sql = "select hp.id AS id, par3.nombre as tipo, par1.nombre AS forma_pago, par2.nombre as tipo_tarjeta, hp.categoria AS categoria, hp.producto as producto,";
        $sql .= "hp.cantidad as cantidad, hp.total as total ";
        $sql .= "from " . self::$tablename . " hp ";
        $sql .= "join pagos pa on pa.id = hp.pago ";
        $sql .= "join parametros par1 on par1.id = hp.forma_pago ";
        $sql .= "left join parametros par2 on par2.id = hp.tipo_tarjeta ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "join parametros par3 on par3.id = pe.tipo ";
        $sql .= "where 1=1 ";
        if ($estado != "") {
            $sql .= "and hp.estado = \"$estado\" ";
        }
        if ($caja != "") {
            $sql .= "and hp.caja = \"$caja\" ";
        }
        if ($fecha != "") {
            $sql .= "and date(hp.fecha) = \"$fecha\" ";
        }
        if ($indicador == 0) {
            $sql .= "and hp.indicador_cierre is null ";
        } else {
            $sql .= "and hp.indicador_cierre = \"$indicador\" ";
        }
        if ($formaPago != "") {
            $sql .= "and hp.forma_pago = \"$formaPago\" ";
        }
        $sql .= "order by tipo, forma_pago asc, tipo_tarjeta asc, categoria asc, total asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleHistorialPagoData());
    }
    
    public static function getAllByReporteCierre($estado = "", $caja = "", $fecha = "", $indicador = "", $valor = "", $opcion = "")
    {
        $sql = "";
        if ($opcion == 1) {
            $sql .= "select par1.nombre AS forma_pago, hp.categoria AS categoria, hp.producto as producto,";
        } else if ($opcion == 2) {
            $sql .= "select par2.nombre AS tipo_tarjeta, hp.categoria AS categoria, hp.producto as producto,";
        }
        $sql .= "sum(hp.cantidad) as cantidad, sum(hp.total) as total ";
        $sql .= "from " . self::$tablename . " hp ";
        $sql .= "join pagos pa on pa.id = hp.pago ";
        $sql .= "join parametros par1 on par1.id = hp.forma_pago ";
        $sql .= "left join parametros par2 on par2.id = hp.tipo_tarjeta ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "join parametros par3 on par3.id = pe.tipo ";
        $sql .= "where 1=1 ";
        if ($estado != "") {
            $sql .= "and hp.estado = \"$estado\" ";
        }
        if ($caja != "") {
            $sql .= "and hp.caja = \"$caja\" ";
        }
        if ($fecha != "") {
            $sql .= "and date(hp.fecha) = \"$fecha\" ";
        }
        if ($indicador != "") {
            $sql .= "and hp.indicador_cierre = \"$indicador\" ";
        }
        if ($opcion == 1) {
            if ($valor != "") {
                $sql .= "and hp.forma_pago = \"$valor\" ";
                $sql .= "group by forma_pago, categoria, producto ";
                $sql .= "order by forma_pago asc, categoria asc, producto asc, total asc";
            }
        } else if ($opcion == 2) {
            if ($valor != "") {
                $sql .= "and hp.tipo_tarjeta = \"$valor\" ";
                $sql .= "group by tipo_tarjeta, categoria, producto ";
                $sql .= "order by tipo_tarjeta asc, categoria asc, producto asc, total asc";
            }
        }        
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleHistorialPagoData());
    }
    
    public static function getAllDescuentoXCierre($fecha, $caja, $cierre, $valor, $opcion)
    {
        $sql = "select distinct ifnull(pa.monto_descuento,0) as descuento from " . self::$tablename . " hp ";
        $sql .= "join pagos pa on pa.id = hp.pago ";
        $sql .= "where hp.estado = 1 and date(hp.fecha) = \"$fecha\" and hp.caja = \"$caja\" AND hp.indicador_cierre = \"$cierre\"";
        if ($opcion == 1) {
            $sql .= "and hp.forma_pago = \"$valor\" ";
        } else if ($opcion == 2) {
            $sql .= "and hp.tipo_tarjeta = \"$valor\" ";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleHistorialPagoData());
    }    
        
}