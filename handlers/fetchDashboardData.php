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

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Get stats
    $statsQuery = "
        SELECT 
            (SELECT COUNT(*) FROM donations WHERE patient_id = :patient_id AND status = 'completed') as total_donations,
            (SELECT COUNT(*) FROM appointments WHERE patient_id = :patient_id AND status IN ('pending', 'confirmed') AND appointment_date >= CURRENT_DATE) as upcoming_count,
            (SELECT MAX(donation_date) FROM donations WHERE patient_id = :patient_id AND status = 'completed') as last_donation,
            (SELECT created_at FROM patients WHERE id = :patient_id) as member_since
    ";
    
    $stmt = $conn->prepare($statsQuery);
    $stmt->execute(['patient_id' => $patientId]);
    $statsData = $stmt->fetch();
    
    // Calculate next eligible date (56 days after last donation)
    $nextEligible = '-';
    if ($statsData['last_donation']) {
        $lastDate = new DateTime($statsData['last_donation']);
        $lastDate->modify('+56 days');
        $nextEligible = $lastDate->format('M d, Y');
    }
    
    $stats = [
        'totalDonations' => (int)$statsData['total_donations'],
        'upcomingCount' => (int)$statsData['upcoming_count'],
        'lastDonation' => $statsData['last_donation'] ? date('M d, Y', strtotime($statsData['last_donation'])) : '-',
        'nextEligible' => $nextEligible
    ];
    
    // Get recent appointments (last 5)
    $recentQuery = "
        SELECT * FROM appointments 
        WHERE patient_id = :patient_id 
        ORDER BY created_at DESC 
        LIMIT 5
    ";
    $stmt = $conn->prepare($recentQuery);
    $stmt->execute(['patient_id' => $patientId]);
    $recentAppointments = $stmt->fetchAll();
    
    // Get all appointments
    $appointmentsQuery = "
        SELECT * FROM appointments 
        WHERE patient_id = :patient_id 
        ORDER BY appointment_date DESC, appointment_time DESC
    ";
    $stmt = $conn->prepare($appointmentsQuery);
    $stmt->execute(['patient_id' => $patientId]);
    $appointments = $stmt->fetchAll();
    
    // Get donation history
    $historyQuery = "
        SELECT * FROM donations 
        WHERE patient_id = :patient_id AND status = 'completed'
        ORDER BY donation_date DESC
    ";
    $stmt = $conn->prepare($historyQuery);
    $stmt->execute(['patient_id' => $patientId]);
    $history = $stmt->fetchAll();
    
    // Get profile info
    $profile = [
        'created_at' => $statsData['member_since']
    ];
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'recentAppointments' => $recentAppointments,
        'appointments' => $appointments,
        'history' => $history,
        'profile' => $profile
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'error_details' => [
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}