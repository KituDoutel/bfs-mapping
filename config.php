<?php
// config.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_mapa');

class Database {
    private $conn;
    
    public function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
            
        } catch (Exception $e) {
            error_log("✗ Database Error: " . $e->getMessage());
            die("Database Error: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function testConnection() {
        return $this->conn && $this->conn->ping();
    }
    
    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>