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
    
    public function login($username, $password) {
        $user = $this->findUserByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }
        return false;
    }
    
    public function logout() {
        // Session already started in constructor, no need to start again
        session_destroy();
    }
    
    public function isLoggedIn() {
        // Session already started in constructor, no need to start again
        return isset($_SESSION['user_id']);
    }
}
    
