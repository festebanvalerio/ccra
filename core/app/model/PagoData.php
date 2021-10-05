<?php

class PagoData
{

    public static $tablename = "pagos";

    public function __construct()
    {
        $this->id = "";
        $this->pedido = "";
        $this->tipo_pago = "";
        $this->porcentaje_descuento = "";
        $this->monto_descuento = "";
        $this->monto_total = "";
        $this->monto_pagado_efectivo = "";
        $this->monto_pagado_tarjeta = "";
        $this->monto_credito = "";
        $this->estado = "";
        $this->usuario_creacion = "";
        $this->fecha_creacion = "";               
    }

    public function getPedido()
    {
        return PedidoData::getById($this->pedido);
    }
    
    public function getTipoPago()
    {
        return ParametroData::getById($this->tipo_pago);
    }
    
    public function getHistorialPago()
    {
        return HistorialPagoData::getAllByPago($this->id);
    }
    
    public function getUsuario()
    {
        return UsuarioData::getById($this->usuario_creacion);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (pedido,tipo_pago,porcentaje_descuento,monto_descuento,monto_total,monto_pagado_efectivo,monto_pagado_tarjeta,";
        $sql .= "monto_credito,estado,usuario_creacion,fecha_creacion) value (\"$this->pedido\",\"$this->tipo_pago\",\"$this->porcentaje_descuento\",";
        $sql .= "\"$this->monto_descuento\",\"$this->monto_total\",\"$this->monto_pagado_efectivo\",\"$this->monto_pagado_tarjeta\",\"$this->monto_credito\",";
        $sql .= "\"$this->estado\",\"$this->usuario_creacion\",\"$this->fecha_creacion\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set monto_total = \"$this->monto_total\",monto_pagado_efectivo = \"$this->monto_pagado_efectivo\",";
        $sql .= "monto_pagado_tarjeta = \"$this->monto_pagado_tarjeta\",monto_credito = \"$this->monto_credito\" where id = \"$this->id\"";
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
        return Model::one($query[0], new PagoData());
    }

    public static function getByPedido($pedido)
    {
        $sql = "select * from " . self::$tablename . " where pedido = \"$pedido\"";        
        $query = Executor::doit($sql);
        return Model::one($query[0], new PagoData());
    }
    
    public static function getAll($estado = "", $sede = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select pa.* from " . self::$tablename . " pa, pedidos pe where pe.id = pa.pedido and pe.estado in (1,2) ";
        if ($estado != "") {
            $sql .= "and pa.estado = \"$estado\" ";
        }
        if ($sede != "") {
            $sql .= "and pe.sede = \"$sede\" ";
        }
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
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    public static function getMontoConsultado($fecha)
    {
        $sql = "select sum(monto_pagado_efectivo) as efectivo, sum(monto_pagado_tarjeta) as tarjeta from " . self::$tablename . " where estado = 1 ";
        $sql .= "and date(fecha_creacion) = \"$fecha\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new PagoData());
    }
    
    // Reporte Ventas - Por rango
    public static function getVentaGroupByFecha($sede, $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select ifnull(sum(pa.monto_pagado_efectivo + pa.monto_pagado_tarjeta + pa.monto_credito),0) as total from " . self::$tablename . " pa ";
        $sql .= "join pedidos pe on pe.id = pa.pedido and pe.sede = \"$sede\" ";
        $sql .= "where pe.estado in (2,3) and pa.estado = 1 ";
        if ($fechaInicio != "") {
            $sql .= "and date(pa.fecha_creacion) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $sql .= "and date(pa.fecha_creacion) <= \"$fechaFin\" ";
        }        
        $query = Executor::doit($sql);        
        return Model::many($query[0], new PagoData());
    }
    
    // Reporte Ventas - Por mes
    public static function getVentaGroupByMes($sede, $anho = "", $mes = "")
    {
        $sql = "select ifnull(sum(pa.monto_pagado_efectivo + pa.monto_pagado_tarjeta + pa.monto_credito),0) as total from " . self::$tablename . " pa ";
        $sql .= "join pedidos pe on pe.id = pa.pedido and pe.sede = \"$sede\" ";
        $sql .= "join anho a on a.id = \"$anho\" ";
        $sql .= "where pe.estado in (2,3) and pa.estado = 1 ";
        if ($anho != "") {
            $sql .= "and year(pa.fecha_creacion) = a.nombre ";
        }
        if ($mes != "") {
            $sql .= "and month(pa.fecha_creacion) = \"$mes\" ";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }

    // Reporte Ventas - Por año
    public static function getVentaGroupByAnho($sede, $anho = "")
    {
        $sql = "select ifnull(sum(pa.monto_pagado_efectivo + pa.monto_pagado_tarjeta + pa.monto_credito),0) as total from " . self::$tablename . " pa ";
        $sql .= "join pedidos pe on pe.id = pa.pedido and pe.sede = \"$sede\" ";
        $sql .= "join anho a on a.id = \"$anho\" ";
        $sql .= "where pe.estado in (2,3) and pa.estado = 1 ";            
        if ($anho != "") {
            $sql .= "and year(pa.fecha_creacion) = a.nombre ";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
        
    // Reporte Forma Pago - Por rango
    public static function getFormaPagoGroupByFecha($sede, $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select par.nombre as forma_pago,(select round(sum(dhp.total * (1 - pa.porcentaje_descuento)),1) as total from " . self::$tablename . " pa ";
        $sql .= "join historial_pagos hp on hp.pago = pa.id ";
        $sql .= "join detalle_historial_pagos dhp on dhp.historial_pago = hp.id ";
        $sql .= "join pedidos pe on pe.id = pa.pedido and pe.sede = \"$sede\" ";        
        $sql .= "where pa.estado = 1 and hp.estado = 1 and dhp.estado = 1 ";
        if ($fechaInicio != "") {
            $sql .= "and date(pa.fecha_creacion) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $sql .= "and date(pa.fecha_creacion) <= \"$fechaFin\" ";
        }
        $sql .= "and hp.forma_pago = par.id) as total ";
        $sql .= "from parametros par where par.tabla = 3 order by par.valor1 asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }

    // Reporte Forma Pago - Por mes
    public static function getFormaPagoGroupByMes($sede, $anho = "", $mes = "")
    {
        $sql = "select par.nombre as forma_pago,(select round(sum(pa.monto_total * (1-pa.porcentaje_descuento)),1) as total from " . self::$tablename . " pa ";
        $sql .= "join historial_pagos hp on hp.pago = pa.id ";
        $sql .= "join pedidos pe on pe.id = pa.pedido and pe.sede = \"$sede\" ";
        $sql .= "join anho a on a.nombre = \"$anho\" ";
        $sql .= "where pa.estado = 1 and hp.estado = 1 ";
        if ($anho != "") {
            $sql .= "and year(pa.fecha_creacion) = a.nombre ";
        }
        if ($mes != "") {
            $sql .= "and month(pa.fecha_creacion) = \"$mes\" ";
        }
        $sql .= "and hp.forma_pago = par.id) as total ";
        $sql .= "from parametros par where par.tabla = 3 order by par.valor1 asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    // Reporte Forma Pago - Por año
    public static function getFormaPagoGroupByAnho($sede, $anho = "")
    {
        $sql = "select par.nombre as forma_pago,(select round(sum(pa.monto_total * (1-pa.porcentaje_descuento)),1) as total from " . self::$tablename . " pa ";        
        $sql .= "join historial_pagos hp on hp.pago = pa.id ";
        $sql .= "join pedidos pe on pe.id = pa.pedido and pe.sede = \"$sede\" ";
        $sql .= "where pa.estado = 1 and hp.estado = 1 ";
        if ($anho != "") {
            $sql .= "and year(pa.fecha_creacion) =  \"$anho\" ";
        }
        $sql .= "and hp.forma_pago = par.id) as total ";
        $sql .= "from parametros par where par.tabla = 3 order by par.valor1 asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    // Reporte Personal - Por rango
    public static function getUsuarioGroupByFecha($sede, $perfil, $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select u.username as usuario,ifnull((select round(sum(pe.total - pe.descuento_pedido),1) from " . self::$tablename . " pa, ";        
        $sql .= "pedidos pe where pe.id = pa.pedido and pe.sede = \"$sede\" and pe.mozo = u.id and pa.estado = 1 and pe.estado in (1,2) ";
        if ($fechaInicio != "") {
            $sql .= "and date(pa.fecha_creacion) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $sql .= "and date(pa.fecha_creacion) <= \"$fechaFin\"";
        }
        $sql .= "),0) as total from usuarios u ";
        $sql .= "where u.estado = 1 and u.sede = \"$sede\" and u.perfil = \"$perfil\" ";
        $sql .= "group by usuario";        
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    // Reporte Personal - Por mes
    public static function getUsuarioGroupByMes($sede, $perfil, $anho = "", $mes = "")
    {
        $sql = "select u.username as usuario,ifnull((select round(sum(pe.total - pe.descuento_pedido),1) from " . self::$tablename . " pa, ";
        $sql .= "pedidos pe, anho a where pe.id = pa.pedido and pe.sede = \"$sede\" and pe.mozo = u.id and pa.estado = 1 and pe.estado in (1,2) and a.nombre = \"$anho\" ";
        if ($anho != "") {
            $sql .= "and year(pa.fecha_creacion) = a.nombre ";
        }
        if ($mes != "") {
            $sql .= "and month(pa.fecha_creacion) = \"$mes\"";
        }
        $sql .= "),0) as total from usuarios u ";
        $sql .= "where u.estado = 1 and u.sede = \"$sede\" and u.perfil = \"$perfil\" ";
        $sql .= "group by usuario";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    // Reporte Personal - Por año
    public static function getUsuarioGroupByAnho($sede, $perfil, $anho = "")
    {
        $sql = "select u.username as usuario,ifnull((select round(sum(pe.total - pe.descuento_pedido),1) from " . self::$tablename . " pa, ";
        $sql .= "pedidos pe, anho a where pe.id = pa.pedido and pe.sede = \"$sede\" and pe.mozo = u.id and pa.estado = 1 and pe.estado in (1,2) ";
        if ($anho != "") {
            $sql .= "and year(pa.fecha_creacion) = \"$anho\" ";
        }
        $sql .= "),0) as total from usuarios u ";
        $sql .= "where u.estado = 1 and u.sede = \"$sede\" and u.perfil = \"$perfil\" ";
        $sql .= "group by usuario";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    public static function getDataReporteGroupByFecha($sede = "", $fechaInicio = "", $fechaFin = "", $perfil = "")
    {
        $sql = "select date(pa.fecha_creacion) as fecha, se.nombre as sede, par1.nombre as tipo, u.username as usuario, par2.nombre as forma_pago, par3.nombre as tipo_tarjeta, ";
        $sql .= "pe.id as codigo, dhp.nom_categoria as categoria, dhp.nom_producto as producto, dhp.cantidad as cantidad, dhp.total as total, pa.porcentaje_descuento as descuento ";
        $sql .= "from " . self::$tablename . " pa ";
        $sql .= "join historial_pagos hp on hp.pago = pa.id ";
        $sql .= "join detalle_historial_pagos dhp on dhp.historial_pago = hp.id ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "join sedes se on se.id = pe.sede ";
        $sql .= "join parametros par1 on par1.id = pe.tipo ";
        $sql .= "join parametros par2 on par2.id = hp.forma_pago ";
        $sql .= "left join parametros par3 on par3.id = hp.tipo_tarjeta ";
        $sql .= "join usuarios u on u.id = pe.mozo ";
        $sql .= "where pa.estado = 1 and hp.estado = 1 and dhp.estado = 1 and pe.sede = \"$sede\" and u.sede = \"$sede\" ";
        if ($fechaInicio != "") {
            $arrFecha = explode("/", $fechaInicio);
            $fechaInicio = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(pa.fecha_creacion) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $arrFecha = explode("/", $fechaFin);
            $fechaFin = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(pa.fecha_creacion) <= \"$fechaFin\" ";
        }
        if ($perfil != "") {
            $sql .= "and u.perfil = \"$perfil\" ";
        }
        $sql .= "order by fecha desc, sede asc, tipo asc, codigo asc, forma_pago asc, tipo_tarjeta asc, categoria asc, producto asc, total asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    public static function getDataReporteGroupByMes($sede = "", $anho = "", $mesInicio = "", $mesFin = "", $perfil = "")
    {
        $sql = "select date(pa.fecha_creacion) as fecha, se.nombre as sede, par1.nombre as tipo, u.username as usuario, par2.nombre as forma_pago, par3.nombre as tipo_tarjeta, ";
        $sql .= "pe.id as codigo, dhp.nom_categoria as categoria, dhp.nom_producto as producto, dhp.cantidad as cantidad, dhp.total as total, pa.porcentaje_descuento as descuento ";
        $sql .= "from " . self::$tablename . " pa ";
        $sql .= "join historial_pagos hp on hp.pago = pa.id ";
        $sql .= "join detalle_historial_pagos dhp on dhp.historial_pago = hp.id ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "join sedes se on se.id = pe.sede ";
        $sql .= "join parametros par1 on par1.id = pe.tipo ";
        $sql .= "join parametros par2 on par2.id = hp.forma_pago ";
        $sql .= "left join parametros par3 on par3.id = hp.tipo_tarjeta ";
        $sql .= "join usuarios u on u.id = pe.mozo ";
        $sql .= "where pa.estado = 1 and hp.estado = 1 and dhp.estado = 1 and pe.sede = \"$sede\" and u.sede = \"$sede\" ";
        if ($anho != "") {
            $sql .= "and year(pa.fecha_creacion) = \"$anho\" ";
        }
        if ($mesInicio != "") {
            $sql .= "and month(pa.fecha_creacion) >= \"$mesInicio\" ";
        }
        if ($mesFin != "") {
            $sql .= "and month(pa.fecha_creacion) <= \"$mesFin\" ";
        }
        if ($perfil != "") {
            $sql .= "and u.perfil = \"$perfil\" ";
        }
        $sql .= "order by fecha desc, sede asc, tipo asc, codigo asc, forma_pago asc, tipo_tarjeta asc, categoria asc, producto asc, total asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    public static function getDataReporteGroupByAnho($sede = "", $anhoInicio = "", $anhoFin = "", $perfil = "")
    {
        $sql = "select date(pa.fecha_creacion) as fecha, se.nombre as sede, par1.nombre as tipo, u.username as usuario, par2.nombre as forma_pago, par3.nombre as tipo_tarjeta, ";
        $sql .= "pe.id as codigo, dhp.nom_categoria as categoria, dhp.nom_producto as producto, dhp.cantidad as cantidad, dhp.total as total, pa.porcentaje_descuento as descuento ";
        $sql .= "from " . self::$tablename . " pa ";
        $sql .= "join historial_pagos hp on hp.pago = pa.id ";
        $sql .= "join detalle_historial_pagos dhp on dhp.historial_pago = hp.id ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "join sedes se on se.id = pe.sede ";
        $sql .= "join parametros par1 on par1.id = pe.tipo ";
        $sql .= "join parametros par2 on par2.id = hp.forma_pago ";
        $sql .= "left join parametros par3 on par3.id = hp.tipo_tarjeta ";
        $sql .= "join usuarios u on u.id = pe.mozo ";
        $sql .= "where pa.estado = 1 and hp.estado = 1 and dhp.estado = 1 and pe.sede = \"$sede\" and u.sede = \"$sede\" ";
        if ($anhoInicio != "") {
            $sql .= "and year(pa.fecha_creacion) >= \"$anhoInicio\" ";
        }
        if ($anhoFin != "") {
            $sql .= "and year(pa.fecha_creacion) <= \"$anhoFin\" ";
        }
        if ($perfil != "") {
            $sql .= "and u.perfil = \"$perfil\" ";
        }
        $sql .= "order by fecha desc, sede asc, tipo asc, codigo asc, forma_pago asc, tipo_tarjeta asc, categoria asc, producto asc, total asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    public static function getDataReporteTipoDescuentoGroupByFecha($sede = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select date(pa.fecha_creacion) as fecha, se.nombre as sede, par1.nombre as tipo, pe.id as codigo, pa.monto_pagado_efectivo total_efectivo, ";
        $sql .= "pa.monto_pagado_tarjeta as total_tarjeta, pe.total as total, pe.descuento_programado as dscto_programado, pe.descuento_pedido as dscto_pedido, ";
        $sql .= "u1.username as usuario_pedido, u2.username as usuario_caja, par2.nombre as tipo_pago ";
        $sql .= "from " . self::$tablename . " pa ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "join sedes se on se.id = pe.sede ";
        $sql .= "join parametros par1 on par1.id = pe.tipo "; 
        $sql .= "join parametros par2 ON par2.id = pa.tipo_pago ";     
        $sql .= "join usuarios u1 on u1.id = pe.mozo ";
        $sql .= "join usuarios u2 on u2.id = pa.usuario_creacion ";
        $sql .= "where pe.estado in (1,2) and pa.estado = 1 and pe.sede = \"$sede\" ";
        if ($fechaInicio != "") {
            $arrFecha = explode("/", $fechaInicio);
            $fechaInicio = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(pa.fecha_creacion) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $arrFecha = explode("/", $fechaFin);
            $fechaFin = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(pa.fecha_creacion) <= \"$fechaFin\" ";
        }        
        $sql .= "order by fecha desc, sede asc, tipo asc, codigo asc";        
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    public static function getDataReporteTipoDescuentoGroupByMes($sede = "", $anho = "", $mesInicio = "", $mesFin = "")
    {
        $sql = "select date(pa.fecha_creacion) as fecha, se.nombre as sede, par1.nombre as tipo, pe.id as codigo, pa.monto_pagado_efectivo total_efectivo, ";
        $sql .= "pa.monto_pagado_tarjeta as total_tarjeta, pe.total as total, pe.descuento_programado as dscto_programado, pe.descuento_pedido as dscto_pedido, ";
        $sql .= "u1.username as usuario_pedido, u2.username as usuario_caja, par2.nombre as tipo_pago ";
        $sql .= "from " . self::$tablename . " pa ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "join sedes se on se.id = pe.sede ";
        $sql .= "join parametros par1 on par1.id = pe.tipo ";
        $sql .= "join parametros par2 ON par2.id = pa.tipo_pago ";
        $sql .= "join usuarios u1 on u1.id = pe.mozo ";
        $sql .= "join usuarios u2 on u2.id = pa.usuario_creacion ";
        $sql .= "join anho a on a.id = \"$anho\" ";
        $sql .= "where pe.estado in (1,2) and pa.estado = 1 and pe.sede = \"$sede\" ";
        if ($anho != "") {
            $sql .= "and year(pa.fecha_creacion) = a.nombre ";
        }
        if ($mesInicio != "") {
            $sql .= "and month(pa.fecha_creacion) >= \"$mesInicio\" ";
        }
        if ($mesFin != "") {
            $sql .= "and month(pa.fecha_creacion) <= \"$mesFin\" ";
        }
        $sql .= "order by fecha desc, sede asc, tipo asc, codigo asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    public static function getDataReporteTipoDescuentoGroupByAnho($sede = "", $anhoInicio = "", $anhoFin = "")
    {
        $sql = "select date(pa.fecha_creacion) as fecha, se.nombre as sede, par1.nombre as tipo, pe.id as codigo, pa.monto_pagado_efectivo total_efectivo, ";
        $sql .= "pa.monto_pagado_tarjeta as total_tarjeta, pe.total as total, pe.descuento_programado as dscto_programado, pe.descuento_pedido as dscto_pedido, ";
        $sql .= "u1.username as usuario_pedido, u2.username as usuario_caja, par2.nombre as tipo_pago ";
        $sql .= "from " . self::$tablename . " pa ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "join sedes se on se.id = pe.sede ";
        $sql .= "join parametros par1 on par1.id = pe.tipo ";
        $sql .= "join parametros par2 ON par2.id = pa.tipo_pago ";
        $sql .= "join usuarios u on u.id = pe.mozo ";
        $sql .= "where pe.estado in (1,2) and pa.estado = 1 and pe.sede = \"$sede\" ";
        if ($anhoInicio != "") {
            $sql .= "and year(pa.fecha_creacion) >= \"$anhoInicio\" ";
        }
        if ($anhoFin != "") {
            $sql .= "and year(pa.fecha_creacion) <= \"$anhoInicio\" ";
        }
        $sql .= "order by fecha desc, sede asc, tipo asc, codigo asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
    
    public static function getPlatoMasVendidoGeneral($sede = "", $fecha = "")
    {
        $sql = "select dhp.nom_producto as producto,sum(dhp.cantidad) as total ";
        $sql .= "from " . self::$tablename . " pa ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "join historial_pagos hp on hp.pago = pa.id ";
        $sql .= "join detalle_historial_pagos dhp on dhp.historial_pago = hp.id ";
        $sql .= "where pa.estado = 1 and date(pe.fecha) <= \"$fecha\" and pe.estado in (2,3) and hp.estado = 1 and pe.sede = \"$sede\" "; 
        $sql .= "group by dhp.nom_producto order by total desc limit 1";        
        $query = Executor::doit($sql);
        return Model::one($query[0], new PagoData());        
    }
    
    public static function getPlatoMasVendido($sede = "", $fecha = "")
    {
        $sql = "select dhp.nom_producto as producto,sum(dhp.cantidad) as total ";
        $sql .= "from " . self::$tablename . " pa ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "join historial_pagos hp on hp.pago = pa.id ";
        $sql .= "join detalle_historial_pagos dhp on dhp.historial_pago = hp.id ";
        $sql .= "where pa.estado = 1 and date(pe.fecha) = \"$fecha\" and pe.estado in (2,3) and hp.estado = 1 and pe.sede = \"$sede\" ";
        $sql .= "group by dhp.nom_producto order by total desc limit 1";
        $query = Executor::doit($sql);
        return Model::one($query[0], new PagoData());
    }
    
    public static function getTotalPagadoXFecha($sede = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select date(pe.fecha) as fecha,sum(pa.monto_pagado_efectivo+pa.monto_pagado_tarjeta) as total ";
        $sql .= "from " . self::$tablename . " pa ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado > 0 and pa.estado = 1 ";
        if ($fechaInicio != "") {
            $sql .= "and date(pe.fecha) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $sql .= "and date(pe.fecha) <= \"$fechaFin\" ";
        }
        $sql .= "group by date(pe.fecha)";
        $query = Executor::doit($sql);
        return Model::one($query[0], new PagoData());
    }
    
    public static function getNumTotalPagadoXFecha($sede = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select date(pe.fecha) as fecha,count(1) as total ";
        $sql .= "from " . self::$tablename . " pa ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado > 0 and pa.estado = 1 ";
        if ($fechaInicio != "") {
            $sql .= "and date(pe.fecha) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $sql .= "and date(pe.fecha) <= \"$fechaFin\" ";
        }
        $sql .= "group by date(pe.fecha)";
        $query = Executor::doit($sql);
        return Model::one($query[0], new PagoData());
    }
    
    public static function getPagosSinComprobante($sede = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select date(pe.fecha) as fecha,pa.id as pago,sum(pa.monto_pagado_efectivo+pa.monto_pagado_tarjeta) as total ";
        $sql .= "from " . self::$tablename . " pa ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "join historial_pagos hp on hp.pago = pa.id ";
        $sql .= "where pe.sede = \"$sede\" and pe.estado > 0 and pa.estado = 1 and hp.comprobante = 0 ";
        if ($fechaInicio != "") {
            $sql .= "and date(pe.fecha) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $sql .= "and date(pe.fecha) <= \"$fechaFin\" ";
        }
        $sql .= "group by date(pe.fecha),pa.id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PagoData());
    }
}