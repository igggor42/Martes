<?php
include_once 'manejoDeSesion.inc.php';

header('Content-Type: application/json');
$respuesta = ['status' => 'error', 'mensaje' => 'Error desconocido.'];

try {
    $pdo->beginTransaction();

    $foto_nombre_db = null;
    $upload_dir = __DIR__ . '/uploads/';
    $mensaje_foto = "No se subió foto.";

    if (isset($_FILES['FotoArticulo']) && $_FILES['FotoArticulo']['error'] == 0) {
        $foto_temp = $_FILES['FotoArticulo']['tmp_name'];
        $foto_nombre_db = uniqid() . '-' . basename($_FILES['FotoArticulo']['name']);
        $ruta_destino = $upload_dir . $foto_nombre_db;

        if (move_uploaded_file($foto_temp, $ruta_destino)) {
            $mensaje_foto = "Foto subida exitosamente como: " . $foto_nombre_db;
        } else {
            $mensaje_foto = "Error al mover el archivo subido.";
            $foto_nombre_db = null;
        }
    }

    $sql = "INSERT INTO MovimientosDeStock 
                (IdMov, CodArticulo, Descripcion, NroDeLote, FechaMovimiento, UnidadMedida, Cantidad, FotoArticulo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['IdMov'],
        $_POST['CodArticulo'],
        $_POST['Descripcion'],
        $_POST['NroDeLote'],
        $_POST['FechaMovimiento'],
        $_POST['UnidadMedida'],
        $_POST['Cantidad'],
        $foto_nombre_db
    ]);

    $pdo->commit();
    $respuesta['status'] = 'exito';
    $respuesta['mensaje'] = "Alta exitosa.";
    $respuesta['debug_foto'] = $mensaje_foto;
    $respuesta['datos_recibidos'] = $_POST;

} catch (PDOException $e) {
    $pdo->rollBack();
    $respuesta['mensaje'] = "Error de Base de Datos: " . $e->getMessage();
    if ($e->getCode() == 23000) {
        $respuesta['mensaje'] = "Error: Clave primaria duplicada. Ya existe un registro con ese Cod. Artículo, Nro. Lote y Tipo Movimiento.";
    }
} catch (Exception $e) {
    $pdo->rollBack();
    $respuesta['mensaje'] = "Error general: " . $e->getMessage();
}

echo json_encode($respuesta);
?>