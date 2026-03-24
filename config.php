<?php
/**
 * Database Configuration File
 * Sets up SQLite database connection and configuration
 */

// Database file path
define('DB_PATH', __DIR__ . '/data/hospital.db');

// Create database connection
function getDatabaseConnection() {
    try {
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        http_response_code(500);
        die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
    }
}

// Initialize database schema
function initializeDatabase() {
    $db = getDatabaseConnection();
    
    try {
        // Check if table exists
        $tableCheck = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='appointments'");
        
        if (!$tableCheck->fetch()) {
            // Create appointments table
            $db->exec("
                CREATE TABLE appointments (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    patient_name TEXT NOT NULL,
                    patient_email TEXT NOT NULL,
                    patient_phone TEXT NOT NULL,
                    doctor TEXT NOT NULL,
                    appointment_date TEXT NOT NULL,
                    appointment_time TEXT NOT NULL,
                    message TEXT,
                    status TEXT DEFAULT 'pending',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Create doctors table
            $db->exec("
                CREATE TABLE doctors (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    specialty TEXT NOT NULL,
                    email TEXT NOT NULL,
                    phone TEXT NOT NULL,
                    available INTEGER DEFAULT 1
                )
            ");
            
            // Insert sample doctors
            $db->exec("
                INSERT INTO doctors (name, specialty, email, phone) VALUES
                ('Dr. Sarah Mitchell', 'Cardiology', 'sarah.mitchell@riyamedicare.com', '+1-555-0201'),
                ('Dr. James Chen', 'Neurology', 'james.chen@riyamedicare.com', '+1-555-0202'),
                ('Dr. Elena Rodriguez', 'Orthopedics', 'elena.rodriguez@riyamedicare.com', '+1-555-0203'),
                ('Dr. Michael Johnson', 'General Practice', 'michael.johnson@riyamedicare.com', '+1-555-0204'),
                ('Dr. Lisa Anderson', 'Pediatrics', 'lisa.anderson@riyamedicare.com', '+1-555-0205'),
                ('Dr. Robert Williams', 'Dermatology', 'robert.williams@riyamedicare.com', '+1-555-0206')
            ");
            
            return true;
        }
    } catch (PDOException $e) {
        error_log('Database initialization error: ' . $e->getMessage());
        return false;
    }
    
    return true;
}

// Set headers for API responses
function setJsonHeaders() {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    
    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit(json_encode(['status' => 'ok']));
    }
}

// Input validation
function validateAppointmentData($data) {
    $errors = [];
    
    if (empty($data['patient_name'])) $errors[] = 'Patient name is required';
    if (empty($data['patient_email']) || !filter_var($data['patient_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    if (empty($data['patient_phone'])) $errors[] = 'Phone number is required';
    if (empty($data['doctor'])) $errors[] = 'Doctor selection is required';
    if (empty($data['appointment_date'])) $errors[] = 'Appointment date is required';
    if (empty($data['appointment_time'])) $errors[] = 'Appointment time is required';
    
    return $errors;
}

?>
