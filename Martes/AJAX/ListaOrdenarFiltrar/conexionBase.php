<?php
// Database configuration for MySQL
$dbname = getenv('DB_NAME') ?: "stock_db"; 
$host = getenv('DB_HOST') ?: "mysql"; 
$user = getenv('DB_USER') ?: "stock_user"; 
$password = getenv('DB_PASSWORD') ?: "stock_password"; 
$port = "3306";

// Debug logging for troubleshooting
error_log("=== DATABASE CONNECTION DEBUG ===");
error_log("DB_HOST: " . $host);
error_log("DB_NAME: " . $dbname);
error_log("DB_USER: " . $user);
error_log("DB_PORT: " . $port);
error_log("DSN: mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4");
error_log("=================================");
