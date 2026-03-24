<?php
/**
 * Export Appointments to Excel/CSV
 * 
 * This endpoint generates an Excel/CSV file with appointment data.
 * Supports filtering by status, date, and patient name.
 */

// Enable error reporting for debugging
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    // Check if valid request
    if (!isset($_GET['action']) || $_GET['action'] !== 'export') {
        http_response_code(400);
        die('Invalid request');
    }

    // Include database config
    require_once __DIR__ . '/../config.php';
    
    // Initialize database
    initializeDatabase();
    $db = getDatabaseConnection();

    // Get filter parameters
    $statusFilter = $_GET['status'] ?? '';
    $dateFilter = $_GET['date'] ?? '';
    $searchFilter = strtolower($_GET['search'] ?? '');

    // Build query with filters
    $query = "SELECT * FROM appointments WHERE 1=1";
    $params = [];

    if (!empty($statusFilter)) {
        $query .= " AND status = ?";
        $params[] = $statusFilter;
    }

    if (!empty($dateFilter)) {
        $query .= " AND appointment_date = ?";
        $params[] = $dateFilter;
    }

    if (!empty($searchFilter)) {
        $query .= " AND (patient_name LIKE ? OR patient_email LIKE ?)";
        $params[] = "%{$searchFilter}%";
        $params[] = "%{$searchFilter}%";
    }

    $query .= " ORDER BY appointment_date DESC, appointment_time DESC";

    // Execute query
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If no appointments found, send error message
    if (empty($appointments)) {
        http_response_code(204);
        die('No appointments found for export');
    }

    // Set headers for CSV download (Excel can read CSV format)
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="Hospital_Appointments_' . date('Y-m-d_H-i-s') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add BOM for Excel to recognize UTF-8 properly
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Write header row
    fputcsv($output, [
        'Appointment ID',
        'Patient Name',
        'Email',
        'Phone',
        'Doctor',
        'Appointment Date',
        'Appointment Time',
        'Status',
        'Booked Date',
        'Medical Notes'
    ]);

    // Write data rows
    foreach ($appointments as $apt) {
        fputcsv($output, [
            $apt['id'] ?? '',
            $apt['patient_name'] ?? '',
            $apt['patient_email'] ?? '',
            $apt['patient_phone'] ?? '',
            $apt['doctor'] ?? '',
            $apt['appointment_date'] ?? '',
            $apt['appointment_time'] ?? '',
            $apt['status'] ?? '',
            $apt['created_at'] ?? '',
            $apt['message'] ?? ''
        ]);
    }

    fclose($output);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Error exporting appointments: ' . $e->getMessage()
    ]);
    exit;
}

