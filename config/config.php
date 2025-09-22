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