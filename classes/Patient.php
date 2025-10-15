<?php

require_once "../autoload.php";

class Patient {
    private $id;
    private $first_name;
    private $last_name;
    private $telephone;
    private $email;
    private $password;

    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    private static function fromDatabase(array $row): self {
        $patient = new self();
        $patient->id = $row['id'];
        $patient->first_name = $row['first_name'];
        $patient->last_name = $row['last_name'];
        $patient->telephone = $row['telephone'];
        $patient->email = $row['email'];
        $patient->password = $row['password'];
        return $patient;
    }

    public function register ($first_name, $last_name, $telephone, $email, $password): bool {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO patients (first_name, last_name, telephone, email, password)
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->getConnection()->prepare($sql);

        if ($stmt->execute([$this->first_name, $this->last_name, $this->telephone, $this->email, $this->password])) {
            return true;
        } else {
            return false;
        }
    }

    public static function findByEmail($email): ?self {
        $db = Database::getInstance();
        $sql = "SELECT * FROM patients WHERE email = ?";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->execute([$email]);
        
        $row = $stmt->fetch();

        return $row ? self::fromDatabase($row) : null;
    }
}
