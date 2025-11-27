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

// Set timezone
date_default_timezone_set('Asia/Manila');

// Validate required fields
if (!isset($_POST['scanner_id']) || !isset($_POST['tag_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'Failed', 'message' => 'Missing required fields: scanner_id or tag_id.']);
    exit;
}

try {
    $scanner_id = $_POST['scanner_id'];
    $tag_id = $_POST['tag_id'];
    $current_time = date('Y-m-d H:i:s');

    // Check if scanner exists
    $stmt = $pdo->prepare("SELECT id, name FROM scanners WHERE id = ?");
    $stmt->execute([$scanner_id]);
    $scanner = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$scanner) {
        http_response_code(404);
        echo json_encode(['status' => 'Failed', 'message' => 'Scanner not found', 'access' => ""]);
        exit;
    }

    // Check if RFID tag exists
    $stmt = $pdo->prepare("SELECT tag_id, name FROM rfid_tags WHERE tag_id = ?");
    $stmt->execute([$tag_id]);
    $tag = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tag) {
        // Insert the log entry
        $stmt = $pdo->prepare("INSERT INTO tags (scanner_id, tag_id, date_time) VALUES (?, ?, ?)");
        $stmt->execute([$scanner_id, $tag_id, $current_time]);

        http_response_code(404);
        echo json_encode(['status' => 'Failed', 'message' => 'RFID tag not found', 'access' => ""]);
        exit;
    }

    // Check if this tag belongs to a staff member
    $stmt = $pdo->prepare("SELECT * FROM staffs WHERE tag_id = ?");
    $stmt->execute([$tag_id]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($staff) {
        // STAFF VALIDATION LOGIC
        
        // 1. Check if staff is allowed in this station
        $allowed_stations = $staff['allowed_stations'] ?? '';
        $allowed_stations_array = $allowed_stations ? explode('|', $allowed_stations) : [];
        
        if (!empty($allowed_stations_array) && !in_array($scanner_id, $allowed_stations_array)) {
            http_response_code(403);
            echo json_encode([
                'status' => 'Failed', 
                'message' => 'Staff ' . $staff['name'] . ' is not allowed in station ' . $scanner['name'],
                'access' => 'DENIED'
            ]);
            exit;
        }
        
        // 2. Check if staff is already logged in at another station
        $stmt = $pdo->prepare("
            SELECT l.*, s.name as scanner_name 
            FROM logs l 
            JOIN scanners s ON l.scanner_id = s.id 
            WHERE l.tag_id = ? 
            AND l.type = 'In' 
            AND l.date_time_out IS NULL
            ORDER BY l.date_time DESC 
            LIMIT 1
        ");
        $stmt->execute([$tag_id]);
        $active_session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($active_session) {
            http_response_code(409);
            echo json_encode([
                'status' => 'Failed', 
                'message' => 'Staff ' . $staff['name'] . ' is already logged in at ' . $active_session['scanner_name'] . '. Please wait until it auto logs out.',
                'access' => 'ALREADY_LOGGED_IN',
                'current_station' => $active_session['scanner_name'],
                'login_time' => $active_session['date_time']
            ]);
            exit;
        }
    }
    
    // Check Last type in the same Scanner to determine IN/OUT
    $stmt = $pdo->prepare("
        SELECT type 
        FROM logs 
        WHERE tag_id = ? 
        AND scanner_id = ? 
        ORDER BY date_time DESC 
        LIMIT 1
    ");
    $stmt->execute([$tag_id, $scanner_id]);
    $typeNow = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Determine scan type
    $type = isset($input['type']) ? $input['type'] : "In";
    
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
    
    // For staff logging IN, check if they're trying to log into a different station while still logged in elsewhere
    if ($staff && strtoupper($type) === 'IN') {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as active_sessions 
            FROM logs 
            WHERE tag_id = ? 
            AND type = 'In' 
            AND date_time_out IS NULL 
            AND scanner_id != ?
        ");
        $stmt->execute([$tag_id, $scanner_id]);
        $active_sessions = $stmt->fetch(PDO::FETCH_ASSOC)['active_sessions'];
        
        if ($active_sessions > 0) {
            http_response_code(409);
            echo json_encode([
                'status' => 'Failed', 
                'message' => 'Cannot log in to ' . $scanner['name'] . '. Staff is already logged in at another station.',
                'access' => 'ALREADY_LOGGED_IN'
            ]);
            exit;
        }
    }
    
    // Insert the log entry
    $stmt = $pdo->prepare("INSERT INTO logs (type, scanner_id, tag_id, date_time) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        strtoupper($type),
        $scanner_id,
        $tag_id,
        $current_time
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
    
    $device = isset($input['device_id']) ? $input['device_id'] : '';
    
    // Prepare response message
    $staff_name = $staff ? $staff['name'] : $tag['name'];
    $message = $staff_name . " logged " . $type . " in " . $scanner['name'];
    
    if ($staff && strtoupper($type) === 'IN') {
        $message .= " (Access Granted)";
    }
    
    http_response_code(200);
    echo json_encode([
        'status' => 'Success', 
        'message' => $message, 
        'access' => $type,
        'staff_name' => $staff_name,
        'station_name' => $scanner['name'],
        'scan_type' => $type
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'Failed', 'message' => 'DB error: ' . $e->getMessage(), 'access' => ""]);
}