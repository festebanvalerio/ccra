<?php

class EmpresaData
{

    public static $tablename = "empresas";

    public function __construct()
    {
        $this->id = "";
        $this->ruc = "";
        $this->nombre_comercial = "";
        $this->razon_social = "";
        $this->direccion = "";
        $this->telefono = "";
        $this->contacto = "";
        $this->facturacion_electronica = "";
        $this->estado = "";
    }

    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (ruc,nombre_comercial,razon_social,direccion,telefono,contacto,facturacion_electronica,estado) ";
        $sql .= "value (\"$this->ruc\",\"$this->nombre_comercial\",\"$this->razon_social\",\"$this->direccion\",\"$this->telefono\",\"$this->contacto\",";
        $sql .= "\"$this->facturacion_electronica\",\"$this->estado\")";
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
        return Model::one($query[0], new EmpresaData());
    }

    public static function getAll($estado = "")
    {
        $sql = "select * from " . self::$tablename . " ";
        if ($estado != "") {
            $sql .= "where estado = \"$estado\" ";
        }
        $sql .= "order by nombre asc";
        $query = Executor::doit($sql);
        return Model::one($query[0], new EmpresaData());
    }
}