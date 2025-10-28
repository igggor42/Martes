<?php


include 'conexionBase.php'; 
$tiposMov = [];
$salidaJson = json_encode(['error' => 'Error de inicio de solicitud.']);

try {
    // DSN actualizado para PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Consulta SQL (es estándar, no necesita cambios)
    $sql = "SELECT IdMov, Descripcion FROM TipoDeMov ORDER BY Descripcion"; 
    
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    while ($fila = $stmt->fetch()) {
    $objTipo = new stdClass();
    $objTipo->IdMov = $fila['idmov'];
    $objTipo->Descripcion = $fila['descripcion'];
    $tiposMov[] = $objTipo;
    }

    $objSalida = new stdClass();
    $objSalida->TipoDeMov = $tiposMov;
    
    $salidaJson = json_encode($objSalida);
    
    $dbh = null;

} catch (PDOException $e) {
    $log_errores = date("Y-m-d H:i") . " " . "Error al cargar tipos de movimiento: "." [DSN: $dsn] ".$e->getMessage() . "\n";
    
    $puntero = fopen("./errores.log", "a");
    fwrite($puntero, $log_errores);
    fclose($puntero);
    
    $salidaJson = json_encode(['error' => 'Error al cargar tipos de movimiento.']);
}

//Enviamos el JSON de tipos de movimiento
header('Content-Type: application/json');
echo $salidaJson;


?>

