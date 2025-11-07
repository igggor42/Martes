<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

ob_start();

include_once 'manejoDeSesion.inc.php';
header('Content-Type: application/json');
$respuesta = ['status' => 'error', 'mensaje' => 'Error desconocido.'];

try {
    if (!isset($pdo) || !$pdo) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
    
    //detecta nombres reales de columnas en la base
    $colsStmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'MovimientosDeStock'");
    $colsStmt->execute();
    $colsList = $colsStmt->fetchAll(PDO::FETCH_COLUMN);
    
    //columna de fecha
    $fechaCol = in_array('fecha_Movimiento', $colsList, true) ? 'fecha_Movimiento' : (in_array('FechaMovimiento', $colsList, true) ? 'FechaMovimiento' : 'fecha_Movimiento');
    //columna de unidad
    $unidadCol = in_array('Unidad_medida', $colsList, true) ? 'Unidad_medida' : (in_array('UnidadMedida', $colsList, true) ? 'UnidadMedida' : 'Unidad_medida');
    //columna de tipo de movimiento
    $hasTipodeMov = in_array('TipodeMov', $colsList, true);
    $hasIdMov = in_array('IdMov', $colsList, true);
    $tipoMovCol = $hasTipodeMov ? 'TipodeMov' : ($hasIdMov ? 'IdMov' : 'TipodeMov');
    
    if (!$hasTipodeMov && !$hasIdMov) {
        throw new Exception('No se encontró columna TipodeMov ni IdMov en la tabla MovimientosDeStock');
    }
    
    $pdo->beginTransaction();

    $foto_blob = null;
    $foto_mime = null;
    $mensaje_foto = "No se subió foto nueva.";

    if (isset($_FILES['FotoArticulo']) && $_FILES['FotoArticulo']['error'] == 0) {
        $foto_temp = $_FILES['FotoArticulo']['tmp_name'];
        $foto_blob = file_get_contents($foto_temp);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $foto_mime = finfo_file($finfo, $foto_temp);
        finfo_close($finfo);
        $mensaje_foto = "Foto nueva recibida (mime: $foto_mime)";
    }

    if (!isset($_POST['CodArticulo_original']) || empty($_POST['CodArticulo_original'])) {
        throw new Exception('CodArticulo_original es requerido');
    }
    
    if (!isset($_POST['Descripcion']) || empty($_POST['Descripcion'])) {
        throw new Exception('Descripcion es requerido');
    }
    
    $tipoMovValue = null;
    if ($hasTipodeMov) {
        $tipoMovValue = $_POST['TipodeMov'] ?? '';
        if (empty($tipoMovValue)) {
            $stmtGet = $pdo->prepare("SELECT TipodeMov FROM MovimientosDeStock WHERE CodArticulo = ?");
            $stmtGet->execute([$_POST['CodArticulo_original']]);
            $rowGet = $stmtGet->fetch(PDO::FETCH_ASSOC);
            if ($rowGet) {
                $tipoMovValue = $rowGet['TipodeMov'];
            }
        }
    } else if ($hasIdMov) {
        // Si la columna es IdMov se cambia el codigo por IdMov
        $codigoRecibido = $_POST['TipodeMov'] ?? '';
        if (empty($codigoRecibido)) {
            // Si está vacio se fija en el registro del principio
            $stmtGet = $pdo->prepare("SELECT IdMov FROM MovimientosDeStock WHERE CodArticulo = ?");
            $stmtGet->execute([$_POST['CodArticulo_original']]);
            $rowGet = $stmtGet->fetch(PDO::FETCH_ASSOC);
            if ($rowGet) {
                $tipoMovValue = $rowGet['IdMov'];
            }
        } else {
            //busca el IdMov segun el codigo
            $stmtTipo = $pdo->prepare("SELECT IdMov FROM TipoDeMov WHERE Codigo = ?");
            $stmtTipo->execute([$codigoRecibido]);
            $tipoRow = $stmtTipo->fetch(PDO::FETCH_ASSOC);
            if ($tipoRow) {
                $tipoMovValue = $tipoRow['IdMov'];
            } else {
                throw new Exception("No se encontró el tipo de movimiento con código: $codigoRecibido");
            }
        }
    }
    
    if ($tipoMovValue === null || $tipoMovValue === '') {
        throw new Exception("TipodeMov es requerido y no se pudo obtener del registro");
    }
    


    $nroDeLote = $_POST['NroDeLote'] ?? '';
    if ($foto_blob !== null) {
        //si se sube foto nueva se actualiza
        $sql = "UPDATE MovimientosDeStock SET 
                    Descripcion = ?, NroDeLote = ?, $fechaCol = ?, $unidadCol = ?, Cantidad = ?, FotoArticulo = ?, FotoMime = ?, $tipoMovCol = ?
                WHERE CodArticulo = ?";
        
        $params = [
            $_POST['Descripcion'] ?? '',
            $nroDeLote,
            $_POST['fecha_Movimiento'] ?? $_POST['FechaMovimiento'] ?? '',
            $_POST['Unidad_medida'] ?? $_POST['UnidadMedida'] ?? '',
            $_POST['Cantidad'] ?? 0,
            $foto_blob,
            $foto_mime,
            $tipoMovValue,
            $_POST['CodArticulo_original']
        ];
    } else {
        $sql = "UPDATE MovimientosDeStock SET 
                    Descripcion = ?, NroDeLote = ?, $fechaCol = ?, $unidadCol = ?, Cantidad = ?, $tipoMovCol = ?
                WHERE CodArticulo = ?";

        $params = [
            $_POST['Descripcion'] ?? '',
            $nroDeLote,
            $_POST['fecha_Movimiento'] ?? $_POST['FechaMovimiento'] ?? '',
            $_POST['Unidad_medida'] ?? $_POST['UnidadMedida'] ?? '',
            $_POST['Cantidad'] ?? 0,
            $tipoMovValue,
            $_POST['CodArticulo_original']
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
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $respuesta['mensaje'] = "Error de Base de Datos: " . $e->getMessage();
    $respuesta['error_code'] = $e->getCode();
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $respuesta['mensaje'] = "Error general: " . $e->getMessage();
}

//se fija que no haya output
if (ob_get_level()) {
    ob_clean();
}
echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
exit;
?>