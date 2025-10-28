<?php
// Database configuration for PostgreSQL
$dbname = getenv('DB_NAME') ?: "stock_db"; 
$host = getenv('DB_HOST') ?: "postgres"; 
$user = getenv('DB_USER') ?: "stock_user"; 
$password = getenv('DB_PASSWORD') ?: "stock_password"; 
$port = getenv('DB_PORT') ?: "5432";
