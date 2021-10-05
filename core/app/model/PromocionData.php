<?php

class PromocionData
{

    public static $tablename = "promociones";

    public function __construct()
    {
        $this->id = "";
        $this->categoria = "";
        $this->producto = "";
        $this->cantidad = "";
        $this->precio = "";
        $this->dias = "";
        $this->estado = "";
    }

    public function getCategoria()
    {
        return CategoriaData::getById($this->categoria);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (categoria,producto,cantidad,precio,dias,estado) value (\"$this->categoria\",\"$this->producto\",\"$this->cantidad\",";
        $sql .= "\"$this->precio\",\"$this->dias\",\"$this->estado\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set categoria = \"$this->categoria\",producto = \"$this->producto\",cantidad = \"$this->cantidad\",precio = \"$this->precio\",";
        $sql .= "dias = \"$this->dias\" where id = \"$this->id\"";
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
        return Model::one($query[0], new PromocionData());
    }

    public static function getAll($estado = "", $categoria = "", $producto = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($categoria != "") {
            $sql .= "and categoria = \"$categoria\" ";
        }
        if ($producto != "") {
            $sql .= "and producto in ($producto) ";
        }        
        $query = Executor::doit($sql);
        return Model::many($query[0], new PromocionData());
    }        
}