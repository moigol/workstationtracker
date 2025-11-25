<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';
$dbname = 'workstationtracker';
$username = 'movidev';
$password = 'movidev';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'Failed', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Validate required fields
if (!isset($_POST['scanner_id']) || !isset($_POST['tag_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'Failed', 'message' => 'Missing required fields: scanner_id or tag_id.']);
    exit;
}

try {
    // Check if scanner exists
    $stmt = $pdo->prepare("SELECT id FROM scanners WHERE id = ?");
    $stmt->execute([$_POST['scanner_id']]);
    $scanner = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$scanner) {
        http_response_code(404);
        echo json_encode(['status' => 'Failed', 'message' => 'Scanner not found', 'access' => ""]);
        exit;
    }

    // Check if RFID tag exists
    $stmt = $pdo->prepare("SELECT tag_id FROM rfid_tags WHERE tag_id = ?");
    $stmt->execute([$_POST['tag_id']]);
    $tag = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tag) {
        // Insert the log entry
        $stmt = $pdo->prepare("INSERT INTO tags (scanner_id, tag_id, date_time) VALUES (?, ?, ?)");
        
        // Use provided date_time or current timestamp
        $date_time = isset($_POST['date_time']) ? $_POST['date_time'] : date('Y-m-d H:i:s');
        
        $stmt->execute([
            $_POST['scanner_id'],
            $_POST['tag_id'],
            $date_time
        ]);

        http_response_code(404);
        echo json_encode(['status' => 'Failed', 'message' => 'RFID tag not found', 'access' => ""]);
        exit;
    }

    // Check Last type in the same Scanner
    $stmt = $pdo->prepare("SELECT type FROM logs WHERE tag_id = ? AND scanner_id = ? ORDER BY date_time DESC");
    $stmt->execute([$_POST['tag_id'], $_POST['scanner_id']]);
    $typeNow = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Insert the log entry
    $stmt = $pdo->prepare("INSERT INTO logs (type, scanner_id, tag_id, date_time) VALUES (?, ?, ?, ?)");
    
    // Use provided date_time or current timestamp
    $date_time = isset($_POST['date_time']) ? $_POST['date_time'] : date('Y-m-d H:i:s');
    $type = isset($_POST['type']) ? $_POST['type'] : "In";

    if(isset($typeNow['type'])) {
        switch($typeNow['type']) {
            case "In":
                $type = "Out";
            break;
            case "Out":
                $type = "In";
            break;
        }
    }
    
    $stmt->execute([
        strtoupper($type),
        $_POST['scanner_id'],
        $_POST['tag_id'],
        $date_time
    ]);
    
    // Get the inserted log with additional information
    $logId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("
        SELECT l.*, s.name as scanner_name, t.name as tag_name 
        FROM logs l 
        JOIN scanners s ON l.scanner_id = s.id 
        JOIN rfid_tags t ON l.tag_id = t.tag_id 
        WHERE l.id = ?
    ");
    $stmt->execute([$logId]);
    $log = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'Success', 'message' => $log['tag_name'] ." logged ". $type ." in ". $_POST['device_id'], 'access' => $type]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'Failed', 'message' => 'DB error: ' . $e->getMessage(), 'access' => ""]);
}