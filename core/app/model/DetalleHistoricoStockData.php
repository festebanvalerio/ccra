<?php

class DetalleHistoricoStockData
{

    public static $tablename = "detalle_historicos_stock";

    public function __construct()
    {
        $this->id = "";
        $this->historico = "";
        $this->insumo = "";
        $this->nom_insumo = "";
        $this->unidad_medida = "";
        $this->costo = "";
        $this->stock = "";
    }

    public function getTotal($estado, $sede, $insumo, $tipo, $fecha, $sinAjuste)
    {
        return MovimientoData::getTotal($estado, $sede, $insumo, $tipo, $fecha, 1, $sinAjuste);
    }

    public static function add($idHistoricoStock)
    {
        $sql = "insert into " . self::$tablename . " select NULL as id,".$idHistoricoStock." as historico_stock,i.id as insumo,i.nombre as nom_insumo,u.nombre AS unidad_medida,i.costo as costo,";
        $sql .= "ia.stock ";
        $sql .= "from insumos i ";
        $sql .= "join unidades u on u.id = i.unidad ";
        $sql .= "join insumos_almacen ia on ia.insumo = i.id ";
        $sql .= "where i.estado = 1 and ia.estado = 1 ";
        $sql .= "order by ia.stock desc";
        return Executor::doit($sql);
    }

    public static function getAll($sede = "", $almacen = "", $fecha = "", $lstIdInsumo = "", $lstIdClasificacion = "")
    {
        $sql = "select dhs.*,c.nombre as clasificacion from " . self::$tablename . " dhs ";
        $sql .= "join historicos_stock hs on hs.id = dhs.historico_stock ";
        $sql .= "join insumos i on i.id = dhs.insumo ";
        $sql .= "left join clasificaciones c on c.id = i.clasificacion ";
        $sql .= "where 1=1 ";
        if ($sede != "") {
            $sql .= "and hs.sede = \"$sede\" ";
        }
        if ($almacen != "") {
            $sql .= "and hs.almacen = \"$almacen\" ";
        }
        if ($fecha != "") {
            $fechaActual = str_replace("/", "-", $fecha);
            $fechaStock = date("Y-m-d", strtotime($fechaActual."- 1 days"));            
            $sql .= "and hs.fecha_stock = \"$fechaStock\" ";
        }
        if ($lstIdInsumo != "") {
            $sql .= "and dhs.insumo in ($lstIdInsumo) ";
        }
        if ($lstIdClasificacion != "") {
            $sql .= "and i.clasificacion in ($lstIdClasificacion) ";
        }
        $sql .= "order by dhs.id desc";
        $query = Executor::doit($sql);        
        return Model::many($query[0], new DetalleHistoricoStockData());
    }
    
    public static function getDetalle($sede = "", $almacen = "", $fechaInicio = "", $fechaFin = "", $lstIdInsumo = "", $lstIdClasificacion = "")
    {
        $sql = "select distinct dhs.insumo as insumo,dhs.nom_insumo as nom_insumo,dhs.unidad_medida as unidad_medida,c.nombre as clasificacion ";
        $sql .= "from " . self::$tablename . " dhs ";
        $sql .= "join historicos_stock hs on hs.id = dhs.historico_stock ";
        $sql .= "join insumos i on i.id = dhs.insumo ";
        $sql .= "left join clasificaciones c on c.id = i.clasificacion ";
        $sql .= "where 1=1 ";
        if ($sede != "") {
            $sql .= "and hs.sede = \"$sede\" ";
        }
        if ($almacen != "") {
            $sql .= "and hs.almacen = \"$almacen\" ";
        }
        if ($fechaInicio != "") {
            $fechaActual = str_replace("/", "-", $fechaInicio);
            $fechaStock = date("Y-m-d", strtotime($fechaActual."- 1 days"));
            $sql .= "and hs.fecha_stock >= \"$fechaStock\" ";
        }
        if ($fechaFin != "") {
            $fechaActual = str_replace("/", "-", $fechaFin);
            $fechaStock = date("Y-m-d", strtotime($fechaActual."- 1 days"));
            $sql .= "and hs.fecha_stock <= \"$fechaStock\" ";
        }
        if ($lstIdInsumo != "") {
            $sql .= "and dhs.insumo in ($lstIdInsumo) ";
        }
        if ($lstIdClasificacion != "") {
            $sql .= "and i.clasificacion in ($lstIdClasificacion) ";
        }
        $sql .= "order by dhs.id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleHistoricoStockData());
    }
    
    public static function getDetalleXInsumo($sede = "", $almacen = "", $fechaInicio = "", $fechaFin = "", $lstIdInsumo = "", $lstIdClasificacion = "")
    {
        $sql = "select dhs.*,c.nombre as clasificacion ";
        $sql .= "from " . self::$tablename . " dhs ";
        $sql .= "join historicos_stock hs on hs.id = dhs.historico_stock ";
        $sql .= "join insumos i on i.id = dhs.insumo ";
        $sql .= "left join clasificaciones c on c.id = i.clasificacion ";
        $sql .= "where 1=1 ";
        if ($sede != "") {
            $sql .= "and hs.sede = \"$sede\" ";
        }
        if ($almacen != "") {
            $sql .= "and hs.almacen = \"$almacen\" ";
        }
        if ($fechaInicio != "") {
            $fechaActual = str_replace("/", "-", $fechaInicio);
            $fechaStock = date("Y-m-d", strtotime($fechaActual."- 1 days"));
            $sql .= "and hs.fecha_stock >= \"$fechaStock\" ";
        }
        if ($fechaFin != "") {
            $fechaActual = str_replace("/", "-", $fechaFin);
            $fechaStock = date("Y-m-d", strtotime($fechaActual."- 1 days"));
            $sql .= "and hs.fecha_stock <= \"$fechaStock\" ";
        }
        if ($lstIdInsumo != "") {
            $sql .= "and dhs.insumo in ($lstIdInsumo) ";
        }
        if ($lstIdClasificacion != "") {
            $sql .= "and i.clasificacion in ($lstIdClasificacion) ";
        }
        $sql .= "order by dhs.id desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleHistoricoStockData());
    }
}