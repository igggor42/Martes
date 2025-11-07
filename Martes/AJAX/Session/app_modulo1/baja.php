<?php
include_once 'manejoDeSesion.inc.php';

header('Content-Type: application/json');
$respuesta = ['status' => 'error', 'mensaje' => 'Error desconocido.'];

// Ahora la clave primaria es `CodArticulo` (autoincremental)
$codArticulo = $_POST['CodArticulo'] ?? null;

if (!$codArticulo) {
    http_response_code(400);
    $respuesta['mensaje'] = 'Falta CodArticulo para la baja.';
    echo json_encode($respuesta);
    exit;
}

try {
    $pdo->beginTransaction();

    $sql = "DELETE FROM MovimientosDeStock WHERE CodArticulo = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$codArticulo]);
    $filas_afectadas = $stmt->rowCount();

    $pdo->commit();

    if ($filas_afectadas > 0) {
        $respuesta['status'] = 'exito';
        $respuesta['mensaje'] = "Baja exitosa.";
        $respuesta['claves_borradas'] = ['CodArticulo' => $codArticulo];
    } else {
        $respuesta['mensaje'] = "No se encontró el registro para borrar.";
    }

} catch (PDOException $e) {
    $pdo->rollBack();
    $respuesta['mensaje'] = "Error de Base de Datos: " . $e->getMessage();
}

echo json_encode($respuesta);
?>