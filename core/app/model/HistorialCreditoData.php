<?php

class HistorialCreditoData
{

    public static $tablename = "historial_creditos";

    public function __construct()
    {
        $this->id = "";
        $this->credito = "";
        $this->fecha = "";
        $this->monto = "";
    }

    public function getCredito()
    {
        return CreditoData::getById($this->credito);
    }
    
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (credito,fecha,monto) value (\"$this->credito\",\"$this->fecha\",\"$this->monto\")";
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new HistorialCreditoData());
    }

    public static function getByCredito($credito = "", $fechaInicio = "", $fechaFin = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($credito != "") {
            $sql .= "and credito = \"$credito\" ";
        }
        if ($fechaInicio != "") {
            $arrFecha = explode("/", $fechaInicio);
            $fechaInicio = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(fecha) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $arrFecha = explode("/", $fechaFin);
            $fechaFin = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(fecha) <= \"$fechaFin\" ";
        }
        $sql .= "order by fecha desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new HistorialCreditoData());
    }
}