<?php

class InsumoAlmacenData
{

    public static $tablename = "insumos_almacen";
 
    public function __construct()
    {
        $this->id = "";
        $this->almacen = "";        
        $this->insumo = "";
        $this->stock = "";
        $this->stock_minimo = "";
        $this->stock_maximo = "";
        $this->estado = "";
    }

    public function getAlmacen()
    {
        return AlmacenData::getById($this->almacen);    
    }
    
    public function getInsumo()
    {
        return InsumoData::getById($this->insumo);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (almacen,insumo,stock,stock_minimo,stock_maximo,estado) value ";
        $sql .= "(\"$this->almacen\",\"$this->insumo\",\"$this->stock\",\"$this->stock_minimo\",\"$this->stock_maximo\",";
        $sql .= "\"$this->estado\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set stock = \"$this->stock\",stock_minimo = \"$this->stock_minimo\",";
        $sql .= "stock_maximo = \"$this->stock_maximo\" where id = \"$this->id\"";
        return Executor::doit($sql);
    }
    
    public function updateStock()
    {
        $sql = "update " . self::$tablename . " set stock = \"$this->stock\" where id = \"$this->id\"";        
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
        return Model::one($query[0], new InsumoAlmacenData());
    }
    
    public static function getAllByInsumo($estado = "", $insumo = "", $almacen = "", $indicador = "")
    {
        $sql = "";
        if ($indicador == "") {
            $sql .= "select * from " . self::$tablename . " ia where 1=1 ";            
        } else {
            $sql .= "select ia.*,c.nombre as clasificacion from " . self::$tablename . " ia ";
            $sql .= "join insumos i on i.id = ia.insumo ";
            $sql .= "join clasificaciones c on c.id = i.clasificacion ";
            $sql .= "where stock > 0 ";
        }
        if ($estado != "") {
            $sql .= "and ia.estado = \"$estado\" ";
        }
        if ($almacen != "") {
            $sql .= "and ia.almacen = \"$almacen\" ";
        }
        if ($insumo != "") {
            $sql .= "and ia.insumo = \"$insumo\" ";
        }
        $sql .= "order by ia.insumo asc";        
        $query = Executor::doit($sql);
        return Model::many($query[0], new InsumoAlmacenData());
    }

    public static function getAll($almacen = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($almacen != "") {
            $sql .= "and almacen = \"$almacen\" ";
        }        
        $sql .= "order by insumo asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new InsumoAlmacenData());
    }
}