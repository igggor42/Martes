<?php
include_once __DIR__ . '/../datos_conexion_a_la_base.php';
header('Content-Type: application/json');

try {
    // Use the current connection ($pdo)
    $sql = "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_TYPE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'MovimientosDeStock'
            ORDER BY ORDINAL_POSITION";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cols)) {
        $dbn = isset($dbname) ? $dbname : '(unknown)';
        echo json_encode(["error" => "Table MovimientosDeStock not found in database " . $dbn]);
        exit;
    }

    // Quick boolean for TipodeMov presence
    $hasTipodeMov = false;
    foreach ($cols as $c) {
        if (strcasecmp($c['COLUMN_NAME'], 'TipodeMov') === 0) {
            $hasTipodeMov = true;
            break;
        }
    }

    echo json_encode(["columns" => $cols, "hasTipodeMov" => $hasTipodeMov]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
