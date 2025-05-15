<?php
// api/create_reminder.php

// Set headers to allow cross-origin requests and specify JSON content
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Check for POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST method is allowed']);
    exit;
}

// Get the posted data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (
    empty($data['date']) || 
    empty($data['time']) || 
    empty($data['message']) || 
    empty($data['reminderType']) ||
    empty($data['contactInfo'])
) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Validate date format (YYYY-MM-DD)
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $data['date'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format. Use YYYY-MM-DD']);
    exit;
}

// Validate time format (HH:MM)
if (!preg_match("/^\d{2}:\d{2}$/", $data['time'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid time format. Use HH:MM']);
    exit;
}

// Validate reminder type
if (!in_array($data['reminderType'], ['SMS', 'Email'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid reminder type. Use SMS or Email']);
    exit;
}

try {
    // Get database connection
    $pdo = require '../config/database.php';
    
    // Prepare the SQL statement
    $sql = "INSERT INTO reminders (reminder_date, reminder_time, message, reminder_type, contact_info) 
            VALUES (:date, :time, :message, :reminderType, :contactInfo)";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters and execute
    $result = $stmt->execute([
        ':date' => $data['date'],
        ':time' => $data['time'],
        ':message' => $data['message'],
        ':reminderType' => $data['reminderType'],
        ':contactInfo' => $data['contactInfo']
    ]);
    
    if ($result) {
        // Get the ID of the newly created reminder
        $reminderId = $pdo->lastInsertId();
        
        http_response_code(201); // Created
        echo json_encode([
            'success' => true,
            'message' => 'Reminder created successfully',
            'id' => $reminderId
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to create reminder']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>