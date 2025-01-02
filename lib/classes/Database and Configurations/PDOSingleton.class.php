<?php

class PDOSingleton extends DatabaseHelper implements IDatabase {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        parent::__construct();

        $dsn = 'mysql:host=' . $this->hostname . ';port=' . $this->port . ';dbname=' . $this->database;

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $ex) {
            die("PDO connection failed: " . $ex->getMessage());
        }
    }

    // Singleton instance method
    static function getInstance():IDatabase {
        if (self::$instance === null) {
            self::$instance = new self(); // Create an instance of PDOSingleton
        }
        return self::$instance;
    }

    // Get the PDO connection
    public function getConnection() {
        return $this->pdo;
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserializing
    public function __wakeup() {}
}
