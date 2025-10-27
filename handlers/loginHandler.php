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