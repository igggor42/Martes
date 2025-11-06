<?php
$host = "127.0.0.1";
$dbname = "tu_base_de_datos"; // <-- REEMPLAZA ESTO
$usuario = "root";             // <-- REEMPLAZA ESTO
$clave = "";                   // <-- REEMPLAZA ESTO
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$opciones = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $usuario, $clave, $opciones);
} catch (\PDOException $e) {
     http_response_code(500);
     echo json_encode(['error' => 'Error de conexión a la base de datos.']);
     exit;
}
?>