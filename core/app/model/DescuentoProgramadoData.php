<?php

class DescuentoProgramadoData
{

    public static $tablename = "descuentos_programado";

    public function __construct()
    {
        $this->id = "";
        $this->sede = "";
        $this->fecha = "";
        $this->fecha_inicio = "";
        $this->fecha_fin = "";
        $this->producto = "";
        $this->precio_actual = "";
        $this->porcentaje = "";
        $this->precio_descuento = "";
        $this->estado = "";
        $this->fecha_creacion = "";
        $this->usuario_creacion = "";
        $this->fecha_actualizacion = "";
        $this->usuario_actualizacion = "";        
    }

    public function getProducto()
    {
        return ProductoData::getById($this->producto);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (sede,fecha,fecha_inicio,fecha_fin,producto,precio_actual,porcentaje,";
        $sql .= "precio_descuento,estado,fecha_creacion,usuario_creacion,usuario_actualizacion,fecha_actualizacion) value ";
        $sql .= "(\"$this->sede\",\"$this->fecha\",\"$this->fecha_inicio\",\"$this->fecha_fin\",\"$this->producto\",";
        $sql .= "\"$this->precio_actual\",\"$this->porcentaje\",\"$this->precio_descuento\",\"$this->estado\",\"$this->fecha_creacion\",";
        $sql .= "\"$this->usuario_creacion\",\"$this->usuario_actualizacion\",\"$this->fecha_actualizacion\")";
        return Executor::doit($sql);
    }
    
    public function update()
    {
        $sql = "update " . self::$tablename . " set fecha_inicio = \"$this->fecha_inicio\",fecha_fin = \"$this->fecha_fin\",producto = \"$this->producto\",";
        $sql .= "precio_actual = \"$this->precio_actual\",porcentaje = \"$this->porcentaje\",precio_descuento = \"$this->precio_descuento\",";
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
        return Model::one($query[0], new DescuentoProgramadoData());
    }

    public static function getAll($estado = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($fechaInicio != "") {
            $arrFecha = explode("/", $fechaInicio);
            $fechaInicio = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and fecha >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $arrFecha = explode("/", $fechaFin);
            $fechaFin = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and fecha <= \"$fechaFin\" ";
        }
        $sql .= "order by fecha desc";        
        $query = Executor::doit($sql);
        return Model::many($query[0], new DescuentoProgramadoData());
    }
    
    public static function getDescuentoXProducto($sede = "", $producto = "", $fecha = "")
    {
        $sql = "select * from " . self::$tablename . " where estado = 1 ";
        if ($sede != "") {
            $sql .= "and sede = \"$sede\" ";
        }
        if ($producto != "") {
            $sql .= "and producto = \"$producto\" ";
        }
        if ($fecha != "") {
            $sql .= "and fecha_inicio <= \"$fecha\" and \"$fecha\" <= fecha_fin ";
        }        
        $sql .= "order by fecha desc";        
        $query = Executor::doit($sql);
        return Model::one($query[0], new DescuentoProgramadoData());
    }
}