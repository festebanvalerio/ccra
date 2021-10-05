<?php

class HistorialDocumentoData
{

    public static $tablename = "historial_documentos";

    public function __construct()
    {
        $this->id = "";
        $this->historial_pago = "";        
        $this->comprobante = "";        
        $this->estado = "";
    }

    public function getHistorialPago()
    {
        return HistorialPagoData::getById($this->historial_pago);    
    }
    
    public function getComprobante()
    {
        return ComprobanteData::getById($this->comprobante);
    }
    
    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (historial_pago,comprobante,estado) value (\"$this->historial_pago\",\"$this->comprobante\",\"$this->estado\")";
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
        return Model::one($query[0], new HistorialDocumentoData());
    }
    
    public static function getByHistorialPago($historialPago)
    {
        $sql = "select * from " . self::$tablename . " where historial_pago = \"$historialPago\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new HistorialDocumentoData());
    }
    
    public static function getAll($estado = "", $sede = "", $tipo = "", $fechaInicio = "", $fechaFin = "", $indicador = "")
    {
        $sql = "select hd.*,c.cs_tipodocumento_cod,c.fe_comprobante_fecenvsun,c.fe_comprobante_faucod,c.fe_comprobante_estsun from " . self::$tablename . " hd ";
        $sql .= "join comprobantes c on c.fe_comprobante_id = hd.comprobante ";
        $sql .= "join historial_pagos hp on hp.id = hd.historial_pago ";
        $sql .= "join pagos pa on pa.id = hp.pago ";
        $sql .= "join pedidos pe on pe.id = pa.pedido ";
        $sql .= "where pe.sede = \"$sede\" ";
        if ($estado != "") {
            $sql .= "and hd.estado = \"$estado\" ";
        }
        if ($tipo != "") {
            $sql .= "and c.cs_tipodocumento_cod = \"$tipo\" ";
        } else {
            if ($indicador != "") {
                $sql .= "and c.cs_tipodocumento_cod in (1,3) ";
            }
        }
        if ($fechaInicio != "") {
            $arrFecha = explode("/", $fechaInicio);
            $fechaInicio = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(c.fe_comprobante_reg) >= \"$fechaInicio\" ";
        }
        if ($fechaFin != "") {
            $arrFecha = explode("/", $fechaFin);
            $fechaFin = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
            $sql .= "and date(c.fe_comprobante_reg) <= \"$fechaFin\" ";
        }
        $sql .= "order by c.fe_comprobante_reg desc";        
        $query = Executor::doit($sql);
        return Model::many($query[0], new HistorialDocumentoData());
    }
    
    public static function getAllByPago($pago)
    {
        $sql = "select hd.* from " . self::$tablename . " hd ";
        $sql .= "join historial_pagos hp on hp.id = hd.historial_pago ";
        $sql .= "join pagos pa on pa.id = hp.pago "; 
        $sql .= "where hd.estado = 1 and hp.estado = 1 and hp.pago = \"$pago\" ";        
        $sql .= "order by pa.fecha_creacion asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new HistorialDocumentoData());
    }

}