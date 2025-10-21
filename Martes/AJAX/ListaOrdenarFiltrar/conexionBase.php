<?php
// Database configuration for MySQL
$dbname = getenv('DB_NAME') ?: "stock_db"; 
$host = getenv('DB_HOST') ?: "mysql"; 
$user = getenv('DB_USER') ?: "stock_user"; 
$password = getenv('DB_PASSWORD') ?: "stock_password"; 
$port = "3306";
