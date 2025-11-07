<?php
$host = "sql300.infinityfree.com";
$dbname = "if0_40353352_datos";
$usuario = "if0_40353352";
$clave = "lfnNc871T8AnXD";
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
     echo json_encode(['error' => $e->getMessage()]);
     exit;
}
?>
