<?php
session_start();
//si NO hay una sesion de usuario
if (!isset($_SESSION['iduser'])) {
    //va al formulario de login
    header('Location: formularioDeLogin.html');
    exit();
} else {
    //SI hay una sesion, se manda a la pagina intermedia
    header('Location: ingresoAlSistema.php');
    exit();
}
?>