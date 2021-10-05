<?php

class ProductoData
{

    public static $tablename = "productos";
    public static $tablename1 = "recetas";
    
    public function __construct()
    {
        $this->id = "";
        $this->sede = "";
        $this->categoria = "";
        $this->tipo = "";        
        $this->nombre = "";
        $this->costo = "";
        $this->precio1 = "";
        $this->precio2 = "";
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
    
    public function getCategoria()
    {
        return CategoriaData::getById($this->categoria);
    }
        
    public function getTipo()
    {
        return ParametroData::getById($this->tipo);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (sede,categoria,tipo,nombre,costo,precio1,precio2,estado,fecha_creacion,";
        $sql .= "usuario_creacion,usuario_actualizacion,fecha_actualizacion) ";
        $sql .= "value (\"$this->sede\",\"$this->categoria\",\"$this->tipo\",\"$this->nombre\",";
        $sql .= "\"$this->costo\",\"$this->precio1\",\"$this->precio2\",\"$this->estado\",\"$this->fecha_creacion\",";
        $sql .= "\"$this->usuario_creacion\",\"$this->usuario_actualizacion\",\"$this->fecha_actualizacion\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set sede = \"$this->sede\",categoria = \"$this->categoria\",";
        $sql .= "tipo = \"$this->tipo\",nombre = \"$this->nombre\",costo = \"$this->costo\",";
        $sql .= "precio1 = \"$this->precio1\",precio2 = \"$this->precio2\",fecha_actualizacion = \"$this->fecha_actualizacion\",";
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
        return Model::one($query[0], new ProductoData());
    }

    public static function getAll($estado = "", $sede = "", $categoria = "", $tipo = "", $nombre = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($sede != "") {
            $sql .= "and sede = \"$sede\" ";
        }
        if ($categoria != "") {
            $sql .= "and categoria = \"$categoria\" ";
        }
        if ($tipo != "") {
            $sql .= "and tipo = \"$tipo\" ";
        }
        if ($nombre != "") {
            $sql .= "and nombre like '%$nombre%' ";
        }
        $sql .= "order by nombre asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ProductoData());
    }
    
    public function getAllSinReceta($sede = "")
    {
        $sql = "select p.* from " . self::$tablename . " p where ";
        $sql .= "p.id not in (select producto from " . self::$tablename1 . " r where r.estado = 1 ";
        if ($sede != "") {
            $sql .= "and r.sede = \"$sede\") and p.sede = \"$sede\" ";
        }
        $sql .= "and p.estado = 1 order by p.nombre asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ProductoData());
    }
    
}