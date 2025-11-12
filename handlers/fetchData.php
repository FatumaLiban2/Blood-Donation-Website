<?php

// Turn off display_errors for this API endpoint to prevent HTML output
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Set JSON header first to ensure any errors are treated as JSON
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../autoload.php';


// Check if the user is authenticated
if (!class_exists('SessionManager') || !SessionManager::isAuthenticated()) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// Ensure logged in user is currently an admin
$currentUserEmail = SessionManager::getCurrentEmail();
if ($currentUserEmail === null || !str_starts_with($currentUserEmail, 'admin@')) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

try {
    $patients = [];
    $admins = [];

    if (class_exists('Patient') && is_callable(['Patient', 'fetchAll'])) {
        $patients = Patient::fetchAll();
    }

    if (class_exists('Admin') && is_callable(['Admin', 'fetchAll'])) {
        $admins = Admin::fetchAll();
    }

    if (class_exists('Appointments') && is_callable(['Appointments', 'getAllAppointments'])) {
        $allAppointments = Appointments::getAllAppointments();
    }
    // Return data as JSON
    http_response_code(200);
    echo json_encode(['patients' => $patients, 'admins' => $admins, 'appointments' => $allAppointments], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}