<?php
/**
 * Contact Form Handler
 * Processes contact form submissions and stores them in database
 */

require_once __DIR__ . '/config.php';

setJsonHeaders();
initializeDatabase();

$db = getDatabaseConnection();

try {
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    $errors = [];
    if (empty($input['full-name'])) $errors[] = 'Full name is required';
    if (empty($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    if (empty($input['phone'])) $errors[] = 'Phone number is required';
    if (empty($input['subject'])) $errors[] = 'Subject is required';
    if (empty($input['message'])) $errors[] = 'Message is required';
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }
    
    // Create contacts table if it doesn't exist
    $db->exec("
        CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT NOT NULL,
            subject TEXT NOT NULL,
            message TEXT NOT NULL,
            status TEXT DEFAULT 'unread',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Insert contact
    $stmt = $db->prepare("
        INSERT INTO contacts (full_name, email, phone, subject, message)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $input['full-name'],
        $input['email'],
        $input['phone'],
        $input['subject'],
        $input['message']
    ]);
    
    if ($result) {
        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Thank you for contacting us! We will get back to you soon.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit contact form']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
