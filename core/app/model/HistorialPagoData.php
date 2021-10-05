<?php

class HistorialPagoData
{

    public static $tablename = "historial_pagos";

    public function __construct()
    {
        $this->id = "";
        $this->pago = "";
        $this->fecha = "";
        $this->forma_pago = "";
        $this->monto_efectivo = "";
        $this->monto_tarjeta = "";
        $this->monto_credito = "";
        $this->tipo_tarjeta = "";
        $this->num_operacion = "";
        $this->num_documento = "";
        $this->cliente = "";
        $this->usuario = "";
        $this->caja = "";
        $this->indicador_cierre = "";
        $this->comprobante = "";
        $this->estado = "";
    }

    public function getPago()
    {
        return PagoData::getById($this->pago);    
    }
    
    public function getFormaPago()
    {
        return ParametroData::getById($this->forma_pago);
    }
    
    public function getTipoTarjeta()
    {
        return ParametroData::getById($this->tipo_tarjeta);
    }
    
    public function getCaja()
    {
        return CajaData::getById($this->caja);
    }
    
    public function getComprobante()
    {
        return ComprobanteData::getById($this->comprobante);
    }
    
    public function getDetalleHistorialPago()
    {
        return DetalleHistorialPagoData::getAllByHistorialPago($this->id);
    }

    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }
        
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (pago,fecha,forma_pago,monto_efectivo,monto_tarjeta,monto_credito,tipo_tarjeta,num_operacion,num_documento,cliente,";
        $sql .= "usuario,caja,estado) value (\"$this->pago\",\"$this->fecha\",\"$this->forma_pago\",\"$this->monto_efectivo\",\"$this->monto_tarjeta\",\"$this->monto_credito\",";
        $sql .= "\"$this->tipo_tarjeta\",\"$this->num_operacion\",\"$this->num_documento\",\"$this->cliente\",\"$this->usuario\",\"$this->caja\",\"$this->estado\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set forma_pago = \"$this->forma_pago\",tipo_tarjeta = \"$this->tipo_tarjeta\",num_operacion = \"$this->num_operacion\",";
        $sql .= "monto_efectivo = \"$this->monto_efectivo\",monto_tarjeta = \"$this->monto_tarjeta\",indicador_cierre = \"$this->indicador_cierre\",";
        $sql .= "comprobante = \"$this->comprobante\" where id = \"$this->id\"";        
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
        return Model::one($query[0], new HistorialPagoData());
    }
    
    public static function getByPago($idPago)
    {
        $sql = "select * from " . self::$tablename . " where pago = \"$idPago\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new HistorialPagoData());
    }
    
    public static function getAllByPago($pago = "")
    {
        $sql = "select * from " . self::$tablename . " where estado = 1 ";        
        if ($pago != "") {
            $sql .= "and pago = \"$pago\" ";
        }
        $query = Executor::doit($sql);        
        return Model::many($query[0], new HistorialPagoData());
    }      
    
    public static function getTotalXFormaPagoMixta($estado = "", $caja = "", $fecha = "", $indicador = "", $opcion = "", $tipoTarjeta = "")
    {
        $sql = "";
        if ($opcion == "1") {
            $sql = "select sum(hp.monto_efectivo) as total from " . self::$tablename . " hp ";
        } else {
            $sql = "select sum(hp.monto_tarjeta) as total from " . self::$tablename . " hp ";
        }
        $sql .= "join pagos pa on pa.id = hp.pago ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "join parametros par on par.id = hp.forma_pago ";
        $sql .= "where pe.estado in (1,2) and par.valor1 = 2 ";
        if ($estado != "") {
            $sql .= "and hp.estado = \"$estado\" and pa.estado = \"$estado\" ";
        }
        if ($caja != "") {
            $sql .= "and hp.caja = \"$caja\" ";
        }
        if ($fecha != "") {
            $sql .= "and date(hp.fecha) = \"$fecha\" ";
        }
        if ($indicador != "") {
            if ($indicador == 0) {
                $sql .= "and hp.indicador_cierre = \"$indicador\" ";
            } else {
                $sql .= "and hp.indicador_cierre = \"$indicador\" ";
            }
        }
        if ($opcion == 2) {
            $sql .= "and hp.tipo_tarjeta = \"$tipoTarjeta\" ";
        }        
        $query = Executor::doit($sql);
        return Model::one($query[0], new HistorialPagoData());
    }
    
    public static function getAllByCierre($estado = "", $caja = "", $fecha = "", $indicador = "")
    {
        $sql = "select hp.* from " . self::$tablename . " hp ";
        $sql .= "join pagos pa on pa.id = hp.pago ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "join parametros par on par.id = hp.forma_pago "; 
        $sql .= "where pe.estado in (1,2) and par.valor1 != 3 ";
        if ($estado != "") {
            $sql .= "and hp.estado = \"$estado\" and pa.estado = \"$estado\" ";
        }
        if ($caja != "") {
            $sql .= "and hp.caja = \"$caja\" ";
        }
        if ($fecha != "") {
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(hp.fecha) = \"$fecha\" ";
        }
        if ($indicador == 0) {
            $sql .= "and hp.indicador_cierre = \"$indicador\" ";
        } else {
            $sql .= "and hp.indicador_cierre = \"$indicador\" ";
        }
        $sql .= "order by date(hp.fecha) asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new HistorialPagoData());
    }
    
    public static function getAllByReporteCierre($estado = "", $caja = "", $fecha = "", $indicador = "", $valor = "", $opcion = "")
    {
        $sql = "";
        if ($opcion == 1) {
            $sql .= "select par1.nombre AS forma_pago, dhp.nom_categoria AS categoria, dhp.nom_producto as producto,";
        } else if ($opcion == 2) {
            $sql .= "select par2.nombre AS tipo_tarjeta, dhp.nom_categoria AS categoria, dhp.nom_producto as producto,";
        }
        $sql .= "sum(dhp.cantidad) as cantidad, sum(dhp.total) as total ";
        $sql .= "from " . self::$tablename . " hp ";
        $sql .= "join detalle_historial_pagos dhp on dhp.historial_pago = hp.id ";
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
                $sql .= "and hp.tipo_tarjeta = \"$valor\" and par1.valor1 = 1 ";
                $sql .= "group by tipo_tarjeta, categoria, producto ";
                $sql .= "order by tipo_tarjeta asc, categoria asc, producto asc, total asc";
            }
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new HistorialPagoData());
    }
    
    public static function getAllDescuentoXCierre($fecha, $caja, $cierre, $valor, $opcion)
    {
        $sql = "select ifnull(pa.monto_descuento,0) as descuento from " . self::$tablename . " hp ";
        $sql .= "join pagos pa on pa.id = hp.pago ";
        $sql .= "where hp.estado = 1 and date(hp.fecha) = \"$fecha\" and hp.caja = \"$caja\" and pa.monto_descuento > 0 ";
        if ($cierre != "") {
            $sql .= "and hp.indicador_cierre = \"$cierre\" "; 
        }
        if ($opcion == 1) {
            $sql .= "and hp.forma_pago = \"$valor\" ";
        } else if ($opcion == 2) {
            $sql .= "and hp.tipo_tarjeta = \"$valor\" ";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new HistorialPagoData());
    }    
        
}