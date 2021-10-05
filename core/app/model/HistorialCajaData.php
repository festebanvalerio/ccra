<?php

class HistorialCajaData
{

    public static $tablename = "historial_cajas";

    public function __construct()
    {
        $this->id = "";
        $this->sede = "";
        $this->piso = "";
        $this->caja = "";
        $this->fecha_apertura = "";
        $this->monto_apertura = "";
        $this->fecha_cierre = "";
        $this->monto_cierre = "";
        $this->estado = "";
        $this->fecha_creacion = "";
        $this->usuario_creacion = "";
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
    
    public function getCaja()
    {
        return CajaData::getById($this->caja);
    }
    
    public function getEstado()
    {
        return EstadoCajaData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (sede,piso,caja,fecha_apertura,monto_apertura,estado,fecha_creacion,usuario_creacion,";
        $sql .= "usuario_actualizacion,fecha_actualizacion) value (\"$this->sede\",\"$this->piso\",\"$this->caja\",\"$this->fecha_apertura\",\"$this->monto_apertura\",";
        $sql .= "\"$this->estado\",\"$this->fecha_creacion\",\"$this->usuario_creacion\",\"$this->usuario_actualizacion\",\"$this->fecha_actualizacion\")";        
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set fecha_cierre = \"$this->fecha_cierre\",monto_cierre = \"$this->monto_cierre\",estado = \"$this->estado\",";
        $sql .= "fecha_actualizacion = \"$this->fecha_actualizacion\",usuario_actualizacion = \"$this->usuario_actualizacion\" where id = \"$this->id\"";        
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
        return Model::one($query[0], new HistorialCajaData());
    }
    
    public static function getAll($estado = "", $sede = "", $caja = "", $fecha = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($sede != "") {
            $sql .= "and sede = \"$sede\" ";
        }
        if ($caja != "") {
            $sql .= "and caja = \"$caja\" ";
        }
        if ($fecha != "") {
            $arrFecha = explode("/", $fecha);
            $fecha = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(fecha_creacion) = \"$fecha\" ";
        }        
        $sql .= "order by fecha_creacion desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new HistorialCajaData());
    }
    
    public static function getAllHistorial($estado = "", $sede = "", $caja = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($sede != "") {
            $sql .= "and sede = \"$sede\" ";
        }
        if ($caja != "") {
            $sql .= "and caja = \"$caja\" ";
        }
        if ($fechaInicio != "") {
            $arrFecha = explode("/", $fechaInicio);
            $fecha = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(fecha_creacion) >= \"$fecha\" ";
        }
        if ($fechaFin != "") {
            $arrFecha = explode("/", $fechaFin);
            $fecha = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(fecha_creacion) <= \"$fecha\" ";
        }
        $sql .= "order by fecha_creacion desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new HistorialCajaData());
    }
}