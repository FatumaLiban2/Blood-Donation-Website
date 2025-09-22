<?php
/**
 * Simple LoginController Class for Blood Donation Website
 * Demonstrates basic OOP concepts in a clean, easy way
 */
class LoginController {
    private $db;
    
    // Constructor - sets up the class
    public function __construct($db) {
        $this->db = $db;
        $this->startSession();
    }
    
    // Private method - starts session safely
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    // Private method - finds user in database
    private function findUser($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Private method - validates input
    private function validateInput($username, $password) {
        if (empty($username) || empty($password)) {
            return "Username and password are required";
        }
        return null; // No errors
    }
    
    // Public method - main login function
    public function login($username, $password) {
        // Check input
        $error = $this->validateInput($username, $password);
        if ($error) {
            return ['success' => false, 'message' => $error];
        }
        
        // Find user
        $user = $this->findUser($username);
        
        // Check password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return ['success' => true, 'message' => 'Login successful'];
        }
        
        return ['success' => false, 'message' => 'Invalid login'];
    }
    
    // Public method - logout user
    public function logout() {
        $_SESSION = [];
        session_destroy();
        return ['success' => true, 'message' => 'Logged out'];
    }
    
    // Public method - check if logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Public method - get current user
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username']
            ];
        }
        return null;
    }
}
    
