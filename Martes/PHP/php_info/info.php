<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Primer Ejercicio php</title>
    <style>
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
</head>
<body>
    <div style="display: flex; flex-direction: row; justify-content: center; align-items: center; margin-top: 20px; border-radius: 5px;">
    <button class="volver-atras">
    <a class="volver-atras" href="../index.php">Volver al inicio</a>
</button>
</div>
<?php
phpinfo();
?>
<div style="display: flex; flex-direction: row; justify-content: center; align-items: center; margin-top: 20px;">
    <button class="volver-atras">
    <a class="volver-atras" href="../index.php">Volver al inicio</a>
</button>
</div>
</body>
</html>