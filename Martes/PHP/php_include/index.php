<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ejemplo de Include</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <h2>Demostración de include()</h2>

    <div class="warning">
        <strong>Importante:</strong> Antes de insertar el include las variables declaradas en el mismo no existen
    </div>

    <p>Pero a pesar de ello el ciclo de ejecución continuará hasta el final</p>

    <?php
    echo "$arreglo1 <br>";
    echo "$arreglo2 <br>";
    ?>

    <hr>

    <div class="info">
        <strong>En este punto se ejecuta la función include().</strong> Cuando se usa include ocurre que si el archivo asociado no existe, se visualiza un warning y el script sigue ejecutándose.
    </div>

    <?php

    include('include.php');


    echo "<h3>Contenido de los arreglos después del include:</h3>";
    echo "<p>Valores de \$arreglo1:</p>";
    echo "<table>";
    foreach ($arreglo1 as $clave => $valor) {
        echo "<tr><td>$clave</td><td>$valor</td></tr>";
    }
    echo "</table>";

    echo "<strong>La longitud del arreglo1 es: " . count($arreglo1) . "</strong>";

    echo "<p>Valores de \$arreglo2:</p>";
    echo "<table>";
    foreach ($arreglo2 as $clave => $valor) {
        echo "<tr><td>$clave</td><td>$valor</td></tr>";
    }
    echo "</table>";

    echo "<strong>La longitud del arreglo2 es: " . count($arreglo2) . "</strong>";

    ?>

    <hr>
    <button class="volver-atras">
        <a class="volver-atras" href="../index.php">Volver al inicio</a>
    </button>

</body>

</html>