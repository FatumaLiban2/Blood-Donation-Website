<?php

require_once __DIR__ . '/../autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['loginSubmit'])) {
        // Grab data from the form
        $email = $_POST['loginEmail'];
        $password = $_POST['loginPassword'];

        // Check if the fields are empty
        if (empty($email) || empty($password)) {
            header("Location: ../index.php?error=emptyfields");
            exit();
        }

        // Check if email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../index.php?error=invalidemail");
            exit();
        }

        // Check if email belongs to admin
        if (str_starts_with($email, 'admin@')) {
            // Check if the email exists in the admin table
            $admin = Admin::findByEmail($email);
            if ($admin === null) {
                header("Location: ../index.php?error=emailnotfound");
                exit();
            } else {
                if ($admin->verifyPassword($password)) {
                    SessionManager::startSession($admin->getId(), $admin->getEmail());
                    header("Location: ../views/admin.php?login=success");
                    exit();
                } else {
                    header("Location: ../index.php?error=wrongpassword");
                    exit();
                }
            }
        }

        // Check if user can login
        $patient = Patient::findByEmail($email);

        if ($patient === null) {
            header("Location: ../index.php?error=emailnotfound");
            exit();
        } else {
            if($patient->verifyPassword($password)) {
                SessionManager::startSession($patient->getId(), $patient->getEmail());
                header("Location: ../views/info.php?login=success");
                exit();
            } else {
                header("Location: ../index.php?error=wrongpassword");
                exit();
            }
        }
    }
}