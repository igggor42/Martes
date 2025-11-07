<?php

$host = "localhost";
$dbname = "igor_stock";
$usuario = "root";
$clave = "";

global $dbh, $pdo;
try {
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 60,
        PDO::ATTR_PERSISTENT => true
    );
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $dbh = new PDO($dsn, $usuario, $clave, $options);
    $pdo = $dbh;
} catch (PDOException $e) {
    error_log('Error conexión BD: ' . $e->getMessage());
}
?>