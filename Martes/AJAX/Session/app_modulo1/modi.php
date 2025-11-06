<?php
include_once 'manejoDeSesion.inc.php';

header('Content-Type: application/json');
$respuesta = ['status' => 'error', 'mensaje' => 'Error desconocido.'];

try {
    $pdo->beginTransaction();

    $foto_nombre_db = null;
    $upload_dir = __DIR__ . '/uploads/';
    $mensaje_foto = "No se subió foto nueva.";

    if (isset($_FILES['FotoArticulo']) && $_FILES['FotoArticulo']['error'] == 0) {
        $foto_temp = $_FILES['FotoArticulo']['tmp_name'];
        $foto_nombre_db = uniqid() . '-' . basename($_FILES['FotoArticulo']['name']);
        $ruta_destino = $upload_dir . $foto_nombre_db;

        if (move_uploaded_file($foto_temp, $ruta_destino)) {
            $mensaje_foto = "Foto nueva subida como: " . $foto_nombre_db;
        } else {
            $mensaje_foto = "Error al mover el archivo subido.";
            $foto_nombre_db = null;
        }
    }

    if ($foto_nombre_db) {
        $sql = "UPDATE MovimientosDeStock SET 
                    Descripcion = ?, FechaMovimiento = ?, UnidadMedida = ?, Cantidad = ?, FotoArticulo = ?
                WHERE IdMov = ? AND CodArticulo = ? AND NroDeLote = ?";
        
        $params = [
            $_POST['Descripcion'], $_POST['FechaMovimiento'], $_POST['UnidadMedida'],
            $_POST['Cantidad'], $foto_nombre_db,
            $_POST['IdMov_original'], $_POST['CodArticulo_original'], $_POST['NroDeLote_original']
        ];
    } else {
        $sql = "UPDATE MovimientosDeStock SET 
                    Descripcion = ?, FechaMovimiento = ?, UnidadMedida = ?, Cantidad = ?
                WHERE IdMov = ? AND CodArticulo = ? AND NroDeLote = ?";

        $params = [
            $_POST['Descripcion'], $_POST['FechaMovimiento'], $_POST['UnidadMedida'],
            $_POST['Cantidad'],
            $_POST['IdMov_original'], $_POST['CodArticulo_original'], $_POST['NroDeLote_original']
        ];
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $filas_afectadas = $stmt->rowCount();

    $pdo->commit();
    $respuesta['status'] = 'exito';
    $respuesta['mensaje'] = "Modificación exitosa. Filas afectadas: " . $filas_afectadas;
    $respuesta['debug_foto'] = $mensaje_foto;
    $respuesta['datos_recibidos'] = $_POST;

} catch (PDOException $e) {
    $pdo->rollBack();
    $respuesta['mensaje'] = "Error de Base de Datos: " . $e->getMessage();
} catch (Exception $e) {
    $pdo->rollBack();
    $respuesta['mensaje'] = "Error general: " . $e->getMessage();
}

echo json_encode($respuesta);
?>