<?php

class PedidoData
{

    public static $tablename = "pedidos";

    public function __construct()
    {
        $this->id = "";
        $this->sede = "";
        $this->piso = "";
        $this->mesa = "";        
        $this->num_comensales = "";
        $this->fecha = "";
        $this->descuento_programado = "";
        $this->descuento_pedido = "";
        $this->servicio = "";
        $this->subtotal = "";
        $this->igv = "";
        $this->total = "";
        $this->mozo = "";
        $this->tipo = "";
        $this->telefono = "";
        $this->direccion = "";
        $this->datos = "";
        $this->hora = "";
        $this->estado = "";
        $this->fecha_actualizacion = "";
        $this->usuario_actualizacion = "";        
    }

    public function getSede()
    {
        return SedeData::getById($this->sede);
    }
    
    public function getPiso()
    {
        return PisoData::getById($this->piso);
    }
    
    public function getPisoSede()
    {
        return PisoSedeData::getInfo($this->sede, $this->piso);
    }
    
    public function getMesaPisoSede($pisoSede)
    {
        return MesaPisoSedeData::getInfo($pisoSede, $this->mesa);
    }
    
    public function getMesa()
    {
        return MesaData::getById($this->mesa);
    }
    
    public function getPago()
    {
        return PagoData::getByPedido($this->id);
    }
    
    public function getUsuario()
    {
        return UsuarioData::getById($this->mozo);
    }
    
    public function getTipo()
    {
        return ParametroData::getById($this->tipo);
    }
    
    public function getEstado()
    {
        return EstadoPedidoData::getById($this->estado);
    }

    public function getAreaXPedido($id, $opcion)
    {
        return AreaData::getAllAreaXProducto($id, $opcion);
    }
    
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (sede,piso,mesa,num_comensales,fecha,descuento_programado,descuento_pedido,servicio,subtotal,igv,total,mozo,tipo,";
        $sql .= "telefono,direccion,datos,hora,estado,usuario_actualizacion,fecha_actualizacion) value (\"$this->sede\",\"$this->piso\",\"$this->mesa\",\"$this->num_comensales\",";
        $sql .= "\"$this->fecha\",\"$this->descuento_programado\",\"$this->descuento_pedido\",\"$this->servicio\",\"$this->subtotal\",\"$this->igv\",\"$this->total\",";
        $sql .= "\"$this->mozo\",\"$this->tipo\",\"$this->telefono\",\"$this->direccion\",\"$this->datos\",\"$this->hora\",\"$this->estado\",\"$this->usuario_actualizacion\",";
        $sql .= "\"$this->fecha_actualizacion\")";        
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set piso = \"$this->piso\",mesa = \"$this->mesa\",descuento_programado = \"$this->descuento_programado\",";
        $sql .= "descuento_pedido = \"$this->descuento_pedido\",estado = \"$this->estado\",fecha_actualizacion = \"$this->fecha_actualizacion\",";
        $sql .= "usuario_actualizacion = \"$this->usuario_actualizacion\",servicio = \"$this->servicio\",subtotal = \"$this->subtotal\",igv = \"$this->igv\",";
        $sql .= "total = \"$this->total\" where id = \"$this->id\"";        
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
        return Model::one($query[0], new PedidoData());
    }

    public static function getAll($estado = "", $sede = "", $tipo = "", $piso = "", $fechaInicio = "", $fechaFin = "", $mesero = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($sede != "") {
            $sql .= "and sede = \"$sede\" ";
        }
        if ($tipo != "") {
            $sql .= "and tipo = \"$tipo\" ";
        }
        if ($piso != "") {
            $sql .= "and piso = \"$piso\" ";
        }
        if ($fechaInicio != "") {
            $arrFecha = explode("/", $fechaInicio);
            $fechaInicio = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(fecha) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $arrFecha = explode("/", $fechaFin);
            $fechaFin = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(fecha) <= \"$fechaFin\" ";
        }
        if ($mesero != "") {
            $sql .= "and mozo = \"$mesero\" ";
        }
        $sql .= "order by fecha asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PedidoData());
    }
    
