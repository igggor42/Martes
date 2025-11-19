<?php
include_once 'manejoDeSesion.inc.php';

header('Content-Type: application/json');

//buscar lista de tipos de movimientos
try {
    $stmt = $pdo->query("SELECT IdMov, Codigo, Descripcion FROM TipoDeMov ORDER BY Descripcion");
    $tipos = $stmt->fetchAll();
    echo json_encode($tipos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
