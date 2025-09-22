<?php

class Database {
    private $db_type;
    private $db_host;
    private $db_name;
    private $db_user;
    private $db_password;
    private $db_sslmode;
    private $db_channelbinding;
    private $db_port;

    private $connection;

    public function __construct() {
        $this->db_type = DB_TYPE;
        $this->db_host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->db_user = DB_USER;
        $this->db_password = DB_PASSWORD;
        $this->db_sslmode = DB_SSLMODE;
        $this->db_channelbinding = DB_CHANNELBINDING;
        $this->db_port = DB_PORT;

        $this->connection = null;
    }

    protected function connect() {
        if ($this->connection === null) {
            try {
                $db_data = "$this->db_type:host={$this->db_host};dbname={$this->db_name};port={$this->db_port};sslmode={$this->db_sslmode};channel_binding={$this->db_channelbinding}";

                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                $this->connection = new PDO($db_data, $this->db_user, $this->db_password, $options);
                echo "Connected successfully";

            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
        }
        
        return $this->connection;
    }

}