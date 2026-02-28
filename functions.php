<?php
// functions.php - FIXED VERSION
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

class MapaFunctions {
    private $db;
    private $logFile;
    
    public function __construct() {
        $this->logFile = __DIR__ . '/debug.log';
        try {
            $database = new Database();
            $this->db = $database->getConnection();
            
            if (!$this->db) {
                throw new Exception("Database connection failed");
            }
            
            file_put_contents($this->logFile, date('[Y-m-d H:i:s] ') . "Database connected successfully\n", FILE_APPEND);
        } catch (Exception $e) {
            file_put_contents($this->logFile, date('[Y-m-d H:i:s] ') . "Database connection error: " . $e->getMessage() . "\n", FILE_APPEND);
            throw $e;
        }
    }
    
    public function createTablesIfNotExist() {
        // Tabela mapa
        $sql1 = "CREATE TABLE IF NOT EXISTS mapa (
            id INT AUTO_INCREMENT PRIMARY KEY,
            interasaun_i VARCHAR(1000),
            interasaun_ii VARCHAR(1000),
            interasaun_iii VARCHAR(1000),
            interasaun_iv VARCHAR(1000),
            interasaun_v VARCHAR(1000),
            interasaun_vi VARCHAR(1000),
            interasaun_vii VARCHAR(1000),
            interasaun_viii VARCHAR(1000),
            interasaun_ix VARCHAR(1000),
            interasaun_x VARCHAR(1000),
            interasaun_xi VARCHAR(1000),
            interasaun_xii VARCHAR(1000),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        // Tabela lokasaun
        $sql2 = "CREATE TABLE IF NOT EXISTS lokasaun (
            id INT AUTO_INCREMENT PRIMARY KEY,
            naran VARCHAR(100) NOT NULL,
            distansia INT DEFAULT 0,
            kategoria VARCHAR(10) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $this->db->query($sql1);
            $this->db->query($sql2);
            return true;
        } catch (Exception $e) {
            file_put_contents($this->logFile, date('[Y-m-d H:i:s] ') . "Error creating tables: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
    
    public function getLokasaunPorKategoria($kategoria) {
        $sql = "SELECT * FROM lokasaun WHERE kategoria = ? ORDER BY id ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->error);
            }
            
            $stmt->bind_param("s", $kategoria);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $lokasaun = [];
            while ($row = $result->fetch_assoc()) {
                $lokasaun[] = $row;
            }
            
            $stmt->close();
            return $lokasaun;
        } catch (Exception $e) {
            file_put_contents($this->logFile, date('[Y-m-d H:i:s] ') . "Error getLokasaunPorKategoria: " . $e->getMessage() . "\n", FILE_APPEND);
            return [];
        }
    }
    
    public function raiDadusInterasaunHotu($dadus) {
        file_put_contents($this->logFile, date('[Y-m-d H:i:s] ') . "Starting raiDadusInterasaunHotu\n", FILE_APPEND);
        file_put_contents($this->logFile, date('[Y-m-d H:i:s] ') . "Input data: " . json_encode($dadus) . "\n", FILE_APPEND);
        
        $sql = "INSERT INTO mapa (
            interasaun_i, interasaun_ii, interasaun_iii, 
            interasaun_iv, interasaun_v, interasaun_vi, 
            interasaun_vii, interasaun_viii, interasaun_ix, 
            interasaun_x, interasaun_xi, interasaun_xii
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $this->db->error);
            }
            
            // Map dadus husi format "interasaun_1" ba "interasaun_i"
            $i = $dadus['interasaun_1'] ?? '';
            $ii = $dadus['interasaun_2'] ?? '';
            $iii = $dadus['interasaun_3'] ?? '';
            $iv = $dadus['interasaun_4'] ?? '';
            $v = $dadus['interasaun_5'] ?? '';
            $vi = $dadus['interasaun_6'] ?? '';
            $vii = $dadus['interasaun_7'] ?? '';
            $viii = $dadus['interasaun_8'] ?? '';
            $ix = $dadus['interasaun_9'] ?? '';
            $x = $dadus['interasaun_10'] ?? '';
            $xi = $dadus['interasaun_11'] ?? '';
            $xii = $dadus['interasaun_12'] ?? '';
            
            file_put_contents($this->logFile, date('[Y-m-d H:i:s] ') . "Mapped values:\n", FILE_APPEND);
            file_put_contents($this->logFile, "  I: $i\n  II: $ii\n  III: $iii\n", FILE_APPEND);
            
            $stmt->bind_param(
                "ssssssssssss",
                $i, $ii, $iii, $iv, $v, $vi,
                $vii, $viii, $ix, $x, $xi, $xii
            );
            
            if ($stmt->execute()) {
                $insertId = $this->db->insert_id;
                file_put_contents($this->logFile, date('[Y-m-d H:i:s] ') . "Data saved successfully. ID: $insertId\n", FILE_APPEND);
                
                $stmt->close();
                
                return [
                    'success' => true,
                    'message' => 'Dadus rai ho susesu!',
                    'id' => $insertId
                ];
            } else {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            file_put_contents($this->logFile, date('[Y-m-d H:i:s] ') . "Error in raiDadusInterasaunHotu: $errorMsg\n", FILE_APPEND);
            
            return [
                'success' => false,
                'message' => 'Error atu rai dadus: ' . $errorMsg
            ];
        }
    }
    
    public function getDadusHotu() {
        $sql = "SELECT * FROM mapa ORDER BY created_at DESC LIMIT 10";
        
        try {
            $result = $this->db->query($sql);
            
            $dadus = [];
            while ($row = $result->fetch_assoc()) {
                $dadus[] = $row;
            }
            
            return $dadus;
        } catch (Exception $e) {
            file_put_contents($this->logFile, date('[Y-m-d H:i:s] ') . "Error getDadusHotu: " . $e->getMessage() . "\n", FILE_APPEND);
            return [];
        }
    }
}
?>