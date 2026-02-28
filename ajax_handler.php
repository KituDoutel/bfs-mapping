<?php
// ajax_handler.php - FIXED VERSION
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

header('Content-Type: application/json');

// Log file ba debug
$logFile = __DIR__ . '/debug.log';

try {
    require_once 'config.php';
    require_once 'functions.php';
    
    $rawInput = file_get_contents('php://input');
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Raw Input: " . $rawInput . "\n", FILE_APPEND);
    
    $input = json_decode($rawInput, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON decode error: ' . json_last_error_msg());
    }
    
    if (!isset($input['action'])) {
        throw new Exception('Action la define');
    }
    
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Action: " . $input['action'] . "\n", FILE_APPEND);
    
    $mapaFunc = new MapaFunctions();
    
    switch ($input['action']) {
        case 'rai_dadus_interasaun_hotu':
            // Versaun ba 12 interasaun
            if (!isset($input['dadus'])) {
                throw new Exception('Dadus la define');
            }
            
            $dadus = $input['dadus'];
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Dadus: " . json_encode($dadus) . "\n", FILE_APPEND);
            
            // Verifica se iha dadus
            $ihaDadus = false;
            foreach ($dadus as $key => $value) {
                if (!empty($value) && strpos($key, 'interasaun_') === 0) {
                    $ihaDadus = true;
                    break;
                }
            }
            
            if (!$ihaDadus) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Favor hili lokasaun interasaun antes atu rai dadus!'
                ]);
                exit;
            }
            
            // Rai dadus
            $result = $mapaFunc->raiDadusInterasaunHotu($dadus);
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Result: " . json_encode($result) . "\n", FILE_APPEND);
            
            echo json_encode($result);
            break;
            
        default:
            throw new Exception('Action la rekonhese: ' . $input['action']);
    }
    
} catch (Exception $e) {
    $errorMsg = $e->getMessage();
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "ERROR: " . $errorMsg . "\n", FILE_APPEND);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Stack trace: " . $e->getTraceAsString() . "\n", FILE_APPEND);
    
    echo json_encode([
        'success' => false,
        'message' => $errorMsg,
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
?>