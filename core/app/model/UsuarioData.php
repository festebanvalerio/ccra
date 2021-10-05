<?php

class UsuarioData
{

    public static $tablename = "usuarios";

    public function __construct()
    {
        $this->id = "";
        $this->username = "";
        $this->password = "";
        $this->nombres = "";
        $this->apellidos = "";
        $this->perfil = "";
        $this->sede = "";
        $this->caja = "";
        $this->estado = "";
        $this->fecha_creacion = "";
        $this->fecha_actualizacion = "";
        $this->fecha_ultimo_ingreso = "";
    }

    public function getEstado()
    {
        return EstadoData::getById($this->estado);
    }

    public function getPerfil()
    {
        return PerfilData::getById($this->perfil);
    }

    public function getLocal()
    {
        return SedeData::getById($this->sede);
    }

    public function getCaja()
    {
        return CajaData::getById($this->caja);
    }
    
    public function add()
    {
        $sql = "insert into " . self::$tablename . " (username,password,nombres,apellidos,perfil,sede,caja,estado,fecha_creacion,";
        $sql .= "fecha_actualizacion) value (\"$this->username\",\"$this->password\",\"$this->nombres\",\"$this->apellidos\",";
        $sql .= "\"$this->perfil\",\"$this->sede\",\"$this->caja\",\"$this->estado\",\"$this->fecha_creacion\",\"$this->fecha_actualizacion\")";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set nombres = \"$this->nombres\",apellidos = \"$this->apellidos\",perfil = \"$this->perfil\",";
        $sql .= "fecha_actualizacion = \"$this->fecha_actualizacion\" where id = \"$this->id\"";
        return Executor::doit($sql);
    }

    public function updatePassword()
    {
        $sql = "update " . self::$tablename . " set password = \"$this->password\",fecha_actualizacion = \"$this->fecha_actualizacion\" where id = \"$this->id\"";
        return Executor::doit($sql);
    }

    public function updateAccess()
    {
        $sql = "update " . self::$tablename . " set fecha_ultimo_ingreso = \"$this->fecha_ultimo_ingreso\" ";
        $sql .= "where id = \"$this->id\"";
        return Executor::doit($sql);
    }
    
    public function delete()
    {
        $sql = "update " . self::$tablename . " set estado = \"$this->estado\",fecha_actualizacion = \"$this->fecha_actualizacion\" ";
        $sql .= "where id = \"$this->id\"";
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where id = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new UsuarioData());
    }

    public static function getByUsername($username)
    {
        $sql = "select count(*) AS total from " . self::$tablename . " where username like '" . $username . "%'";
        $query = Executor::doit($sql);
        return Model::one($query[0], new UsuarioData());
    }
        
    public static function getAll($estado = "", $perfil = "", $sede = "", $usuario = "")
    {
        $sql = "select * from " . self::$tablename . " where 1=1 ";
        if ($estado != "") {
            $sql .= "and estado = \"$estado\" ";
        }
        if ($perfil != "") {
            $sql .= "and perfil = \"$perfil\" ";
        }
        if ($sede != "") {
            $sql .= "and sede = \"$sede\" ";
        }
        if ($usuario != "") {
            $sql .= "and id = \"$usuario\" ";
        }
        $sql .= "order by username asc";        
        $query = Executor::doit($sql);
        return Model::many($query[0], new UsuarioData());
    }

    public static function getAllMesero($estado = "", $sede = "", $usuario = "")
    {
        $sql = "select u.* from " . self::$tablename . " u ";
        $sql .= "join perfiles p on p.id = u.perfil ";
        $sql .= "where p.indicador = 2 and p.estado = 1 ";
        if ($estado != "") {
            $sql .= "and u.estado = \"$estado\" ";
        }        
        if ($sede != "") {
            $sql .= "and u.sede = \"$sede\" ";
        }
        if ($usuario != "") {
            $sql .= "and u.id = \"$usuario\" ";
        }
        $sql .= "order by u.username asc";        
        $query = Executor::doit($sql);
        return Model::many($query[0], new UsuarioData());
    }
    
    public static function validate($usuario, $password)
    {
        $sql = "select u.*,s.nombre as nom_sede,e.id as empresa,e.ruc as ruc from " . self::$tablename . " u ";
        $sql .= "join sedes s on s.id = u.sede ";
        $sql .= "join empresas e on e.id = s.empresa ";
        $sql .= "where u.username= \"$usuario\" and u.password= \"$password\"";        
        $query = Executor::doit($sql);
        return Model::one($query[0], new UsuarioData());
    }
        
}