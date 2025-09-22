<?php
class SignupController {
    private $db;
    private $userModel;
    
    public function __construct($db, $userModel) {
        $this->db = $db;
        $this->userModel = $userModel;
    }
    
    public function signup($username, $password, $email) {
        // Validate inputs
        $validation = $this->validateInputs($username, $password, $email);
        if (!$validation['valid']) {
            return ['success' => false, 'error' => $validation['message']];
        }
        
        // Check if username already exists
        $existingUser = $this->userModel->findByUsername($username);
        if ($existingUser) {
            return ['success' => false, 'error' => 'Username already exists'];
        }
        
        // Check if email already exists
        $existingEmail = $this->userModel->findByEmail($email);
        if ($existingEmail) {
            return ['success' => false, 'error' => 'Email already exists'];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Create user
        $userId = $this->userModel->createUser([
            'username' => $username,
            'email'    => $email,
            'password' => $hashedPassword
        ]);
        
        if ($userId) {
            // Auto login (ensure session is started elsewhere)
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            
            return ['success' => true, 'message' => 'Account created successfully'];
        }
        
        return ['success' => false, 'error' => 'Failed to create account'];
    }
    
    private function validateInputs($username, $password, $email) {
        if (empty($username) || empty($password) || empty($email)) {
            return ['valid' => false, 'message' => 'All fields are required'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'Invalid email format'];
        }
        
        if (strlen($password) < 6) {
            return ['valid' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        if (strlen($username) < 3) {
            return ['valid' => false, 'message' => 'Username must be at least 3 characters'];
        }
        
        return ['valid' => true];
    }
}
?>