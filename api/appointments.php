<?php
/**
 * Appointment API Endpoints
 * Handles GET, POST, PUT, DELETE operations for appointments
 */

require_once __DIR__ . '/config.php';

setJsonHeaders();
initializeDatabase();

$db = getDatabaseConnection();
$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route the request
if (strpos($request_uri, '/api/appointments') !== false) {
    if ($request_method === 'GET') {
        handleGetAppointments($db);
    } elseif ($request_method === 'POST') {
        handleCreateAppointment($db);
    } elseif ($request_method === 'PUT') {
        handleUpdateAppointment($db);
    } elseif ($request_method === 'DELETE') {
        handleDeleteAppointment($db);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}

/**
 * Get all appointments or a specific appointment
 */
function handleGetAppointments($db) {
    try {
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        $filter = isset($_GET['filter']) ? $_GET['filter'] : null; // 'pending', 'confirmed', 'completed'
        
        if ($id) {
            // Get single appointment
            $stmt = $db->prepare("SELECT * FROM appointments WHERE id = ?");
            $stmt->execute([$id]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($appointment) {
                echo json_encode($appointment);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Appointment not found']);
            }
        } else {
            // Get all appointments
            $query = "SELECT * FROM appointments";
            $params = [];
            
            if ($filter && in_array($filter, ['pending', 'confirmed', 'completed'])) {
                $query .= " WHERE status = ?";
                $params[] = $filter;
            }
            
            $query .= " ORDER BY appointment_date DESC, appointment_time DESC";
            
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'status' => 'success',
                'count' => count($appointments),
                'data' => $appointments
            ]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error retrieving appointments: ' . $e->getMessage()]);
    }
}

/**
 * Create a new appointment
 */
function handleCreateAppointment($db) {
    try {
        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate input
        $errors = validateAppointmentData($input);
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }
        
        // Insert appointment
        $stmt = $db->prepare("
            INSERT INTO appointments 
            (patient_name, patient_email, patient_phone, doctor, appointment_date, appointment_time, message, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        
        $result = $stmt->execute([
            $input['patient_name'],
            $input['patient_email'],
            $input['patient_phone'],
            $input['doctor'],
            $input['appointment_date'],
            $input['appointment_time'],
            $input['message'] ?? ''
        ]);
        
        if ($result) {
            $id = $db->lastInsertId();
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Appointment created successfully',
                'id' => $id,
                'appointment' => [
                    'id' => $id,
                    'patient_name' => $input['patient_name'],
                    'patient_email' => $input['patient_email'],
                    'status' => 'pending'
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to create appointment']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Update appointment status or details
 */
function handleUpdateAppointment($db) {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Appointment ID is required']);
            return;
        }
        
        // Build update query dynamically
        $allowed_fields = ['status', 'patient_name', 'patient_email', 'patient_phone', 'doctor', 'appointment_date', 'appointment_time', 'message'];
        $updates = [];
        $params = [];
        
        foreach ($input as $key => $value) {
            if (in_array($key, $allowed_fields)) {
                $updates[] = "$key = ?";
                $params[] = $value;
            }
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['error' => 'No valid fields to update']);
            return;
        }
        
        $params[] = $id;
        $stmt = $db->prepare("
            UPDATE appointments 
            SET " . implode(', ', $updates) . ", updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        
        if ($stmt->execute($params)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Appointment updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update appointment']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Delete an appointment
 */
function handleDeleteAppointment($db) {
    try {
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Appointment ID is required']);
            return;
        }
        
        $stmt = $db->prepare("DELETE FROM appointments WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Appointment deleted successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete appointment']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

?>
