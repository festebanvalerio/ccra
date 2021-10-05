<?php

class Database
{

    public static $db;

    public static $con;

    function __construct()
    {
        $this->user = "developer";
        $this->pass = "db2017";
        $this->host = "localhost";
        $this->ddbb = "ccra";
    }

    function connect()
    {
        $con = new mysqli($this->host, $this->user, $this->pass, $this->ddbb);
        mysqli_set_charset($con, "utf8");
        return $con;
    }

    public static function getCon()
    {
        if (self::$con == null && self::$db == null) {
            self::$db = new Database();
            self::$con = self::$db->connect();
        }
        return self::$con;
    }
}
