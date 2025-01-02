<?php

require_once __DIR__ . '/../../../vendor/autoload.php'; 

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

class DatabaseHelper {
    protected $hostname;
    protected $username;
    protected $password;
    protected $database;
    protected $port;

    protected function __construct() {
        $this->hostname = $_ENV['DATABASE_HOSTNAME'];
        $this->username = $_ENV['DATABASE_USERNAME'];
        $this->password = $_ENV['DATABASE_PASSWORD'];
        $this->database = $_ENV['DATABASE_NAME'];
        $this->port = $_ENV['DATABASE_PORT'];
    }
}
