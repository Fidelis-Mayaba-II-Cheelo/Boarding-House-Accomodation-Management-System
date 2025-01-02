<?php

class MySQLSingleton extends DatabaseHelper implements IDatabase{

    private static $instance = null;
    private $mysqli;

    private function __construct(){
        parent::__construct();

        try{
            $this->mysqli = new Mysqli(
                $this->hostname,
                $this->username,
                $this->password,
                $this->database,
                $this->port
            );
        } catch(Exception $ex){
            die("MySQLI connection failed: ". $ex->getMessage());
        }
        
    }

    //MySQLSingleton instance
    static function getInstance():IDatabase{
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function getConnection()
    {
        return $this->mysqli;
    }
}