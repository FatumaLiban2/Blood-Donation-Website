<?php

require_once "../autoload.php";

/**
 * SessionManager class handles user sessions and authentication
 * Implements singleton pattern for centralized session management
 */
class SessionManager {
    
    private static ?SessionManager $instance = null;
    
    /**
     * Private constructor to implement singleton pattern
     */
    private function __construct() {
        // Constructor logic will be added in next steps
    }
    
    /**
     * Get singleton instance of SessionManager
     */
    public static function getInstance(): SessionManager {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserializing of the instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize a singleton.");
    }
}
