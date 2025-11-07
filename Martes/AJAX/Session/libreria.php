<?php
// Este archivo contiene la función de autenticación
function autenticarUsuario($pdo, $usuario, $password) {
    if (!$pdo) {
        error_log("Error: No hay conexión a la base de datos disponible");
        return false;
    }
    try {
        // Encripta la clave recibida para comparar
        $password_hasheada = hash('sha256', $password);

        $sql = "SELECT id_usuario as iduser, password, contador_sesiones FROM usuarios WHERE login = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario]);
        $user = $stmt->fetch();

        // Compara claves encriptadas
        if ($user && $password_hasheada === $user['password']) {
            // Éxito: Aceptado=True
            return ['iduser' => $user['iduser'], 'contador' => $user['contador_sesiones']];
        } else {
            // Falla: Aceptado=False
            return false;
        }
    } catch (PDOException $e) {
        return false;
    }
}
?>