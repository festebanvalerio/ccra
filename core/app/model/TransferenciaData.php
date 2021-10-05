<?php

class TransferenciaData
{

    public static $tablename = "transferencias";

    public function __construct()
    {
        $this->id = "";
        $this->fecha = "";
        $this->sede_origen  = "";
        $this->almacen_origen = "";
        $this->sede_destino  = "";
        $this->almacen_destino = "";
        $this->observacion = "";
        $this->estado = "";
        $this->fecha_creacion = "";
        $this->usuario_creacion = "";
        $this->fecha_actualizacion = "";
        $this->usuario_actualizacion = "";        
    }

    public function getSedeOrigen()
    {
        return SedeData::getById($this->sede_origen);
    }
    
    public function getAlmacenOrigen()
    {
        return AlmacenData::getById($this->almacen_origen);
    }
    
    public function getSedeDestino()
    {
        return SedeData::getById($this->sede_destino);
    }
    
    public function getAlmacenDestino()
    {
        return AlmacenData::getById($this->almacen_destino);
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
        $sql = "insert into " . self::$tablename . " (fecha,sede_origen,almacen_origen,sede_destino,almacen_destino,observacion,estado,fecha_creacion,usuario_creacion,";
        $sql .= "usuario_actualizacion,fecha_actualizacion) value (\"$this->fecha\",\"$this->sede_origen\",\"$this->almacen_origen\",\"$this->sede_destino\",";
        $sql .= "\"$this->almacen_destino\",\"$this->observacion\",\"$this->estado\",\"$this->fecha_creacion\",\"$this->usuario_creacion\",\"$this->usuario_actualizacion\",";
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
        return Model::one($query[0], new TransferenciaData());
    }

    public static function getAll($estado = "", $almacenOrigen = "", $almacenDestino = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($almacenOrigen != "") {
            $sql .= "and almacen_origen = \"$almacenOrigen\" ";
        }
        if ($almacenDestino != "") {
            $sql .= "and almacen_destino = \"$almacenDestino\" ";
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
        return Model::many($query[0], new TransferenciaData());
    }
}