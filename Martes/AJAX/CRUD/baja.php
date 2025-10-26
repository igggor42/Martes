<?php
include './datosConexionBase.php';

$codArt = $_POST['codArt']; 
$respuesta_estado = "";

try {
    $sql = "delete from articulos where codArt=:codArt;"; 
            
    $stmt = $dbh->prepare($sql);
    $respuesta_estado .= "\n<br />Preparación exitosa";
    
    $stmt->bindParam(':codArt', $codArt);
    $respuesta_estado .= "\n<br />Vinculación exitosa";
    
    $stmt->execute();
    $respuesta_estado .= "\n<br />Ejecución exitosa";

} catch (PDOException $e) {
    $respuesta_estado .= "\n<br />Conexión exitosa"; 
    $respuesta_estado .= "\n<br />" . $e->getMessage();
}

$dbh = null; 
echo $respuesta_estado; 
?>