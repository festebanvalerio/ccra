<?php

class AreaData
{

    public static $tablename = "areas";

    public function __construct()
    {
        $this->id = "";
        $this->sede = "";
        $this->nombre = "";
        $this->impresora = "";
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
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (sede,nombre,impresora,estado,fecha_creacion,usuario_creacion,usuario_actualizacion,fecha_actualizacion) ";
        $sql .= "value (\"$this->sede\",\"$this->nombre\",\"$this->impresora\",\"$this->estado\",\"$this->fecha_creacion\",\"$this->usuario_creacion\",";
        $sql .= "\"$this->usuario_actualizacion\",\"$this->fecha_actualizacion\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set nombre = \"$this->nombre\",impresora = \"$this->impresora\",fecha_actualizacion = \"$this->fecha_actualizacion\",";
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
        return Model::one($query[0], new AreaData());
    }

    public static function getAll($estado = "", $sede = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($sede != "") {
            $sql .= "and sede = \"$sede\" ";
        }
        $sql .= "order by id asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new AreaData());
    }
    
    public static function getAllAreaRepetida($estado, $sede, $nombre, $id = "")
    {
        $sql = "select * from " . self::$tablename . " where estado = \"$estado\" and sede = \"$sede\" and nombre = \"$nombre\" ";
        if ($id != "") {
            $sql .= "and id not in (\"$id\")";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new AreaData());
    }
    
    public static function getAllAreaXProducto($pedido, $opcion = 0)
    {
        $sql = "select distinct a.* ";
        $sql .= "from detalle_pedidos dp ";
        $sql .= "join pedidos pe on pe.id = dp.pedido ";
        $sql .= "join productos_area pa on pa.producto = dp.producto ";
        $sql .= "join " . self::$tablename . " a on a.id = pa.area ";
        $sql .= "where pe.id = \"$pedido\" and dp.estado = 1 and a.estado = 1 ";
        if ($opcion == 0) {
            $sql .= "and a.impresora != ''";
        } else {
            $sql .= "and (dp.fecha_comanda is null or dp.fecha_comanda = '0000-00-00 00:00:00')";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new AreaData());
    }
}