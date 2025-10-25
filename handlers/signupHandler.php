<?php

require_once "../autoload.php";

use Services;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['signupSubmit'])) {
        // Grab data from the form
        $first_name = $_POST['firstName'];
        $last_name = $_POST['lastName'];
        $telephone = $_POST['telephone'];
        $email = $_POST['signupEmail'];
        $password = $_POST['signupPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        $fullName = $first_name . " " . $last_name;

        // Check if the fields are empty
        if (empty($first_name) || empty($last_name) || empty($telephone) || empty($email) || empty($password) || empty($confirmPassword)) {
            header("Location: ../index.php?error=emptyfields");
            exit();
        }

        // Check of passwords match
        if ($password !== $confirmPassword) {
            header("Location: ../index.php?error=passwordmissmatch");
            exit();
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../index.php?error=invalidemail");
            exit();
        }

        // Instanciate patient class
        $patient = new Patient();

        // Check if the email already exists
        if (Patient::findByEmail($email)) {
            header("Location: ../index.php?error=emailexists");
            exit();
        }

        if ($patient->register($first_name, $last_name, $telephone, $email, $password)) {
            $patientId = Patient::findId($email);

            $verificationCode = OTPCode::generate();

            session_start();
            $_SESSION['verification_code'] = $verificationCode;
            $_SESSION['verification_code_expiry'] = time() + 300; // 5 minutes expiry
            $_SESSION['pending_verification_id'] = $patientId;
            $_SESSION['pending_verification_email'] = $email;

            $mail = new Services\Mail($email);

            // Send OTP email
            if ($mail->sendOTPMail($fullName, $verificationCode)) {
                // If email is sent successfully, redirect to show verification modal
                header("Location: ../index.php?verification=sent");
                exit();
            } else {
                // If email sending fails, redirect to error page
                header("Location: ../index.php?error=failedtosendverificationemail");
                exit();
            }

        } else {
            header("Location: ../index.php?error=registrationfailed");
            exit();
        }
    }

}
