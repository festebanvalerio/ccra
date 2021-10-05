<?php

class ClienteData
{

    public static $tablename = "clientes";

    public function __construct()
    {
        $this->id = "";
        $this->tipo_documento = "";
        $this->num_documento = "";
        $this->datos = "";
        $this->direccion = "";
        $this->estado = "";        
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
        $sql = "insert into " . self::$tablename . " (tipo_documento,num_documento,datos,direccion,estado) value (\"$this->tipo_documento\",\"$this->num_documento\",";
        $sql .= "\"$this->datos\",\"$this->direccion\",\"$this->estado\")";
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
        return Model::one($query[0], new ClienteData());
    }

    public static function getByNumDoc($estado = "", $numDoc = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($numDoc != "") {
            $sql .= "and num_documento = \"$numDoc\" ";
        }
        $sql .= "order by id asc";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ClienteData());
    }
}