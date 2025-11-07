<?php
session_start();

// Este script es el "portero" de toda la aplicación

// Si NO hay una sesión de usuario...
if (!isset($_SESSION['iduser'])) {
    // ...lo mandamos al formulario de login.
    header('Location: formularioDeLogin.html');
    exit();
} else {
    // ...SI hay una sesión, lo mandamos a la página intermedia.
    header('Location: ingresoAlSistema.php');
    exit();
}
?>