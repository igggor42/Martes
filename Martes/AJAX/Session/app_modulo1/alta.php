<?php
include_once 'manejoDeSesion.inc.php';

header('Content-Type: application/json');
$respuesta = ['status' => 'error', 'mensaje' => 'Error desconocido.'];

//detecta nombres de la base
try {
    $colsStmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'MovimientosDeStock'");
    $colsStmt->execute();
    $colsList = $colsStmt->fetchAll(PDO::FETCH_COLUMN);
    
    $fechaCol = in_array('fecha_Movimiento', $colsList, true) ? 'fecha_Movimiento' : (in_array('FechaMovimiento', $colsList, true) ? 'FechaMovimiento' : 'fecha_Movimiento');
    $unidadCol = in_array('Unidad_medida', $colsList, true) ? 'Unidad_medida' : (in_array('UnidadMedida', $colsList, true) ? 'UnidadMedida' : 'Unidad_medida');
    $hasTipodeMov = in_array('TipodeMov', $colsList, true);
    $hasIdMov = in_array('IdMov', $colsList, true);
    $tipoMovCol = $hasTipodeMov ? 'TipodeMov' : ($hasIdMov ? 'IdMov' : 'TipodeMov');
    
    if (!$hasTipodeMov && !$hasIdMov) {
        throw new Exception('No se encontró columna TipodeMov ni IdMov en la tabla MovimientosDeStock');
    }
    
    $pdo->beginTransaction();

    $foto_blob = null;
    $foto_mime = null;
    $mensaje_foto = "No se subió foto.";

    if (isset($_FILES['FotoArticulo']) && $_FILES['FotoArticulo']['error'] == 0) {
        $foto_temp = $_FILES['FotoArticulo']['tmp_name'];
        $foto_blob = file_get_contents($foto_temp);
        //detecta mimetype
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $foto_mime = finfo_file($finfo, $foto_temp);
            finfo_close($finfo);
        } else {
            $foto_mime = $_FILES['FotoArticulo']['type'] ?? null;
        }
        $mensaje_foto = "Foto recibida (mime: $foto_mime)";
    }

    $tipoMovValue = null;
    if ($hasTipodeMov) {
        //usa el codigo solo si es la columna TipodeMov
        $tipoMovValue = $_POST['TipodeMov'] ?? '';
    } else if ($hasIdMov) {
        //convierte el codigo a IdMov si es necesario
        $codigoRecibido = $_POST['TipodeMov'] ?? '';
        if (!empty($codigoRecibido)) {
            //busca el IdMov correspondiente al codigo
            $stmtTipo = $pdo->prepare("SELECT IdMov FROM TipoDeMov WHERE Codigo = ?");
            $stmtTipo->execute([$codigoRecibido]);
            $tipoRow = $stmtTipo->fetch(PDO::FETCH_ASSOC);
            if ($tipoRow) {
                $tipoMovValue = $tipoRow['IdMov'];
            } else {
                throw new Exception("No se encontró el tipo de movimiento con código: $codigoRecibido");
            }
        } else {
            throw new Exception("TipodeMov es requerido");
        }
    }

    $sql = "INSERT INTO MovimientosDeStock 
                (Descripcion, NroDeLote, $fechaCol, $tipoMovCol, $unidadCol, Cantidad, FotoArticulo, FotoMime) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['Descripcion'],
        $_POST['NroDeLote'] ?? null,
        $_POST['fecha_Movimiento'] ?? $_POST['FechaMovimiento'] ?? '',
        $tipoMovValue,
        $_POST['Unidad_medida'] ?? $_POST['UnidadMedida'] ?? null,
        $_POST['Cantidad'],
        $foto_blob,
        $foto_mime
    ]);

    $nuevoId = $pdo->lastInsertId();

    $pdo->commit();
    $respuesta['status'] = 'exito';
    $respuesta['mensaje'] = "Alta exitosa. Id generado: $nuevoId";
    $respuesta['debug_foto'] = $mensaje_foto;
    $respuesta['nuevo_id'] = $nuevoId;
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