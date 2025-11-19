<?php
function autenticarUsuario($pdo, $usuario, $password) {
    if (!$pdo) {
        error_log("Error: No hay conexión a la base de datos disponible");
        return false;
    }
    try {
        //encripta la clave recibida para despues compararla
        $password_hasheada = hash('sha256', $password);

        $sql = "SELECT id_usuario as iduser, password, contador_sesiones FROM usuarios WHERE login = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario]);
        //array asociativo
        $user = $stmt->fetch();

        //compara claves encriptadas
        if ($user && $password_hasheada === $user['password']) {
            //True y devuelve el array asociativo
            return ['iduser' => $user['iduser'], 'contador' => $user['contador_sesiones']];
        } else {
            //False
            return false;
        }
    } catch (PDOException $e) {
        return false;
    }
}

?>
