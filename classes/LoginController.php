<?php
/**
 * Basic LoginController class with constructor and properties
 * Demonstrates: Class definition, private properties, constructor
 */
class LoginController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        $this->startSession();
    }
    
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Private method to find user by username
     * Demonstrates: Encapsulation, prepared statements
     */
    private function findUserByUsername($username) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate login inputs
     * Demonstrates: Method abstraction, validation logic
     */
    private function validateLoginInputs($username, $password) {
        $errors = [];
        
        if (empty($username)) {
            $errors[] = "Username is required";
        }
        
        if (empty($password)) {
            $errors[] = "Password is required";
        }
        
        if (strlen($username) < 3) {
            $errors[] = "Username must be at least 3 characters";
        }
        
        return $errors;
    }
    
    /**
     * Main login method
     * Demonstrates: Public interface, method composition, return arrays
     */
    public function login($username, $password) {
        // Use validation method
        $validationErrors = $this->validateLoginInputs($username, $password);
        
        if (!empty($validationErrors)) {
            return [
                'success' => false,
                'message' => implode(', ', $validationErrors)
            ];
        }
        
        // Use private database method
        $user = $this->findUserByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            $this->setUserSession($user);
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'user_id' => $user['id']
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Invalid credentials'
        ];
    }
    
    /**
     * Set user session data
     * Demonstrates: Private helper method, session security
     */
    private function setUserSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['login_time'] = time();
        
        // Security: regenerate session ID
        session_regenerate_id(true);
    }
    
    /**
     * Check if user is logged in
     * Demonstrates: Boolean return, session checking
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get current user information
     * Demonstrates: Conditional return, array structure
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? 'Unknown',
            'login_time' => $_SESSION['login_time'] ?? null
        ];
    }
    
    /**
     * Logout user and clean session
     * Demonstrates: Complete session cleanup, error handling
     */
    public function logout() {
        try {
            // Clear all session variables
            $_SESSION = [];
            
            // Delete session cookie if it exists
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }
            
            // Destroy the session
            session_destroy();
            
            return [
                'success' => true,
                'message' => 'Successfully logged out'
            ];
            
        } catch (Exception $e) {
            error_log("Logout error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error during logout'
            ];
        }
    }
}
    
