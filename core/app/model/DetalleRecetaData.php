<?php

class DetalleRecetaData
{

    public static $tablename = "detalle_recetas";
    public static $tablenameAux = "recetas";
    
    public function __construct()
    {
        $this->id = "";
        $this->receta = "";
        $this->insumo = "";
        $this->cantidad = "";
        $this->precio = "";
        $this->estado = "";
    }

    public function getReceta()
    {
        return RecetaData::getById($this->receta);    
    }
    
    public function getInsumo()
    {
        return InsumoData::getById($this->insumo);
    }    
    
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (receta,insumo,cantidad,precio,estado) value (\"$this->receta\",\"$this->insumo\",";
        $sql .= "\"$this->cantidad\",\"$this->precio\",\"$this->estado\")";
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
        return Model::one($query[0], new DetalleRecetaData());
    }
    
    public static function getAllByReceta($receta)
    {
        $sql = "select * from " . self::$tablename . " where receta = \"$receta\" and estado = 1 ";
        $sql .= "order by id asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleRecetaData());
    }

    public static function getAllInsumosByProducto($producto)
    {
        $sql = "select dr.* from " . self::$tablename . " dr ";
        $sql .= "join " . self::$tablenameAux . " r on r.id = dr.receta ";
        $sql .= "where r.producto = \"$producto\" and r.estado = 1 and dr.estado = 1";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DetalleRecetaData());
    }

}