<?php
$nombre_campo = 'claveAEncriptar'; 
$clave_ingresada = '';
$mostrar_resultado = false;

if (isset($_POST[$nombre_campo])) {
    $clave_ingresada = $_POST[$nombre_campo];
    $mostrar_resultado = true;
    
    $clave_md5 = md5($clave_ingresada);
    $clave_sha256 = hash('sha256', $clave_ingresada);

} else {
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Encriptación</title>
</head>
<body>

    <?php if ($mostrar_resultado): ?>
    
        <p>Clave: <?php echo $clave_ingresada; ?></p>
        <p>Clave encriptada en md5 (128 bits o 16 octetos o 16 pares hexadecimales):</p>
        <p><?php echo $clave_md5; ?></p>
        
        <p>Clave: <?php echo $clave_ingresada; ?></p>
        <p>Clave encriptada en sha256 (256 bits o 32 octetos o 32 pares hexadecimales):</p>
        <p><?php echo $clave_sha256; ?></p>
        
        <?php else: ?>

        <form method="POST" action="">
            <label for="<?php echo $nombre_campo; ?>">Ingrese la clave a encriptar:</label>
            <input type="text" id="<?php echo $nombre_campo; ?>" name="<?php echo $nombre_campo; ?>" required>
            <button type="submit">Obtener encriptación</button>
        </form>

    <?php endif; ?>

</body>
</html>