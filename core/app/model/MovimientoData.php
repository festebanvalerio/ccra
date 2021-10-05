<?php

class MovimientoData
{

    public static $tablename = "movimientos";

    public function __construct()
    {
        $this->id = "";
        $this->sede = "";
        $this->insumo = "";
        $this->tipo = "";
        $this->cantidad = "";
        $this->detalle = "";
        $this->modulo = "";
        $this->fecha = "";
        $this->estado = "";
    }

    public function getSede()
    {
        return SedeData::getById($this->sede);
    }
    
    public function getInsumo()
    {
        return InsumoData::getById($this->insumo);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (sede,insumo,tipo,cantidad,detalle,modulo,fecha,estado) ";
        $sql .= "value (\"$this->sede\",\"$this->insumo\",\"$this->tipo\",\"$this->cantidad\",\"$this->detalle\",";
        $sql .= "\"$this->modulo\",\"$this->fecha\",\"$this->estado\")";
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
        return Model::one($query[0], new MovimientoData());
    }

    public static function getAll($estado = "", $sede = "", $insumo = "", $tipo = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($sede != "") {
            $sql .= "and sede = \"$sede\" ";
        }
        if ($insumo != "") {
            $sql .= "and insumo = \"$insumo\" ";
        }
        if ($tipo != "") {
            $sql .= "and tipo = \"$tipo\" ";
        }
        if ($fechaInicio != "") {
            $arrFecha = explode("/", $fechaInicio);
            $fecha = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(fecha) >= \"$fecha\" ";
        }
        if ($fechaFin != "") {
            $arrFecha = explode("/", $fechaFin);
            $fecha = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(fecha) <= \"$fecha\" ";
        }
        $sql .= "order by fecha desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MovimientoData());
    }
    
    public static function getTotal($estado = "", $sede = "", $insumo = "", $tipo = "", $fecha = "", $indicador = 0, $sinAjuste = "")
    {        
        $sql = "select IFNULL(NULL, SUM(cantidad)) as total from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($sede != "") {
            $sql .= "and sede = \"$sede\" ";
        }
        if ($insumo != "") {
            $sql .= "and insumo = \"$insumo\" ";
        }
        if ($tipo != "") {
            if ($tipo == -1) {
                $tipo = 0;
                $sql .= "and tipo = \"$tipo\" ";
            } else {
                $sql .= "and tipo = \"$tipo\" ";
            }
        }
        if ($fecha != "") {
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            if ($indicador == 0) {
                $sql .= "and date(fecha) <= \"$fecha\" ";
            } else {
                $sql .= "and date(fecha) = \"$fecha\" ";
            }
        }
        if ($sinAjuste != "") {
            if ($sinAjuste == 1) {
                $sql .= "and modulo <> 2 ";
            }
        }
        $query = Executor::doit($sql);
        return Model::one($query[0], new MovimientoData());
    }
}