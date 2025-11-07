<?php
// Este script será INCLUIDO por todos los archivos de esta carpeta
// Sube un nivel (..) para encontrar el archivo de conexión
include_once __DIR__ . '/../datos_conexion_a_la_base.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si la sesión NO está seteada, lo expulsamos al index principal
if (!isset($_SESSION['iduser']) || !isset($_SESSION['session_id'])) {
    header('location: ../index.php');
    exit();
}
?>