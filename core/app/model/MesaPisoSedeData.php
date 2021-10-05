<?php

class MesaPisoSedeData
{

    public static $tablename = "mesas_pisos_sede";

    public function __construct()
    {
        $this->id = "";
        $this->piso_sede = "";
        $this->mesa = "";
    }

    public function getPisoSede()
    {
        return PisoSedeData::getById($this->piso_sede);
    }
    
    public function getMesa()
    {
        return MesaData::getById($this->mesa);
    }
    
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (piso_sede,mesa) value (\"$this->piso_sede\",\"$this->mesa\")";
        return Executor::doit($sql);
    }

    public function delete()
    {
        $sql = "delete from " . self::$tablename . " where piso_sede = \"$this->piso_sede\"";        
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new MesaPisoSedeData());
    }

    public static function getMesaxPisoxSede($pisoSede = "", $mesa = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($pisoSede != "") {
            $sql .= "and piso_sede = \"$pisoSede\" ";
        }
        if ($mesa != "") {
            $sql .= "and mesa = \"$mesa\" ";
        }
        $sql .= "order by mesa asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MesaPisoSedeData());
    }

    public static function getInfo($pisoSede, $mesa)
    {
        $sql = "select * from " . self::$tablename . " where piso_sede = \"$pisoSede\" and mesa = \"$mesa\" ";
        $query = Executor::doit($sql);
        return Model::one($query[0], new MesaPisoSedeData());
    }
    
    public static function getMesaDisponibleXPiso($pisoSede, $sede) {
        $sql = "select mps.* from " . self::$tablename . " mps ";
        $sql .= "join pisos_sede ps on ps.id = mps.piso_sede ";
        $sql .= "join mesas m on m.id = mps.mesa ";
        $sql .= "where mps.piso_sede = \"$pisoSede\" and m.id not in (select pe.mesa from pedidos pe where pe.estado = 1 AND pe.sede = \"$sede\") ";
        $sql .= "order by m.id asc";        
        $query = Executor::doit($sql);
        return Model::many($query[0], new MesaPisoSedeData());
    }    
}