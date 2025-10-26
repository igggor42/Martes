<?php
include './datosConexionBase.php';

$codArt = $_POST['codArt']; 
$respuesta_estado = "";

try {
    $sql = "select documentoPdf from articulos where codArt = :codArt"; 
    
    $stmt = $dbh->prepare($sql);
    $respuesta_estado .= "\n<br />preparacion exitosa"; 
    
    $stmt->bindParam(':codArt', $codArt);
    $respuesta_estado .= "\n<br />vinculación exitosa";
    
    $stmt->execute(); 
    $respuesta_estado .= "\n<br />ejecución exitosa";

    $fila = $stmt->fetch(PDO::FETCH_ASSOC); 
    
    $objArticulo = new stdClass();
    if ($fila && $fila['documentoPdf']) {
        $objArticulo->documentoPdf = base64_encode($fila['documentoPdf']); 
    } else {
        $objArticulo->documentoPdf = null;
    }
    
    $salidaJson = json_encode($objArticulo, JSON_INVALID_UTF8_SUBSTITUTE);
    
    $dbh = null; 
    echo $salidaJson;
    
} catch (PDOException $e) {
    $respuesta_estado .= "\n<br />" . $e->getMessage();
    $dbh = null;
    echo $respuesta_estado;
}
?>