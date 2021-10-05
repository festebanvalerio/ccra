<?php

class AjusteData
{

    public static $tablename = "ajustes";

    public function __construct()
    {
        $this->id = "";
        $this->fecha = "";
        $this->almacen = "";
        $this->observacion = "";
        $this->estado = "";
        $this->fecha_creacion = "";
        $this->usuario_creacion = "";
        $this->fecha_actualizacion = "";
        $this->usuario_actualizacion = "";        
    }

    public function getAlmacen()
    {
        return AlmacenData::getById($this->almacen);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (fecha,almacen,observacion,estado,fecha_creacion,usuario_creacion,";
        $sql .= "usuario_actualizacion,fecha_actualizacion) value (\"$this->fecha\",\"$this->almacen\",\"$this->observacion\",";
        $sql .= "\"$this->estado\",\"$this->fecha_creacion\",\"$this->usuario_creacion\",\"$this->usuario_actualizacion\",";
        $sql .= "\"$this->fecha_actualizacion\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set observacion = \"$this->observacion\",fecha_actualizacion = \"$this->fecha_actualizacion\",";
        $sql .= "usuario_actualizacion = \"$this->usuario_actualizacion\" where id = \"$this->id\"";
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
        return Model::one($query[0], new AjusteData());
    }

    public static function getAll($estado = "", $almacen = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($almacen != "") {
            $sql .= "and almacen = \"$almacen\" ";
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
        $sql .= "order by fecha desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new AjusteData());
    }
}