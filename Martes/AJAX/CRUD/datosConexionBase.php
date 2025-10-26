<?php
$hostname = "localhost";
$username = "usuario_bd";
$password = "clave_bd";
$dbname = "nombre_bd";

try {
    $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    $dbh = null;
    exit();
}

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>