<?php
//script que se aplica para todos los archivos
include_once __DIR__ . '/../datos_conexion_a_la_base.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['iduser']) || !isset($_SESSION['session_id'])) {
    header('location: ../index.php');
    exit();
}
?>