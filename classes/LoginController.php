<?php
class LoginController {
   private $db;
    private $userModel;

    public function __construct($db) {
        $this->db = $db;
    }
      public function login($username, $password) {
        $user = $this->userModel->findByUsername($username);
         if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }
        return false;
    }
    public function logout() {
        session_start();
        session_destroy();
    }

    public function isLoggedIn() {
        session_start();
        return isset($_SESSION['user_id']);
    }
}


