<?php

require_once __DIR__ . "/../autoload.php";

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

    public static function findId($email): int {
        $db = Database::getInstance();
        $sql = "SELECT * FROM patients WHERE email = ?";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->execute([$email]);
        
        $row = $stmt->fetch();

        $fetchedId = (int) $row['id'];

        return $fetchedId;
    }

    public function verifyPassword($password): bool {
        if (password_verify($password, $this->password)) {
            return true;
        }
        return false;
    }

    public static function markAsVerified($patientId): bool {
        $db = Database::getInstance();
        $sql = "UPDATE patients SET is_verified = TRUE, verified_at = NOW() WHERE id = ?";
        $stmt = $db->getConnection()->prepare($sql);
        return $stmt->execute([$patientId]);
    }

    // Getters

    public function getId() {
        return $this->id;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function getTelephone() {
        return $this->telephone;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }
}
