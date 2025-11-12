<?php

// Turn off display_errors for this API endpoint
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Set JSON header
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../autoload.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Check if the user is authenticated
if (!SessionManager::isAuthenticated()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

// Ensure logged in user is an admin
$currentUserEmail = SessionManager::getCurrentEmail();
if ($currentUserEmail === null || !str_starts_with($currentUserEmail, 'admin@')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Admin access required']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['appointment_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Appointment ID is required']);
        exit;
    }

    $appointment_id = (int)$input['appointment_id'];

    // Mark appointment as completed
    $result = Appointments::markAsCompleted($appointment_id);

    if ($result) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Appointment marked as completed']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to update appointment']);
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
