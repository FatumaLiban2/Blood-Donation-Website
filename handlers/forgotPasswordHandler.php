<?php

require_once __DIR__ . "/../autoload.php";

use Services\Mail;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['forgotPasswordSubmit'])) {
        $email = trim($_POST['forgotPasswordEmail']);

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../index.php?error=invalidemail");
            exit();
        }

        $patient = Patient::findByEmail($email);

        if (!$patient) {
            header("Location: ../index.php?error=accountnotfound");
            exit();
        }

        $fullName = $patient->getFirstName() . ' ' . $patient->getLastName();
        $otpCode = OTPCode::generate();
        $expiresInSeconds = 300; // 5 minutes

        session_start();
        $_SESSION['forgot_password_email'] = $email;
        $_SESSION['forgot_password_name'] = $fullName;
        $_SESSION['forgot_password_code'] = $otpCode;
        $_SESSION['forgot_password_code_expiry'] = time() + $expiresInSeconds;

        $mail = new Mail($email);

        if ($mail->sendForgotPasswordOTP($fullName, $otpCode)) {
            header("Location: ../index.php?forgotpassword=otpsent");
            exit();
        } else {
            header("Location: ../index.php?error=emailfailed");
            exit();
        }
    }

    if (isset($_POST['forgotPasswordOtpSubmit'])) {
        $enteredCode = trim($_POST['forgotPasswordOtp']);

        session_start();

        if (!isset($_SESSION['forgot_password_code']) || !isset($_SESSION['forgot_password_email'])) {
            header("Location: ../index.php?error=invalidsession");
            exit();
        }

        if (time() > $_SESSION['forgot_password_code_expiry']) {
            header("Location: ../index.php?error=forgotcodeexpired");
            exit();
        }

        if ($enteredCode === $_SESSION['forgot_password_code']) {
            // Code is correct, redirect to reset password page
            header("Location: ../index.php?resetPassword=start");
            exit();
        } else {
            header("Location: ../index.php?error=invalidresetcode");
            exit();
        }

    }
}