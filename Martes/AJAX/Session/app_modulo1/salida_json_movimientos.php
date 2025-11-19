<?php
include_once 'manejoDeSesion.inc.php';
header('Content-Type: application/json');

$colsStmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'MovimientosDeStock'");
$colsStmt->execute();
$colsList = $colsStmt->fetchAll(PDO::FETCH_COLUMN);

$hasTipodeMov = in_array('TipodeMov', $colsList, true);
$hasIdMov = in_array('IdMov', $colsList, true);
$fechaCol = in_array('fecha_Movimiento', $colsList, true) ? 'fecha_Movimiento' : (in_array('FechaMovimiento', $colsList, true) ? 'FechaMovimiento' : null);
$unidadCol = in_array('Unidad_medida', $colsList, true) ? 'Unidad_medida' : (in_array('UnidadMedida', $colsList, true) ? 'UnidadMedida' : null);


if ($hasTipodeMov) {
    $join = "LEFT JOIN TipoDeMov t ON m.TipodeMov = t.Codigo";
    $tipoField = 'TipodeMov';
    //filtra segun la columna de la tabla MovimientosDeStock
    $tipoFilterOn = 'm.TipodeMov';
} elseif ($hasIdMov) {
    $join = "LEFT JOIN TipoDeMov t ON m.IdMov = t.IdMov";
    $tipoField = 'IdMov';
    $tipoFilterOn = 't.Codigo';
} else {
    $join = '';
    $tipoField = null;
    $tipoFilterOn = null;
}
//crea una consulta capaz de añadirle filtros
$sql = "SELECT m.*";
$sql .= ", t.Descripcion as TipoMovDescripcion, t.Codigo as TipoMovCodigo";
$sql .= " FROM MovimientosDeStock m ";
$sql .= $join . " WHERE 1=1";
$params = [];

//aplica los filtros
if (!empty($_GET['cod'])) {
    $sql .= " AND m.CodArticulo LIKE ?";
    $params[] = '%' . trim($_GET['cod']) . '%';
}
if (!empty($_GET['desc'])) {
    $sql .= " AND m.Descripcion LIKE ?";
    $params[] = '%' . trim($_GET['desc']) . '%';
}
if (!empty($_GET['lote'])) {
    $sql .= " AND m.NroDeLote LIKE ?";
    $params[] = '%' . trim($_GET['lote']) . '%';
}
if (!empty($_GET['tipo_mov']) && $tipoFilterOn !== null) {
    $sql .= " AND $tipoFilterOn = ?";
    $params[] = trim($_GET['tipo_mov']);
}
//ordena
$orderBy = $fechaCol !== null ? "m.$fechaCol" : 'm.CodArticulo';
$sql .= " ORDER BY $orderBy DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //convierte los datos de foto a data URI (base64) para la entrega JSON
    foreach ($movimientos as &$m) {
        if (!empty($m['FotoArticulo'])) {
            $mime = !empty($m['FotoMime']) ? $m['FotoMime'] : 'image/jpeg';
            $base = base64_encode($m['FotoArticulo']);
            $m['FotoDataURI'] = "data:$mime;base64,$base";
            $m['tiene_foto'] = 'SI';
            unset($m['FotoArticulo']);
        } else {
            $m['FotoDataURI'] = null;
            $m['tiene_foto'] = null;
        }
        if ($fechaCol !== null && isset($m[$fechaCol])) {
            $m['fecha_Movimiento'] = $m[$fechaCol];
        } else {
            $m['fecha_Movimiento'] = isset($m['FechaMovimiento']) ? $m['FechaMovimiento'] : null;
        }

        if ($tipoField !== null && isset($m[$tipoField])) {
            $m['TipodeMov'] = $m[$tipoField];

            if ($tipoField === 'IdMov' && isset($m['TipoMovCodigo'])) {
                $m['TipodeMov'] = $m['TipoMovCodigo'];
            }
        } elseif (isset($m['TipodeMov'])) {
        } else {
            $m['TipodeMov'] = null;
        }
        
        //asegura agarrar el codigo y no el IdMov
        if (isset($m['TipoMovCodigo']) && !empty($m['TipoMovCodigo'])) {
            $m['TipodeMov'] = $m['TipoMovCodigo'];
        }

        if ($unidadCol !== null && isset($m[$unidadCol])) {
            $m['Unidad_medida'] = $m[$unidadCol];
        } else {
            $m['Unidad_medida'] = isset($m['UnidadMedida']) ? $m['UnidadMedida'] : null;
        }
    }

    echo json_encode($movimientos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
