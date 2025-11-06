<?php
session_start();
session_unset();
session_destroy();
header('Location: formularioDeLogin.html');
exit();
?>