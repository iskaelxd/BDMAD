
<?php
require_once '../BDConexion/BD.php';
require_once '../Modelo/Usuario.php';
require_once '../Usuario.php'; // Clase que ejecuta SP

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $fechaNacimiento = $_POST['fechaNacimiento'];

    // Encriptar contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Crear objeto de la clase Usuario (la de backend que ejecuta SP)
    $usuarioBackend = new \Usuarios();

        // Primero validamos si el username o email ya existen
        $existeUsername = $usuarioBackend->obtener(null, $username, null);
        $existeEmail = $usuarioBackend->obtener(null, null, $email);
    
        if (!empty($existeUsername)) {
            echo json_encode(['status' => 'error', 'message' => 'El nombre de usuario ya está en uso.']);
            exit;
        }
    
        if (!empty($existeEmail)) {
            echo json_encode(['status' => 'error', 'message' => 'El correo electrónico ya está registrado.']);
            exit;
        }
    
        // Si no existen, continuamos con el registro

    // Preparar los datos para insertar
    $data = [
        'nombre' => $nombre,
        'apellido' => $apellido,
        'username' => $username,
        'email' => $email,
        'password' => $passwordHash,
        'fecha_nacimiento' => $fechaNacimiento,
        'avatar' => null,
        'bio' => null,
        'privacidad' => 'publico',
    ];

    try {
        $resultado = $usuarioBackend->agregar($data);
        echo json_encode(['status' => 'success', 'message' => 'Usuario registrado correctamente']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
