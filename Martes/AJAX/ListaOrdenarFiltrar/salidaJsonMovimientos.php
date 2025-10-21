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
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;client_encoding=UTF8";
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
        $sql .= " AND M.CodArticulo LIKE CONCAT('%', :codArt, '%')";
        $parametros[':codArt'] = $f_mov_codArt;
    }
    if (!empty($f_mov_tipoMov)) {
        $sql .= " AND M.IdMov = :idMov";
        $parametros[':idMov'] = $f_mov_tipoMov; 
    }
    if (!empty($f_mov_nroLote)) {
        $sql .= " AND M.NroDeLote LIKE CONCAT('%', :nroLote, '%')";
        $parametros[':nroLote'] = $f_mov_nroLote;
    }
    if (!empty($f_mov_descripcion)) {
        $sql .= " AND M.Descripcion LIKE CONCAT('%', :descripcion, '%')";
        $parametros[':descripcion'] = $f_mov_descripcion;
    }
    if (!empty($f_mov_fecha)) {
        $sql .= " AND M.FechaMovimiento LIKE CONCAT('%', :fecha, '%')";
        $parametros[':fecha'] = $f_mov_fecha;
    }
    if (!empty($f_mov_um)) {
        $sql .= " AND M.UnidadMedida LIKE CONCAT('%', :um, '%')";
        $parametros[':um'] = $f_mov_um;
    }
    
    //Agregamos el ordenamiento
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
        $objMov->CodArticulo = $fila['CodArticulo'];
        $objMov->Descripcion = $fila['Descripcion'];
        $objMov->NroDeLote = $fila['NroDeLote'];
        $objMov->FechaMovimiento = $fila['FechaMovimiento'];
        $objMov->UnidadMedida = $fila['UnidadMedida'];
        $objMov->Cantidad = $fila['Cantidad'];
        $objMov->FotoArticulo = $fila['FotoArticulo'];
        $objMov->TipoMovimiento = $fila['TipoMovimiento'];
        
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



