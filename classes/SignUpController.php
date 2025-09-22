<?php
class SignUpController {
    private $db;
    private $userModel;

    public function __construct($db, $userModel) {
        $this->db = $db;
        $this->userModel = $userModel;
    }

    public function signup($username, $password, $email) {
        // Check if username already exists
        $existingUser = $this->userModel->findByUsername($username);
        if ($existingUser) {
            return false; 
        }

        
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        
        $userId = $this->userModel->createUser([
            'username' => $username,
            'email'    => $email,
            'password' => $hashedPassword
        ]);

        if ($userId) {
            // Auto login 
            session_start();
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            return true;
        }

        return false;
    }
}
