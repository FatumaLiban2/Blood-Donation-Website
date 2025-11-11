<?php

require_once __DIR__ . "/../autoload.php";

use Services\Mail;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'method_not_allowed']);
    exit();
}

// Parse JSON or form data for email
$requestPayload = file_get_contents('php://input');
$data = json_decode($requestPayload, true);

if (!is_array($data)) {
    // Fallback to form-encoded request
    $data = $_POST;
}

$email = isset($data['email']) ? trim($data['email']) : '';

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid_email']);
    exit();
}

// Locate account by email
$patient = Patient::findByEmail($email);

if (!$patient) {
    http_response_code(404);
    echo json_encode(['error' => 'account_not_found']);
    exit();
}

// Generate OTP and store in session for later verification
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$otpCode = OTPCode::generate();
$expiresInSeconds = 300; // 5 minutes

$_SESSION['forgot_password_email'] = $email;
$fullName = trim($patient->getFirstName() . ' ' . $patient->getLastName());
if ($fullName === '') {
    $fullName = 'User';
}
$_SESSION['forgot_password_name'] = $fullName;
$_SESSION['forgot_password_code'] = $otpCode;
$_SESSION['forgot_password_code_expiry'] = time() + $expiresInSeconds;

$mail = new Mail($email);
try {
    if ($mail->resendOTP($fullName, $otpCode)) {
        echo json_encode([
            'status' => 'ok',
            'expires_in' => $expiresInSeconds,
        ]);
        exit();
    }
} catch (Throwable $e) {
    // Fall through to error response below
    error_log('Resend OTP mail error: ' . $e->getMessage());
}

http_response_code(500);
echo json_encode(['error' => 'mail_send_failed']);
exit();