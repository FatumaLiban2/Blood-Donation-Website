<?php

require_once __DIR__ . "/../autoload.php";

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verifyCodeSubmit'])) {
        $enteredCode = $_POST['verificationCode'];

        // Check if the verification code is set in the session
        if (!isset($_SESSION['verification_code']) || !isset($_SESSION['pending_verification_id']) || !isset($_SESSION['pending_verification_email'])) {
            header("Location: ../index.php?error=invalidsession");
            exit();
        }

        // Check if code is expired
        if (time() > $_SESSION['verification_code_expiry']) {
            header("Location: ../index.php?error=codeexpired");
            exit();
        }

        // Verify the code
        if ($enteredCode === $_SESSION['verification_code']) {
            $patientId = $_SESSION['pending_verification_id'];
            $email = $_SESSION['pending_verification_email'];

            // Mark the patient as verified in the database
            if (Patient::markAsVerified($patientId)) {
                // Clear temporary session data
                unset($_SESSION['verification_code']);
                unset($_SESSION['verification_code_expiry']);
                unset($_SESSION['pending_verification_id']);
                unset($_SESSION['pending_verification_email']);
                unset($_SESSION['pending_verification_name']);

                // Redirect to verification success page
                header("Location: ../index.php?verification=success");
                exit();
            } else {
                header("Location: ../index.php?error=verificationfailed");
                exit();
            }
        } else {
            // If the code is incorrect, redirect with an error
            header("Location: ../index.php?error=invalidcode");
            exit();
        }
    }
}