<?php

class ParametroData
{

    public static $tablename = "parametros";
    public static $tablename1 = "anho";
    public static $tablename2 = "mes";

    public function __construct()
    {
        $this->id = "";
        $this->tabla = "";
        $this->nombre = "";
        $this->valor1 = "";
        $this->valor2 = "";
        $this->valor3 = "";
        $this->estado = "";
    }

    public function getTabla()
    {
        return TablaData::getById($this->tabla);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (tabla,nombre,valor1,valor2,valor3,estado) value (\"$this->tabla\",\"$this->nombre\",";
        $sql .= "\"$this->valor1\",\"$this->valor2\",\"$this->valor3\",\"$this->estado\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set tabla = \"$this->tabla\",nombre = \"$this->nombre\",valor1 = \"$this->valor1\",";
        $sql .= "valor2 = \"$this->valor2\",valor3 = \"$this->valor3\" where id = \"$this->id\"";        
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
        return Model::one($query[0], new ParametroData());
    }

    public static function getAll($estado = "", $tabla = "", $nombre = "", $valor = "")
    {
        $sql = "select p.* from " . self::$tablename . " p ";
        $sql .= "join tablas t on t.id = p.tabla where 1=1 ";
        if ($tabla != "") {
            $sql .= "and t.nombre = \"$tabla\" ";
        }
        if ($estado != "") {
            $sql .= "and p.estado = \"$estado\" ";
        }
        if ($nombre != "") {
            $sql .= "and p.nombre = \"$nombre\" ";
        }
        if ($valor != "") {
            $sql .= "and p.valor3 = \"$valor\" ";
        }
        $sql .= "order by p.valor1 asc";        
        $query = Executor::doit($sql);
        return Model::many($query[0], new ParametroData());
    }
    
    public static function getAllDocuments($estado = "", $tabla = "", $nombre = "", $valor = "")
    {
        $sql = "select p.* from " . self::$tablename . " p ";
        $sql .= "join tablas t on t.id = p.tabla where 1=1 ";
        if ($tabla != "") {
            $sql .= "and t.nombre = \"$tabla\" ";
        }
        if ($estado != "") {
            $sql .= "and p.estado = \"$estado\" ";
        }
        if ($nombre != "") {
            $sql .= "and p.nombre = \"$nombre\" ";
        }
        if ($valor != "") {
            $sql .= "and p.valor3 = \"$valor\" ";
        }
        $sql .= "order by p.valor1 asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ParametroData());
    }
    
    public static function getAllVouchers($estado = "", $tabla = "", $nombre = "", $valor = "")
    {
        $sql = "select p.* from " . self::$tablename . " p ";
        $sql .= "join tablas t on t.id = p.tabla where 1=1 and p.valor2 > 0 ";
        if ($tabla != "") {
            $sql .= "and t.nombre = \"$tabla\" ";
        }
        if ($estado != "") {
            $sql .= "and p.estado = \"$estado\" ";
        }
        if ($nombre != "") {
            $sql .= "and p.nombre = \"$nombre\" ";
        }
        if ($valor != "") {
            $sql .= "and p.valor3 = \"$valor\" ";
        }
        $sql .= "order by p.valor1 asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ParametroData());
    }
    
    public static function getByAnho($id)
    {
        $sql = "select id,nombre as anho from " . self::$tablename1 . " where id = \"$id\" ";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ParametroData());
    }
    
    public static function getAllAnho()
    {
        $sql = "select id,nombre as anho from " . self::$tablename1;
        $query = Executor::doit($sql);
        return Model::many($query[0], new ParametroData());
    }
    
    public static function getAllMes()
    {
        $sql = "select id,nombre as mes from " . self::$tablename2;
        $query = Executor::doit($sql);
        return Model::many($query[0], new ParametroData());
    }
}