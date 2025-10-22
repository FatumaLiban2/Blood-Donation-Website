<?php

// Load enviroment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

// Database configuration
define("DB_TYPE", $_ENV['DBTYPE']);
define("DB_HOST", $_ENV['PGHOST']);
define("DB_NAME", $_ENV['PGDATABASE']);
define("DB_USER", $_ENV['PGUSER']);
define("DB_PASSWORD", $_ENV['PGPASSWORD']);
define("DB_SSLMODE", $_ENV['PGSSLMODE']);
define("DB_CHANNELBINDING", $_ENV['PGCHANNELBINDING']);
define("DB_PORT", $_ENV['PGPORT']);

// PHP Mailer SMTP configuration
define("SMTP_HOST", $_ENV['SMTP_HOST']);
define("SMTP_USER", $_ENV['SMTP_USER']);
define("SMTP_PASSWORD", $_ENV['SMTP_PASSWORD']);
define("SMTP_PORT", $_ENV['SMTP_PORT']);
define("SMTP_ENCRYPTION", $_ENV['SMTP_ENCRYPTION']);

// Cryptographic keys
define("JWT_PRIVATE_KEY_ENCODED", $_ENV['JWT_PRIVATE_KEY_ENCODED']);
define("JWT_PUBLIC_KEY_ENCODED", $_ENV['JWT_PUBLIC_KEY_ENCODED']);