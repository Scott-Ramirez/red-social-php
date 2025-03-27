<?php
require_once __DIR__ . '/../config/config.php';

class Database {
    private $conn;

    public function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $_ENV['DB_HOST'] . 
                ";port=" . $_ENV['DB_PORT'] . 
                ";dbname=" . $_ENV['DB_DATABASE'] . 
                ";charset=utf8",
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD']
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            die("❌ Error de conexión: " . $e->getMessage());
        }
    }
}
