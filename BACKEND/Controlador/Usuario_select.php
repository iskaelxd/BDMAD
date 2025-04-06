<?php
require_once '../BDConexion/BD.php';
require_once '../Modelo/Usuario.php';
require_once '../Usuario.php'; // Clase que ejecuta el SP

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Puedes permitir buscar por email, username o id_usuario.
    // En este ejemplo, buscaremos por email. Adapta según tu necesidad.
    $email = $_POST['email'] ?? null;
    $username = $_POST['username'] ?? null;
    $id_usuario = $_POST['id_usuario'] ?? null;

    // Instancia de la clase que maneja las operaciones con el SP
    $usuarioBackend = new \Usuarios();

    // Llamamos al método obtener, que usa el TransaCode = 'S'
    // El método obtener está definido de tal forma que si alguno de los 3
    // parámetros (id_usuario, username, email) es válido, te filtra por ese campo.
    // Si los 3 vienen nulos, obtendría todos los usuarios (según tu SP).
    $resultado = $usuarioBackend->obtener($id_usuario, $username, $email);

    if (!empty($resultado)) {
        // Si viene información, convertimos el campo avatar (BLOB) a base64
        // para poder retornarlo en JSON sin problemas.
        foreach ($resultado as &$fila) {
            if (!empty($fila['avatar'])) {
                // Lo convertimos a base64
                $fila['avatar'] = base64_encode($fila['avatar']);
            }
        }
        // Devolvemos la info en formato JSON
        echo json_encode([
            'status' => 'success',
            'data'   => $resultado
        ]);
    } else {
        // Si no se encontró ningún usuario con ese criterio
        echo json_encode([
            'status'  => 'error',
            'message' => 'No existe usuario con los datos especificados.'
        ]);
    }
}
?>
