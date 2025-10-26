<?php

require_once __DIR__ . '/../autoload.php';

use Services\Mail;

session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'method_not_allowed']);
    exit();
}

if (!isset(
    $_SESSION['pending_verification_email'],
    $_SESSION['pending_verification_id'],
    $_SESSION['pending_verification_name']
)) {
    http_response_code(400);
    echo json_encode(['error' => 'verification_session_missing']);
    exit();
}

$verificationCode = OTPCode::generate();

$_SESSION['verification_code'] = $verificationCode;
$_SESSION['verification_code_expiry'] = time() + 300;

$mail = new Mail($_SESSION['pending_verification_email']);

if ($mail->resendOTP($_SESSION['pending_verification_name'], $verificationCode)) {
    echo json_encode(['status' => 'ok', 'expires_in' => 300]);
    exit();
}

http_response_code(500);
echo json_encode(['error' => 'mail_send_failed']);
