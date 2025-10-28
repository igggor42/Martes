<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuesta formulario</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    .volver-atras {
        background-color: #000000ff;
        color: white;
        border: none;
        padding: 10px 15px;
        text-decoration: none;
        border-radius: 5px;
    }

    .volver-atras a {
        color: white;
        text-decoration: none;
    }
</style>

<body>
    <h1>Respuesta del formulario</h1>
    <?php
    echo "Nombre: " . htmlspecialchars($_POST['nombre']) . "<br>";
    echo "<hr>";
    echo "Apellido: " . htmlspecialchars($_POST['apellido']) . "<br>";

    echo '<div style="margin-top: 20px;">
        <button class="volver-atras">
        <a href="index.php">Volver atras</a>
    </button>
    </div>';
    ?>
</body>

</html>