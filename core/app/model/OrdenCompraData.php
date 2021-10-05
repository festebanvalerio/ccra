<?php

class OrdenCompraData
{

    public static $tablename = "ocs";

    public function __construct()
    {
        $this->id = "";
        $this->fecha = "";
        $this->sede = "";
        $this->almacen = "";
        $this->tipo_documento = "";
        $this->num_documento = "";
        $this->ruc = "";
        $this->razon_social = "";
        $this->monto = "";
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
    
    public function getAlmacen()
    {
        return AlmacenData::getById($this->almacen);
    }
    
    public function getTipoDocumento()
    {
        return ParametroData::getById($this->tipo_documento);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (fecha,sede,almacen,tipo_documento,num_documento,ruc,razon_social,monto,estado,fecha_creacion,usuario_creacion,";
        $sql .= "usuario_actualizacion,fecha_actualizacion) value (\"$this->fecha\",\"$this->sede\",\"$this->almacen\",\"$this->tipo_documento\",\"$this->num_documento\",";
        $sql .= "\"$this->ruc\",\"$this->razon_social\",\"$this->monto\",\"$this->estado\",\"$this->fecha_creacion\",\"$this->usuario_creacion\",";
        $sql .= "\"$this->usuario_actualizacion\",\"$this->fecha_actualizacion\")";        
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
        return Model::one($query[0], new OrdenCompraData());
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
        return Model::many($query[0], new OrdenCompraData());
    }
}