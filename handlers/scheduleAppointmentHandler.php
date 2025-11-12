<?php
require_once __DIR__ . '/../autoload.php';

header('Content-Type: application/json');

// Validate session
$session = SessionManager::validateSession();
if (!$session) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$patientId = $session->patient_id;

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

// Validate required fields
$requiredFields = ['appointmentDate', 'appointmentTime', 'appointmentType', 'bloodGroup'];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Field $field is required"]);
        exit();
    }
}

$appointmentDate = $input['appointmentDate'];
$appointmentTime = $input['appointmentTime'];
$appointmentType = $input['appointmentType'];
$bloodGroup = $input['bloodGroup'];
$notes = $input['notes'] ?? null;

// Validate date is in the future
$today = new DateTime();
$today->setTime(0, 0, 0);
$selectedDate = new DateTime($appointmentDate);

if ($selectedDate < $today) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Appointment date must be in the future']);
    exit();
}

try {
    // Check if patient already has an appointment on this date
    if (Appointments::checkDuplicateAppointment($patientId, $appointmentDate)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'You already have an appointment on this date']);
        exit();
    }
    
    // Check if patient donated recently (must wait 56 days)
    $lastAppointment = Appointments::getLastCompletedAppointment($patientId);
    
    if ($lastAppointment) {
        $lastDate = new DateTime($lastAppointment);
        $nextEligible = clone $lastDate;
        $nextEligible->modify('+56 days');
        
        if ($selectedDate < $nextEligible) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => 'You must wait 56 days between donations. Next eligible date: ' . $nextEligible->format('M d, Y')
            ]);
            exit();
        }
    }
    
    // Get patient info for full name
    $patient = Patient::findByEmail($session->email);
    if (!$patient) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Patient not found']);
        exit();
    }
    
    $fullName = $patient->getFirstName() . ' ' . $patient->getLastName();
    
    // Create appointment using Appointments class
    $appointment = new Appointments();
    $success = $appointment->scheduleAppointment(
        $patientId,
        $fullName,
        $appointmentType,
        $appointmentDate,
        $appointmentTime,
        $bloodGroup,
        $notes
    );
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Appointment scheduled successfully'
        ]);
    } else {
        throw new Exception('Failed to schedule appointment');
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
