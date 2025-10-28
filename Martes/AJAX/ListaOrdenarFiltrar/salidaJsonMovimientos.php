<?php
include 'conexionBase.php'; 

$orden = isset($_POST['orden']) ? $_POST['orden'] : 'CodArticulo'; 
$f_mov_codArt = isset($_POST['f_mov_codArt']) ? $_POST['f_mov_codArt'] : '';
$f_mov_tipoMov = isset($_POST['f_mov_tipoMov']) ? $_POST['f_mov_tipoMov'] : '';
$f_mov_nroLote = isset($_POST['f_mov_nroLote']) ? $_POST['f_mov_nroLote'] : '';
$f_mov_descripcion = isset($_POST['f_mov_descripcion']) ? $_POST['f_mov_descripcion'] : '';
$f_mov_fecha = isset($_POST['f_mov_fecha']) ? $_POST['f_mov_fecha'] : '';
$f_mov_um = isset($_POST['f_mov_um']) ? $_POST['f_mov_um'] : '';

$log_errores = '';
$salidaJson = json_encode(['error' => 'Error de inicio de solicitud.']);

//Conexion PDO
try {
    // DSN actualizado para PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $sql = "SELECT 
                M.CodArticulo, M.Descripcion, M.NroDeLote, M.FechaMovimiento, 
                M.UnidadMedida, M.Cantidad, M.FotoArticulo, T.Descripcion AS TipoMovimiento
            FROM 
                MovimientosDeStock M
            JOIN
                TipoDeMov T ON M.IdMov = T.IdMov
            WHERE 1=1";           
    $parametros = [];

 
    if (!empty($f_mov_codArt)) {
        // Sintaxis de LIKE cambiada para PostgreSQL
        $sql .= " AND M.CodArticulo LIKE '%' || :codArt || '%'";
        $parametros[':codArt'] = $f_mov_codArt;
    }
    if (!empty($f_mov_tipoMov)) {
        $sql .= " AND M.IdMov = :idMov";
        $parametros[':idMov'] = $f_mov_tipoMov; 
    }
    if (!empty($f_mov_nroLote)) {
        // Sintaxis de LIKE cambiada para PostgreSQL
        $sql .= " AND M.NroDeLote LIKE '%' || :nroLote || '%'";
        $parametros[':nroLote'] = $f_mov_nroLote;
    }
    if (!empty($f_mov_descripcion)) {
        // Sintaxis de LIKE cambiada para PostgreSQL
        $sql .= " AND M.Descripcion LIKE '%' || :descripcion || '%'";
        $parametros[':descripcion'] = $f_mov_descripcion;
    }
    if (!empty($f_mov_fecha)) {
        // Sintaxis de LIKE cambiada para PostgreSQL (Nota: LIKE en un campo DATE puede ser problemático)
        $sql .= " AND CAST(M.FechaMovimiento AS TEXT) LIKE '%' || :fecha || '%'";
        $parametros[':fecha'] = $f_mov_fecha;
    }
    if (!empty($f_mov_um)) {
        // Sintaxis de LIKE cambiada para PostgreSQL
        $sql .= " AND M.UnidadMedida LIKE '%' || :um || '%'";
        $parametros[':um'] = $f_mov_um;
    }
    
    // El ordenamiento es estándar, no necesita cambios
    $sql .= " ORDER BY M.$orden";



    $stmt = $dbh->prepare($sql);

    foreach ($parametros as $param => $valor) {
        $stmt->bindValue($param, $valor); 
    }
    
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    //Respuesta
    $movimientos = [];
    
    while ($fila = $stmt->fetch()) {
    $objMov = new stdClass();
    $objMov->CodArticulo = $fila['codarticulo'];
    $objMov->Descripcion = $fila['descripcion'];
    $objMov->NroDeLote = $fila['nrodelote'];
    $objMov->FechaMovimiento = $fila['fechamovimiento'];
    $objMov->UnidadMedida = $fila['unidadmedida'];
    $objMov->Cantidad = $fila['cantidad'];
    $objMov->FotoArticulo = $fila['fotoarticulo'];
    // El alias 'AS TipoMovimiento' también se convierte a minúsculas
    $objMov->TipoMovimiento = $fila['tipomovimiento']; 

    array_push($movimientos, $objMov);
    }
    
    $objSalida = new stdClass();
    $objSalida->MovimientosDeStock = $movimientos;
    $objSalida->cuenta = count($movimientos);
    
    $salidaJson = json_encode($objSalida);

    $dbh = null; 

} catch (PDOException $e) {
    $log_errores = date("Y-m-d H:i") . " " . "Error en la conexión o consulta: " . $e->getMessage() . "\n";
    $salidaJson = json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]); 

    $puntero = fopen("./errores.log", "a");
    fwrite($puntero, $log_errores);
    fclose($puntero);
}

//Enviamos la respuesta
header('Content-Type: application/json');
echo $salidaJson;

?>

