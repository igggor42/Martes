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
    // Alias para compatibilidad: algunos archivos usan $pdo, otros $dbh
    $pdo = $dbh;
    // No imprimir en pantalla (evita romper respuestas JSON)
    // error_log('Conexión a la base de datos establecida.');
} catch (PDOException $e) {
    // Registrar el error en el log y no imprimir HTML
    error_log('Error conexión BD: ' . $e->getMessage());
    // Para desarrollo, podríamos mostrar el mensaje; en producción es mejor ocultarlo
    // echo "<h2 style='color:red'>❌ Error: " . $e->getMessage() . "</h2>";
}
?>