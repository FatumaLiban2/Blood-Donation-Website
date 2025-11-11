<?php

require_once __DIR__ . '/../autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resetPasswordSubmit'])) {
        $password = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmNewPassword'];

        if ($password !== $confirmPassword) {
            header("Location: ../index.php?error=passwordmissmatch&resetPassword=start");
            exit();
        }
        
        session_start();
        if (!isset($_SESSION['forgot_password_email'])) {
            header("Location: ../index.php?error=invalidsession");
            exit();
        }

        $email = $_SESSION['forgot_password_email'];
        $patient = Patient::findByEmail($email);

        if ($patient->updatePassword($password)) {
            // Clear session variables related to password reset
            unset($_SESSION['forgot_password_email']);
            unset($_SESSION['forgot_password_name']);
            unset($_SESSION['forgot_password_code']);
            unset($_SESSION['forgot_password_code_expiry']);

            header("Location: ../index.php?resetpassword=success");
            exit();
        } else {
            header("Location: ../index.php?error=resetfailed&resetPassword=start");
            exit();
        }

    }
}   