<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lectura de formularios con php</title>
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

        form {
            margin-top: 20px;
            margin-bottom:20px;

        }
        .button-submit {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Lectura de formularios con php</h1>
    <form method="post" action="respuesta.php">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
        <br><br>
        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" required>
        <br><br>
        <button class="button-submit" type="submit">Enviar</button>
    </form>
    <div style="margin-top: 20px;">
        <button class="volver-atras">
        <a href="../index.php">Volver atras</a>
    </button>
    </div>
</body>
</html>