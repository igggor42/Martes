<?php
include_once 'manejoDeSesion.inc.php';

header('Content-Type: application/json');

$sql = "SELECT m.*, t.Descripcion as TipoMovDescripcion 
        FROM MovimientosDeStock m
        JOIN TipoDeMov t ON m.IdMov = t.IdMov
        WHERE 1=1";
$params = [];

if (!empty($_GET['cod'])) {
    $sql .= " AND m.CodArticulo LIKE ?";
    $params[] = '%' . $_GET['cod'] . '%';
}
if (!empty($_GET['desc'])) {
    $sql .= " AND m.Descripcion LIKE ?";
    $params[] = '%' . $_GET['desc'] . '%';
}
if (!empty($_GET['lote'])) {
    $sql .= " AND m.NroDeLote LIKE ?";
    $params[] = '%' . $_GET['lote'] . '%';
}
if (!empty($_GET['tipo_mov'])) {
    $sql .= " AND t.Codigo = ?";
    $params[] = $_GET['tipo_mov'];
}

$sql .= " ORDER BY m.FechaMovimiento DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $movimientos = $stmt->fetchAll();
    echo json_encode($movimientos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>