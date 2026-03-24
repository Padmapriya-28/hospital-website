<?php
/**
 * Doctor API Endpoint
 * Provides list of available doctors
 */

require_once __DIR__ . '/../config.php';

setJsonHeaders();
initializeDatabase();

$db = getDatabaseConnection();

try {
    $stmt = $db->prepare("SELECT id, name, specialty, email, phone FROM doctors WHERE available = 1 ORDER BY name");
    $stmt->execute();
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'count' => count($doctors),
        'data' => $doctors
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error retrieving doctors: ' . $e->getMessage()]);
}

?>
