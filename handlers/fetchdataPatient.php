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
    // Get appointments using Appointments class
    $appointmentsData = Appointments::getAppointmentsByPatientId($patientId);
    
    // Convert to arrays - handle both object and array responses
    $appointments = [];
    foreach ($appointmentsData as $apt) {
        // Check if it's an object or array
        if (is_object($apt)) {
            $appointments[] = [
                'appointment_id' => $apt->appointment_id ?? null,
                'patient_id' => $apt->patient_id ?? null,
                'full_name' => $apt->full_name ?? '',
                'appointment_type' => $apt->appointment_type ?? '',
                'schedule_day' => isset($apt->appointment_date) ? $apt->appointment_date->format('Y-m-d') : null,
                'schedule_time' => isset($apt->appointment_time) ? $apt->appointment_time->format('H:i:s') : null,
                'blood_group' => $apt->blood_group ?? '',
                'additional_notes' => $apt->additional_notes ?? null,
                'is_completed' => $apt->is_completed ?? false
            ];
        } else if (is_array($apt)) {
            $appointments[] = [
                'appointment_id' => $apt['appointments_id'] ?? null,
                'patient_id' => $apt['patient_id'] ?? null,
                'full_name' => $apt['full_name'] ?? '',
                'appointment_type' => $apt['appointment_type'] ?? '',
                'schedule_day' => $apt['schedule_day'] ?? null,
                'schedule_time' => $apt['schedule_time'] ?? null,
                'blood_group' => $apt['blood_group'] ?? '',
                'additional_notes' => $apt['additional_notes'] ?? null,
                'is_completed' => $apt['is_completed'] ?? false
            ];
        }
    }
    
    // Calculate statistics
    $completedAppointments = array_filter($appointments, fn($apt) => $apt['is_completed']);
    $upcomingAppointments = array_filter($appointments, function($apt) {
        return !$apt['is_completed'] && 
               $apt['schedule_day'] && 
               strtotime($apt['schedule_day']) >= strtotime('today');
    });
    
    $completedCount = count($completedAppointments);
    $upcomingCount = count($upcomingAppointments);
    
    // Get last completed appointment date
    $lastAppointment = Appointments::getLastCompletedAppointment($patientId);
    $lastDonation = $lastAppointment ? date('M d, Y', strtotime($lastAppointment)) : '-';
    
    // Calculate next eligible date (56 days after last donation)
    $nextEligible = '-';
    if ($lastAppointment) {
        $lastDate = new DateTime($lastAppointment);
        $lastDate->modify('+56 days');
        $nextEligible = $lastDate->format('M d, Y');
    }
    
    // Build stats object
    $stats = [
        'totalDonations' => $completedCount,
        'upcomingCount' => $upcomingCount,
        'lastDonation' => $lastDonation,
        'nextEligible' => $nextEligible
    ];
    
    // Format appointments for display
    $formattedAppointments = array_map(function($apt) {
        $time = $apt['schedule_time'] ? date('h:i A', strtotime($apt['schedule_time'])) : '-';
        
        return [
            'appointment_id' => $apt['appointment_id'],
            'appointment_date' => $apt['schedule_day'],
            'appointment_time' => $time,
            'appointment_type' => $apt['appointment_type'],
            'blood_type' => $apt['blood_group'], // Note: Using blood_type for consistency with JS
            'blood_group' => $apt['blood_group'],
            'full_name' => $apt['full_name'],
            'notes' => $apt['additional_notes'],
            'status' => $apt['is_completed'] ? 'completed' : 'upcoming',
            'created_at' => $apt['schedule_day']
        ];
    }, $appointments);
    
    // Get recent appointments (last 5)
    $recentAppointments = array_slice($formattedAppointments, 0, 5);
    
    // Get completed appointments as donation history
    $history = array_filter($formattedAppointments, fn($apt) => $apt['status'] === 'completed');
    $history = array_values($history); // Re-index array
    
    // Get patient profile info
    $patient = Patient::findByEmail($session->email);
    $profile = [
        'name' => $patient ? $patient->getFirstName() . ' ' . $patient->getLastName() : '',
        'email' => $session->email,
        'telephone' => $patient ? $patient->getTelephone() : '',
        'created_at' => date('Y-m-d')
    ];
    
    // Return all data
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'recentAppointments' => $recentAppointments,
        'appointments' => $formattedAppointments,
        'history' => $history,
        'profile' => $profile
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error loading dashboard data: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
