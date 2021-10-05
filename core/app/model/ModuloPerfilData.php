<?php

class ModuloPerfilData
{

    public static $tablename = "modulos_perfil";

    public function __construct()
    {
        $this->id = "";
        $this->perfil = "";
        $this->modulo = "";
        $this->indicador = "";
    }

    public function getPerfil()
    {
        return PerfilData::getById($this->perfil);
    }

    public function getModulo()
    {
        return ModuloData::getById($this->modulo);
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (perfil,modulo,indicador) value (\"$this->perfil\",\"$this->modulo\",\"$this->indicador\")";
        return Executor::doit($sql);
    }

    public function delete()
    {
        $sql = "delete from " . self::$tablename . " where idperfil = \"$this->perfil\"";
        return Executor::doit($sql);
    }
    
    public function deleteModulo($id)
    {
        $sql = "delete from " . self::$tablename . " where id = $id";
        return Executor::doit($sql);
    }
    
    public static function getAllByPerfil($idPerfil)
    {
        $sql = "select mp.* from " . self::$tablename . " mp, modulos m where mp.modulo=m.id and mp.perfil = \"$idPerfil\" and mp.indicador = '0' and ";
        $sql .= "m.estado = '1' order by m.orden asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ModuloPerfilData());
    }

    public static function existModulo($idPerfil, $idModulo, $indicador)
    {
        $sql = "select * from " . self::$tablename . " where perfil = \"$idPerfil\" and modulo = \"$idModulo\" and indicador = \"$indicador\" order by id asc";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ModuloPerfilData());
    }

    public static function getAllModuloHijos($idPerfil, $idModulo)
    {
        $sql = "select mp.* from " . self::$tablename . " mp, modulos m where mp.modulo = m.id and mp.perfil = \"$idPerfil\" and m.id_padre = \"$idModulo\" and ";
        $sql .= "mp.indicador = '1' and m.estado = '1' order by orden asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ModuloPerfilData());
    }    
}