<?php
session_start();
global $dbh;

include 'datos_conexion_a_la_base.php';
include 'libreria.php';

if (!isset($dbh)) {
    die("Error: No se pudo establecer la conexión con la base de datos");
}

//evita re-incremento por reload
if (isset($_SESSION['iduser']) && $_SERVER["REQUEST_METHOD"] != "POST") {
    mostrarPaginaIntermedia();
    exit();
}

//autentificacion e inicio de sesion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    $auth_data = autenticarUsuario($dbh, $usuario, $password);

    if ($auth_data) {
        //crea sesion e incrementa usuarios
    $nuevo_contador = $auth_data['contador'] + 1;
    $sql_update = "UPDATE usuarios SET contador_sesiones = ? WHERE id_usuario = ?";
    $stmt_update = $dbh->prepare($sql_update);
    $stmt_update->execute([$nuevo_contador, $auth_data['iduser']]);

        $_SESSION['iduser'] = $auth_data['iduser'];
        $_SESSION['usuario'] = $usuario;
        $_SESSION['session_id'] = session_create_id();
        $_SESSION['contador'] = $nuevo_contador;

        //pagina intermedia
        mostrarPaginaIntermedia();

    } else {
        //redirige de vuelta al login
        header('Location: formularioDeLogin.html?error=1');
        exit();
    }
} else {
    header('Location: formularioDeLogin.html');
    exit();
}

function mostrarPaginaIntermedia() {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Sesión Iniciada</title>
        <style>
            body { font-family: sans-serif; padding: 2rem; background-color: #f9f9f9; }
            .container { max-width: 600px; margin: 0 auto; background: #fff; border: 1px solid #ddd; padding: 2rem; border-radius: 8px; }
            h1 { color: #333; }
            p { background: #eee; padding: 0.5rem; border-radius: 4px; word-break: break-all; }
            button { padding: 0.7rem 1rem; margin-right: 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
            .btn-primary { background-color: #28a745; color: white; }
            .btn-secondary { background-color: #dc3545; color: white; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Información de Sesión</h1>
            <p><br>Identificativo de sesión:</br> ' . htmlspecialchars($_SESSION['session_id']) . '</p>
            <p><br>Login de usuario:</br> ' . htmlspecialchars($_SESSION['usuario']) . '</p>
            <p><br>Contador de sesión:</br> ' . htmlspecialchars($_SESSION['contador']) . '</p>
            <button class="btn-primary" onclick="location.href=\'./app_modulo1/index.php\'">Ingrese al módulo</button>
            <button class="btn-secondary" onclick="location.href=\'destruirsesion.php\'">Terminar sesión</button>
        </div>
    </body>
    </html>';
}

?>

