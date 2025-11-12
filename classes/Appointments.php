<?php

require_once __DIR__ . '/../autoload.php';

class Appointments {
    private int $appointment_id;
    private int $patient_id;
    private string $full_name;
    private string $appointment_type;
    private \DateTimeInterface $appointment_date;
    private \DateTimeInterface $appointment_time;
    private ?string $additional_notes;
    private string $blood_group;
    private bool $is_completed;

    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    private static function fromDatabase(array $row): self {
        $appointment = new self();
        $appointment->appointment_id = $row['appointment_id'];
        $appointment->patient_id = $row['patient_id'];
        $appointment->full_name = $row['full_name'];
        $appointment->appointment_type = $row['appointment_type'];
        $appointment->appointment_date = new DateTime($row['schedule_day']);
        $appointment->appointment_time = new DateTime($row['schedule_time']);
        $appointment->additional_notes = $row['additional_notes'];
        $appointment->blood_group = $row['blood_group'];
        $appointment->is_completed = (bool)$row['is_completed'];
        return $appointment;
    }

    public function scheduleAppointment($patient_id, $full_name, $appointment_type, $appointment_date, $appointment_time, $blood_group, $additional_notes = null): bool {
        $this->patient_id = $patient_id;
        $this->full_name = $full_name;
        $this->appointment_type = $appointment_type;
        $this->appointment_date = new DateTime($appointment_date);
        
        // Parse time from H:i format (e.g., "09:00") to H:i:s
        $time = DateTime::createFromFormat('H:i', $appointment_time);
        if (!$time) {
            $time = DateTime::createFromFormat('H:i:s', $appointment_time);
        }
        if (!$time) {
            throw new \InvalidArgumentException('Invalid time format');
        }
        $this->appointment_time = $time;
        
        $this->blood_group = $blood_group;
        $this->additional_notes = $additional_notes;
        $this->is_completed = false;

        // Default value for is_completed is automatically set to false in the database
        $sql = "INSERT INTO appointments (patient_id, full_name, appointment_type, schedule_day, schedule_time, blood_group, additional_notes)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->getConnection()->prepare($sql);

        return $stmt->execute([
            $this->patient_id,
            $this->full_name,
            $this->appointment_type,
            $this->appointment_date->format('Y-m-d'),
            $this->appointment_time->format('H:i:s'),
            $this->blood_group,
            $this->additional_notes
        ]);
    }

    public static function getAllAppointments(): array {
        $db = Database::getInstance();
        $sql = "SELECT * FROM appointments ORDER BY schedule_day, schedule_time";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function markAsCompleted($appointment_id): bool {
        $db = Database::getInstance();
        $sql = "UPDATE appointments SET is_completed = true WHERE appointment_id = ?";
        $stmt = $db->getConnection()->prepare($sql);
        return $stmt->execute([$appointment_id]);
    }

    public static function getAppointmentsByPatientId($patient_id): array {
        $db = Database::getInstance();
        $sql = "SELECT * FROM appointments WHERE patient_id = ? ORDER BY schedule_day DESC, schedule_time DESC";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->execute([$patient_id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function checkDuplicateAppointment($patient_id, $appointment_date): bool {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM appointments 
                WHERE patient_id = ? AND schedule_day = ? AND is_completed = FALSE";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->execute([$patient_id, $appointment_date]);
        
        $result = $stmt->fetch();
        return (int)$result['count'] > 0;
    }

    public static function getLastCompletedAppointment($patient_id): ?string {
        $db = Database::getInstance();
        $sql = "SELECT MAX(schedule_day) as last_appointment 
                FROM appointments 
                WHERE patient_id = ? AND is_completed = TRUE";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->execute([$patient_id]);
        
        $result = $stmt->fetch();
        return $result['last_appointment'];
    }
}