    public static function getMesaOcupadaXMozo($estado = "", $sede = "", $piso = "", $mesa = "", $mozo = "", $indicador = 0)
    {
        $sql = "select * from " . self::$tablename . " where 1 = 1 and date(fecha) = date(now()) ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($sede != "") {
            $sql .= "and sede = \"$sede\" ";
        }
        if ($piso != "") {
            $sql .= "and piso = \"$piso\" ";
        }
        if ($mesa != "") {
            $sql .= "and mesa = \"$mesa\" ";
        }
        if ($mozo != "") {
            if ($indicador == 0) {
                $sql .= "and mozo = \"$mozo\" ";
            } else {
                $sql .= "and mozo != \"$mozo\" ";
            }
        }        
        $sql .= "order by fecha asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PedidoData());
    }
    
    public static function getTipoDescuentoGroupByFecha($sede, $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select ifnull(sum(pe.descuento_programado),0) as dscto_programado, ifnull(sum(pe.descuento_pedido),0) as dscto_pedido ";
        $sql .= "from " . self::$tablename . " pe ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado in (1,2) ";
        if ($fechaInicio != "") {
            $sql .= "and date(pe.fecha) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $sql .= "and date(pe.fecha) <= \"$fechaFin\" ";
        }
        $query = Executor::doit($sql);        
        return Model::many($query[0], new PedidoData());
    }
    
    public static function getTipoDescuentoGroupByMes($sede, $anho = "", $mes = "")
    {
        $sql = "select ifnull(sum(pe.descuento_programado),0) as dscto_programado, ifnull(sum(pe.descuento_pedido),0) as dscto_pedido ";
        $sql .= "from " . self::$tablename . " pe ";
        $sql .= "join anho a on a.id = \"$anho\" ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado in (1,2) ";
        if ($anho != "") {
            $sql .= "and year(pe.fecha) = a.nombre ";
        }
        if ($mes != "") {
            $sql .= "and month(pe.fecha) = \"$mes\" ";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new PedidoData());
    }
    
    public static function getTipoDescuentoGroupByAnho($sede, $anho = "")
    {
        $sql = "select ifnull(sum(pe.descuento_programado),0) as dscto_programado, ifnull(sum(pe.descuento_pedido),0) as dscto_pedido ";
        $sql .= "from " . self::$tablename . " pe ";
        $sql .= "join anho a on a.id = \"$anho\" ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado in (1,2) ";
        if ($anho != "") {
            $sql .= "and year(pe.fecha) = a.nombre ";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new PedidoData());
    }
    
    // Reporte Pedido - Por rango
    public static function getPedidoGroupByFecha($sede, $fechaInicio = "", $fechaFin = "", $estado = "")
    {
        $sql = "select ifnull(sum(pe.total),0) as total from " . self::$tablename . " pe ";
        $sql .= "where pe.sede = \"$sede\" ";
        if ($estado == "") {
            $sql .= "and pe.estado in (1,2) ";
        } else {
            $sql .= "and pe.estado = 0 ";
        }
        if ($fechaInicio != "") {
            $sql .= "and date(pe.fecha) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $sql .= "and date(pe.fecha) <= \"$fechaFin\" ";
        }        
        $query = Executor::doit($sql);
        return Model::many($query[0], new PedidoData());
    }
    
    // Reporte Pedidos - Por mes
    public static function getPedidoGroupByMes($sede, $anho = "", $mes = "", $estado = "")
    {
        $sql = "select ifnull(sum(pe.total),0) as total from " . self::$tablename . " pe ";
        $sql .= "join anho a on a.id = \"$anho\" ";
        $sql .= "where pe.sede = \"$sede\" ";
        if ($estado == "") {
            $sql .= "and pe.estado in (1,2) ";
        } else {
            $sql .= "and pe.estado = 0 ";
        }
        if ($anho != "") {
            $sql .= "and year(pe.fecha) = a.nombre ";
        }
        if ($mes != "") {
            $sql .= "and month(pe.fecha) = \"$mes\" ";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new PedidoData());
    }
    
    // Reporte Pedidos - Por año
    public static function getPedidoGroupByAnho($sede, $anho = "", $estado = "")
    {
        $sql = "select ifnull(sum(pe.total),0) as total from " . self::$tablename . " pe ";
        $sql .= "join anho a on a.id = \"$anho\" ";
        $sql .= "where pe.sede = \"$sede\" ";
        if ($estado == "") {
            $sql .= "and pe.estado in (1,2) ";
        } else {
            $sql .= "and pe.estado = 0 ";
        }
        if ($anho != "") {
            $sql .= "and year(pe.fecha) = a.nombre ";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new PedidoData());
    }
    
    public static function getDataReporteGroupByFecha($sede = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select date(pe.fecha) as fecha, se.nombre as sede, par1.nombre as tipo, u.username as usuario, pe.id as codigo, dp.categoria as categoria, ";
        $sql .= "dp.producto as producto, dp.cantidad as cantidad, dp.total as total, pe.descuento_programado as descuento_programado, pe.descuento_pedido as descuento_pedido ";        
        $sql .= "from " . self::$tablename . " pe ";
        $sql .= "join detalle_pedidos dp on dp.pedido = pe.id ";
        $sql .= "join sedes se on se.id = pe.sede ";
        $sql .= "join parametros par1 on par1.id = pe.tipo ";
        $sql .= "join usuarios u on u.id = pe.mozo ";
        $sql .= "where pe.estado in (1,2) and dp.estado = 1 and pe.sede = \"$sede\" and u.sede = \"$sede\" ";
        if ($fechaInicio != "") {
            $arrFecha = explode("/", $fechaInicio);
            $fechaInicio = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(pe.fecha) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $arrFecha = explode("/", $fechaFin);
            $fechaFin = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(pe.fecha) <= \"$fechaFin\" ";
        }
        $sql .= "order by fecha desc, sede asc, tipo asc, codigo asc, categoria asc, producto asc, total asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    public static function getDataReporteGroupByMes($sede = "", $anho = "", $mesInicio = "", $mesFin = "")
    {
        $sql = "select date(pe.fecha) as fecha, se.nombre as sede, par1.nombre as tipo, u.username as usuario, pe.id as codigo, dp.categoria as categoria, ";
        $sql .= "dp.producto as producto, dp.cantidad as cantidad, dp.total as total, pe.descuento_programado as descuento_programado, pe.descuento_pedido as descuento_pedido ";
        $sql .= "from " . self::$tablename . " pe ";
        $sql .= "join detalle_pedidos dp on dp.pedido = pe.id ";
        $sql .= "join sedes se on se.id = pe.sede ";
        $sql .= "join parametros par1 on par1.id = pe.tipo ";
        $sql .= "join usuarios u on u.id = pe.mozo ";        
        $sql .= "join anho a on a.id = \"$anho\" ";
        $sql .= "where pe.estado in (1,2) and dp.estado = 1 and pe.sede = \"$sede\" and u.sede = \"$sede\" ";
        if ($anho != "") {
            $sql .= "and year(pe.fecha) = a.nombre ";
        }
        if ($mesInicio != "") {
            $sql .= "and month(pe.fecha) >= \"$mesInicio\" ";
        }
        if ($mesFin != "") {
            $sql .= "and month(pe.fecha) <= \"$mesFin\" ";
        }
        $sql .= "order by fecha desc, sede asc, tipo asc, codigo asc, categoria asc, producto asc, total asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    public static function getDataReporteGroupByAnho($sede = "", $anhoInicio = "", $anhoFin = "")
    {
        $sql = "select date(pe.fecha) as fecha, se.nombre as sede, par1.nombre as tipo, u.username as usuario, pe.id as codigo, dp.categoria as categoria, ";
        $sql .= "dp.producto as producto, dp.cantidad as cantidad, dp.total as total, pe.descuento_programado as descuento_programado, pe.descuento_pedido as descuento_pedido ";        
        $sql .= "from " . self::$tablename . " pe ";
        $sql .= "join detalle_pedidos dp on dp.pedido = pe.id ";
        $sql .= "join sedes se on se.id = pe.sede ";
        $sql .= "join parametros par1 on par1.id = pe.tipo ";
        $sql .= "join usuarios u on u.id = pe.mozo ";        
        $sql .= "where pe.estado in (1,2) and dp.estado = 1 and pe.sede = \"$sede\" and u.sede = \"$sede\" ";
        if ($anhoInicio != "") {
            $sql .= "and year(pe.fecha) >= \"$anhoInicio\" ";
        }
        if ($anhoFin != "") {
            $sql .= "and year(pe.fecha) <= \"$anhoInicio\" ";
        }
        $sql .= "order by fecha desc, sede asc, tipo asc, codigo asc, categoria asc, producto asc, total asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    // Reporte ticket medio - Por rango
    public static function getTicketMedioGroupByFecha($sede, $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select (case when num_comensales is NULL then 0 else round(sum(total/num_comensales),2) end) as total from ( ";
        $sql .= "select sum(pe.total-pe.descuento_programado-pe.descuento_pedido) as total,sum(pe.num_comensales) as num_comensales ";
        $sql .= "from " . self::$tablename . " pe ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado = 2 and pe.mesa > 0 ";
        if ($fechaInicio != "") {
            $sql .= "and date(pe.fecha) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $sql .= "and date(pe.fecha) <= \"$fechaFin\" ";
        }
        $sql .= ") tabla";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PedidoData());
    }
    
    // Reporte ticket medio - Por mes
    public static function getTicketMedioGroupByMes($sede, $anho = "", $mes = "")
    {
        $sql = "select (case when num_comensales is NULL then 0 else round(sum(total/num_comensales),2) end) as total from ( ";
        $sql .= "select sum(pe.total-pe.descuento_programado-pe.descuento_pedido) as total,sum(pe.num_comensales) as num_comensales ";
        $sql .= "from " . self::$tablename . " pe ";
        $sql .= "join anho a on a.id = \"$anho\" ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado = 2 and pe.mesa > 0 ";
        if ($anho != "") {
            $sql .= "and year(pe.fecha) = a.nombre ";
        }
        if ($mes != "") {
            $sql .= "and month(pe.fecha) = \"$mes\" ";
        }
        $sql .= ") tabla";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PedidoData());
    }
    
    // Reporte ticket medio - Por año
    public static function getTicketMedioGroupByAnho($sede, $anho = "")
    {
        $sql = "select (case when num_comensales is NULL then 0 else round(sum(total/num_comensales),2) end) as total from ( ";
        $sql .= "select sum(pe.total-pe.descuento_programado-pe.descuento_pedido) as total,sum(pe.num_comensales) as num_comensales ";
        $sql .= "from " . self::$tablename . " pe ";
        $sql .= "join anho a on a.id = \"$anho\" ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado = 2 and pe.mesa > 0 ";
        if ($anho != "") {
            $sql .= "and year(pe.fecha) = a.nombre ";
        }
        $sql .= ") tabla";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PedidoData());
    }
    
    // Total numero de comensales
    public static function getNumComensalesByFecha($sede, $fecha = "")
    {
        $sql = "select sum(pe.num_comensales) as num_comensales ";
        $sql .= "from " . self::$tablename . " pe ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado = 2 and pe.mesa > 0 ";
        if ($fecha != "") {
            $sql .= "and date(pe.fecha) = \"$fecha\" ";
        }
        $query = Executor::doit($sql);
        return Model::one($query[0], new PedidoData());
    }
    
    // Ticket medio
    public static function getTicketMedioByFecha($sede, $fecha = "")
    {
        $sql = "select (case when num_comensales is NULL then 0 else round(sum(total/num_comensales),2) end) as total from ( ";
        $sql .= "select sum(pe.total-pe.descuento_programado-pe.descuento_pedido) as total,sum(pe.num_comensales) as num_comensales ";
        $sql .= "from " . self::$tablename . " pe ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado = 2 and pe.mesa > 0 ";
        if ($fecha != "") {
            $sql .= "and date(pe.fecha) = \"$fecha\" ";
        }
        $sql .= ") tabla";
        $query = Executor::doit($sql);
        return Model::one($query[0], new PedidoData());
    }
    
    public static function getVentaXTipoByFecha($sede, $fecha = "", $tipo = "0")
    {
        $sql = "select sum(pe.total-pe.descuento_programado-pe.descuento_pedido) as total ";
        $sql .= "from " . self::$tablename . " pe ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado = 2 ";
        if ($fecha != "") {
            $sql .= "and date(pe.fecha) = \"$fecha\" ";
        }
        if ($tipo > 0) {
            $sql .= "and pe.tipo = \"$tipo\" ";
        }
        $query = Executor::doit($sql);
        return Model::one($query[0], new PedidoData());
    }
}