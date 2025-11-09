<?php
require_once _DIR_ . '/../autoload.php';

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
$requiredFields = ['appointmentDate', 'appointmentTime', 'bloodType'];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Field $field is required"]);
        exit();
    }
}

$appointmentDate = $input['appointmentDate'];
$appointmentTime = $input['appointmentTime'];
$bloodType = $input['bloodType'];
$notes = $input['notes'] ?? '';

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
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Check if patient has already scheduled an appointment on this date
    $checkQuery = "
        SELECT COUNT(*) as count FROM appointments 
        WHERE patient_id = :patient_id 
        AND appointment_date = :appointment_date 
        AND status IN ('pending', 'confirmed')
    ";
    $stmt = $conn->prepare($checkQuery);
    $stmt->execute([
        'patient_id' => $patientId,
        'appointment_date' => $appointmentDate
    ]);
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'You already have an appointment on this date']);
        exit();
    }
    
    // Check if patient donated recently (must wait 56 days)
    $lastDonationQuery = "
        SELECT MAX(donation_date) as last_donation 
        FROM donations 
        WHERE patient_id = :patient_id AND status = 'completed'
    ";
    $stmt = $conn->prepare($lastDonationQuery);
    $stmt->execute(['patient_id' => $patientId]);
    $lastDonationResult = $stmt->fetch();
    
    if ($lastDonationResult['last_donation']) {
        $lastDonation = new DateTime($lastDonationResult['last_donation']);
        $nextEligible = clone $lastDonation;
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
    
    // Insert appointment
    $insertQuery = "
        INSERT INTO appointments (patient_id, appointment_date, appointment_time, blood_type, notes, status, created_at)
        VALUES (:patient_id, :appointment_date, :appointment_time, :blood_type, :notes, 'pending', NOW())
    ";
    
    $stmt = $conn->prepare($insertQuery);
    $success = $stmt->execute([
        'patient_id' => $patientId,
        'appointment_date' => $appointmentDate,
        'appointment_time' => $appointmentTime,
        'blood_type' => $bloodType,
        'notes' => $notes
    ]);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Appointment scheduled successfully',
            'appointmentId' => $conn->lastInsertId()
        ]);
    } else {
        throw new Exception('Failed to insert appointment');
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