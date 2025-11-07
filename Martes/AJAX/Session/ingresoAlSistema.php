<?php
session_start();
global $dbh;

include 'datos_conexion_a_la_base.php';
include 'libreria.php';

if (!isset($dbh)) {
    die("Error: No se pudo establecer la conexión con la base de datos");
}

// 1. Condicional para evitar re-incremento por reload
if (isset($_SESSION['iduser']) && $_SERVER["REQUEST_METHOD"] != "POST") {
    mostrarPaginaIntermedia();
    exit();
}

// 2. Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    $auth_data = autenticarUsuario($dbh, $usuario, $password);

    if ($auth_data) {
        // 3. Autenticación exitosa: Crea sesión
        
    // Incrementar contador en la BD (tabla `usuarios`)
    $nuevo_contador = $auth_data['contador'] + 1;
    $sql_update = "UPDATE usuarios SET contador_sesiones = ? WHERE id_usuario = ?";
    $stmt_update = $dbh->prepare($sql_update);
    $stmt_update->execute([$nuevo_contador, $auth_data['iduser']]);

        // 4. Crear variables de sesión
        $_SESSION['iduser'] = $auth_data['iduser'];
        $_SESSION['usuario'] = $usuario;
        $_SESSION['session_id'] = session_create_id();
        $_SESSION['contador'] = $nuevo_contador;

        // 5. Mostrar página intermedia
        mostrarPaginaIntermedia();

    } else {
        // 6. Falla de autenticación: Redirigir de vuelta al login
        header('Location: formularioDeLogin.html?error=1');
        exit();
    }
} else {
    header('Location: formularioDeLogin.html');
    exit();
}

function mostrarPaginaIntermedia() {
    // Genera la página de información de sesión
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
            <p><strong>Identificativo de sesión:</strong> ' . htmlspecialchars($_SESSION['session_id']) . '</p>
            <p><strong>Login de usuario:</strong> ' . htmlspecialchars($_SESSION['usuario']) . '</p>
            <p><strong>Contador de sesión:</strong> ' . htmlspecialchars($_SESSION['contador']) . '</p>
            <button class="btn-primary" onclick="location.href=\'./app_modulo1/index.php\'">Ingrese al módulo</button>
            <button class="btn-secondary" onclick="location.href=\'destruirsesion.php\'">Terminar sesión</button>
        </div>
    </body>
    </html>';
}
?>