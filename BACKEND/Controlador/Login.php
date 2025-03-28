<?php
require_once '../BDConexion/BD.php';
require_once '../Usuario.php'; // La clase con el SP

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $usuario = new \Usuarios();

    // Buscar por email (TransaCode = 'S')
    $resultado = $usuario->obtener(null, null, $email);

    if (empty($resultado)) {
        echo json_encode(['status' => 'error', 'message' => 'Correo no encontrado.']);
        exit;
    }

    $usuarioData = $resultado[0]; // Solo debe haber uno

    // Validar contraseña
    if (password_verify($password, $usuarioData['password'])) {
        // Podrías aquí crear una sesión o token si deseas
        echo json_encode(['status' => 'success', 'message' => 'Inicio de sesión exitoso.', 'usuario' => [
            'id_usuario' => $usuarioData['id_usuario'],
            'username' => $usuarioData['username'],
            'email' => $usuarioData['email'],
            'nombre' => $usuarioData['nombre']
        ]]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Contraseña incorrecta.']);
    }
}
?>
