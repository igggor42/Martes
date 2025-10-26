<?php
include './datosConexionBase.php';

$codArt = $_POST['codArt']; 
$familia = $_POST['familia']; 
$descripcion = $_POST['descripcion']; 
$um = $_POST['um']; 
$fechaAlta = $_POST['fechaAlta']; 
$saldoStock = $_POST['saldoStock']; 

$respuesta_estado = "\nRespuesta del servidor al alta. Entradas recibidas en el req http:"; 
$respuesta_estado .= "\n<br />codArt: " . $codArt; 
$respuesta_estado .= "\n<br />familia: " . $familia;

try {
    // Primera etapa: INSERT simple (sin binarios)
    $sql = "insert into articulos (codArt, familia, descripcion, um, fechaAlta, saldoStock)
            values (:codArt, :familia, :descripcion, :um, :fechaAlta, :saldoStock);";
            
    $stmt = $dbh->prepare($sql); 
    $respuesta_estado .= "\n<br />Preparación exitosa";
    
    $stmt->bindParam(':codArt', $codArt);
    $stmt->bindParam(':familia', $familia);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':um', $um);
    $stmt->bindParam(':fechaAlta', $fechaAlta);
    $stmt->bindParam(':saldoStock', $saldoStock);
    $respuesta_estado .= "\n<br />Vinculación exitosa";
    
    $stmt->execute(); 
    $respuesta_estado .= "\n<br />Ejecución exitosa";
    
} catch (PDOException $e) {
    $respuesta_estado .= "\n<br />Conexión exitosa"; 
    $respuesta_estado .= "\n<br />" . $e->getMessage();
}

// Segunda etapa: Modificación para el binario (PDF)
if (isset($_FILES['documentoPdf']) && !empty($_FILES['documentoPdf']['name'])) { 
    $respuesta_estado .= "\n<br />Trae documentoPdf asociado a codArt: " . $codArt; 
    
    $contenidoPdf = file_get_contents($_FILES['documentoPdf']['tmp_name']);
    
    $sql_bin = "update articulos set documentoPdf=:contenidoPdf where codArt=:codArt;"; 
    
    try {
        $stmt_bin = $dbh->prepare($sql_bin);
        $respuesta_estado .= "\n<br />Preparación exitosa";
        
        $stmt_bin->bindParam(':contenidoPdf', $contenidoPdf, PDO::PARAM_LOB); 
        $stmt_bin->bindParam(':codArt', $codArt); 
        $respuesta_estado .= "\n<br />Vinculación exitosa";
        
        $stmt_bin->execute();
        $respuesta_estado .= "\n<br />Ejecución exitosa";
        
    } catch (PDOException $e) {
        $respuesta_estado .= "\n<br />" . $e->getMessage();
    }
} else {
     $respuesta_estado .= "<br />No ha sido seleccionado ningun file para enviar"; 
}

$dbh = null; 
echo $respuesta_estado; 
?>