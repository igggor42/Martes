<?php
// Este archivo contiene la función de autenticación
function autenticarUsuario($pdo, $usuario, $password) {
    try {
        // Encripta la clave recibida para comparar
        $password_hasheada = hash('sha256', $password);

        $sql = "SELECT iduser, password, contador_sesiones FROM Login WHERE usuario = ?";
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