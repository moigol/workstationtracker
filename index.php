<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Manila');

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
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get the request path
$request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_path = str_replace('/index.php', '', $request_path); // Remove index.php if present
$method = $_SERVER['REQUEST_METHOD'];

// API routes
if (strpos($request_path, '/api/') === 0) {
    header('Content-Type: application/json');
    
    // Logs API endpoints
    if ($request_path === '/api/logs' && $method === 'GET') {
        // Get logs with filters and pagination
        $scanner_id = $_GET['scanner_id'] ?? null;
        $tag_id = $_GET['tag_id'] ?? null;
        $staff_id = $_GET['staff_id'] ?? null;
        $type = $_GET['type'] ?? null;
        $date_from = $_GET['date_from'] ?? null;
        $date_to = $_GET['date_to'] ?? null;
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = min(100, max(1, intval($_GET['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;
        
        try {
            // Build base query
            $baseQuery = "FROM logs l 
                        JOIN scanners s ON l.scanner_id = s.id 
                        JOIN rfid_tags t ON l.tag_id = t.tag_id 
                        LEFT JOIN staffs i ON t.tag_id = i.tag_id
                        WHERE 1=1";
            
            $params = [];
            
            if ($scanner_id) {
                $baseQuery .= " AND l.scanner_id = ?";
                $params[] = $scanner_id;
            }
            
            if ($tag_id) {
                $baseQuery .= " AND l.tag_id = ?";
                $params[] = $tag_id;
            }
            
            if ($staff_id) {
                $baseQuery .= " AND i.id = ?";
                $params[] = $staff_id;
            }
            
            if ($type) {
                $baseQuery .= " AND l.type = ?";
                $params[] = strtoupper($type);
            }
            
            if ($date_from) {
                $baseQuery .= " AND l.date_time >= ?";
                $params[] = $date_from . ' 00:00:00';
            }
            
            if ($date_to) {
                $baseQuery .= " AND l.date_time <= ?";
                $params[] = $date_to . ' 23:59:59';
            }
            
            // Count query
            $countQuery = "SELECT COUNT(*) as total " . $baseQuery;
            $stmt = $pdo->prepare($countQuery);
            $stmt->execute($params);
            $totalResult = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalLogs = $totalResult['total'];
            $totalPages = ceil($totalLogs / $limit);
            
            // Data query - use integers directly in the query for LIMIT and OFFSET
            $dataQuery = "SELECT l.*, s.name as scanner_name, t.name as tag_name, i.name as staff_name, i.avatar " . 
                        $baseQuery . " ORDER BY l.date_time DESC LIMIT " . $limit . " OFFSET " . $offset;
            
            $stmt = $pdo->prepare($dataQuery);
            $stmt->execute($params);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'logs' => $logs,
                'totalLogs' => $totalLogs,
                'totalPages' => $totalPages,
                'currentPage' => $page
            ]);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // RFID Tags API endpoints
    elseif ($request_path === '/api/rfid-tags' && $method === 'GET') {
        // Get all RFID tags
        $stmt = $pdo->query("SELECT rfid_tags.*, staffs.name AS staff_name FROM rfid_tags LEFT JOIN staffs ON rfid_tags.tag_id = staffs.tag_id ORDER BY rfid_tags.name");
        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($tags);
    }
    elseif ($request_path === '/api/rfid-tags' && $method === 'POST') {
        // Add new RFID tag
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO rfid_tags (tag_id, name, description) VALUES (?, ?, ?)");
            $stmt->execute([$input['tag_id'], $input['name'], $input['description']]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    elseif (preg_match('/^\/api\/rfid-tags\/(\d+)$/', $request_path, $matches) && $method === 'PUT') {
        // Update RFID tag
        $id = $matches[1];
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            $stmt = $pdo->prepare("UPDATE rfid_tags SET tag_id = ?, name = ?, description = ? WHERE id = ?");
            $stmt->execute([$input['tag_id'], $input['name'], $input['description'], $id]);
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    elseif (preg_match('/^\/api\/rfid-tags\/(\d+)$/', $request_path, $matches) && $method === 'DELETE') {
        // Delete RFID tag
        $id = $matches[1];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM rfid_tags WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    elseif ($request_path === '/api/unreg-tags' && $method === 'GET') {
        // Get all RFID tags
        $stmt = $pdo->query("SELECT * FROM tags ORDER BY date_time DESC");
        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($tags[0]);
    }

    // Items API endpoints
    elseif ($request_path === '/api/items' && $method === 'GET') {
        // Get all items with tag information
        try {
            $stmt = $pdo->query("
                SELECT i.*, t.name as tag_name 
                FROM items i 
                LEFT JOIN rfid_tags t ON i.tag_id = t.tag_id 
                ORDER BY i.name
            ");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($items);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    elseif ($request_path === '/api/items' && $method === 'POST') {
        // Add new item
        $input = json_decode(file_get_contents('php://input'), true);

        if (!empty($input['tag_id'])) {
            $stmt = $pdo->prepare("SELECT id FROM items WHERE tag_id = ? AND id != ?");
            $stmt->execute([$input['tag_id'], $id ?? 0]);
            $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingItem) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'This RFID tag is already assigned to another item']);
                exit;
            }
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO items (name, description, tag_id) VALUES (?, ?, ?)");
            $stmt->execute([
                $input['name'], 
                $input['description'], 
                !empty($input['tag_id']) ? $input['tag_id'] : null
            ]);
            
            // Get the inserted item with tag information
            $itemId = $pdo->lastInsertId();
            $stmt = $pdo->prepare("
                SELECT i.*, t.name as tag_name 
                FROM items i 
                LEFT JOIN rfid_tags t ON i.tag_id = t.tag_id 
                WHERE i.id = ?
            ");
            $stmt->execute([$itemId]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'item' => $item]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    elseif (preg_match('/^\/api\/items\/(\d+)$/', $request_path, $matches) && $method === 'PUT') {
        // Update item
        $id = $matches[1];
        $input = json_decode(file_get_contents('php://input'), true);

        if (!empty($input['tag_id'])) {
            $stmt = $pdo->prepare("SELECT id FROM items WHERE tag_id = ? AND id != ?");
            $stmt->execute([$input['tag_id'], $id ?? 0]);
            $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingItem) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'This RFID tag is already assigned to another item']);
                exit;
            }
        }
        
        try {
            $stmt = $pdo->prepare("UPDATE items SET name = ?, description = ?, tag_id = ? WHERE id = ?");
            $stmt->execute([
                $input['name'], 
                $input['description'], 
                !empty($input['tag_id']) ? $input['tag_id'] : null,
                $id
            ]);
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    elseif (preg_match('/^\/api\/items\/(\d+)$/', $request_path, $matches) && $method === 'DELETE') {
        // Delete item
        $id = $matches[1];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Staffs API endpoints
    elseif ($request_path === '/api/staffs' && $method === 'GET') {
        // Get all staffs with tag information
        try {
            $stmt = $pdo->query("
                SELECT i.*, t.name as tag_name 
                FROM staffs i 
                LEFT JOIN rfid_tags t ON i.tag_id = t.tag_id 
                ORDER BY i.name
            ");
            $staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($staffs);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    elseif ($request_path === '/api/staffs' && $method === 'POST') {
        // Add new item
        $input = json_decode(file_get_contents('php://input'), true);

        if (!empty($input['tag_id'])) {
            $stmt = $pdo->prepare("SELECT id FROM staffs WHERE tag_id = ? AND id != ?");
            $stmt->execute([$input['tag_id'], $id ?? 0]);
            $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingItem) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'This RFID tag is already assigned to another item']);
                exit;
            }
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO staffs (name, position, tag_id, allowed_stations, avatar) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['name'], 
                $input['position'], 
                !empty($input['tag_id']) ? $input['tag_id'] : null,
                implode('|',$input['allowed_stations']),
                $input['avatar']
            ]);
            
            // Get the inserted item with tag information
            $itemId = $pdo->lastInsertId();
            $stmt = $pdo->prepare("
                SELECT i.*, t.name as tag_name 
                FROM staffs i 
                LEFT JOIN rfid_tags t ON i.tag_id = t.tag_id 
                WHERE i.id = ?
            ");
            $stmt->execute([$itemId]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'item' => $item]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    elseif (preg_match('/^\/api\/staffs\/(\d+)$/', $request_path, $matches) && $method === 'PUT') {
        // Update item
        $id = $matches[1];
        $input = json_decode(file_get_contents('php://input'), true);

        if (!empty($input['tag_id'])) {
            $stmt = $pdo->prepare("SELECT id FROM staffs WHERE tag_id = ? AND id != ?");
            $stmt->execute([$input['tag_id'], $id ?? 0]);
            $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingItem) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'This RFID tag is already assigned to another item']);
                exit;
            }
        }
        
        try {
            $stmt = $pdo->prepare("UPDATE staffs SET name = ?, position = ?, tag_id = ?, allowed_stations = ?, avatar = ? WHERE id = ?");
            $stmt->execute([
                $input['name'], 
                $input['position'], 
                !empty($input['tag_id']) ? $input['tag_id'] : null,
                implode('|',$input['allowed_stations']),
                $input['avatar'],
                $id
            ]);
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    elseif (preg_match('/^\/api\/staffs\/(\d+)$/', $request_path, $matches) && $method === 'DELETE') {
        // Delete item
        $id = $matches[1];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM staffs WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Scanners API endpoints
    elseif ($request_path === '/api/scanners' && $method === 'GET') {
        // Get all scanners
        try {
            $stmt = $pdo->query("SELECT * FROM scanners ORDER BY name");
            $scanners = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($scanners);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    elseif ($request_path === '/api/scanners' && $method === 'POST') {
        // Add new scanner
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            // Let the database generate the ID with the prefix
            $stmt = $pdo->prepare("INSERT INTO scanners (name, description) VALUES (?, ?)");
            $stmt->execute([$input['name'], $input['description']]);
            
            // Get the inserted scanner with its generated ID
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    elseif (preg_match('/^\/api\/scanners\/(\d+)$/', $request_path, $matches) && $method === 'PUT') {
        // Update scanner
        $id = $matches[1];
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            $stmt = $pdo->prepare("UPDATE scanners SET name = ?, description = ? WHERE id = ?");
            $stmt->execute([$input['name'], $input['description'], $id]);
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    elseif (preg_match('/^\/api\/scanners\/(\d+)$/', $request_path, $matches) && $method === 'DELETE') {
        // Delete scanner
        $id = $matches[1];
        
        try {
            // Check if scanner is used in any logs
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM logs WHERE scanner_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Cannot delete scanner: it is used in logs']);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM scanners WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    elseif (preg_match('/^\/api\/scanners\/(\d+)$/', $request_path, $matches) && $method === 'GET') {
        // Get specific scanner details
        $scannerId = $matches[1];
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM scanners WHERE id = ?");
            $stmt->execute([$scannerId]);
            $scanner = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$scanner) {
                http_response_code(404);
                echo json_encode(['error' => 'Scanner not found']);
                exit;
            }
            
            echo json_encode($scanner);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    elseif ($request_path === '/api/scanner-stats' && $method === 'GET') {
        $scanner_id = $_GET['scanner_id'] ?? null;
        
        try {
            $query = "SELECT 
                COUNT(*) as total_scans,
                COUNT(CASE WHEN DATE(date_time) = CURDATE() THEN 1 END) as today_scans,
                MAX(date_time) as last_scan
                FROM logs WHERE 1=1";
            
            $params = [];
            
            if ($scanner_id) {
                $query .= " AND scanner_id = ?";
                $params[] = $scanner_id;
            }
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode($stats);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // Logs API endpoints
    elseif ($request_path === '/api/rfid-log' && $method === 'POST') {
        // Set timezone
        date_default_timezone_set('Asia/Manila');
        
        // Add new log entry
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (!isset($input['scanner_id']) || !isset($input['tag_id'])) {
            http_response_code(400);
            echo json_encode(['status' => 'Failed', 'message' => 'Missing required fields: scanner_id or tag_id.']);
            exit;
        }

        try {
            $scanner_id = $input['scanner_id'];
            $tag_id = $input['tag_id'];
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
                // Insert into unknown tags table
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
    }
    else {
        http_response_code(404);
        echo json_encode(['error' => 'API endpoint not found']);
    }
    exit;
}

// Frontend routes
else {

    // Serve the appropriate HTML file based on the request path
    $page = 'dashboard';
    
    if ($request_path === '/' || $request_path === '') {
        $page = 'dashboard';
    } 
    elseif ($request_path === '/scanners') {
        $page = 'scanners';
    }
    elseif ($request_path === '/rfid-tags') {
        $page = 'rfid-tags';
    }
    elseif ($request_path === '/items') {
        $page = 'items';
    }
    elseif ($request_path === '/staffs') {
        $page = 'staffs';
    }
    elseif ($request_path === '/scan') {
        $page = 'scan';
    }
    else {
        // If no matching route found, show 404 page
        http_response_code(404);
        include 'views/404.php';
        exit;
    }
    
    // Serve the main HTML file
    include "views/parts/header.php";
    include "views/$page.php";
    include "views/parts/footer.php";
    exit;
}