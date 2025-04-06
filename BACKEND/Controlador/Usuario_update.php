<?php
require_once '../BDConexion/BD.php';
require_once '../Modelo/Usuario.php';
require_once '../Usuario.php'; // Clase que ejecuta SP y métodos de Usuario

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extraer los datos de la solicitud
    $id_usuario       = $_POST['id_usuario']          ?? null;
    $nombre           = trim($_POST['nombre']         ?? '');
    $apellido         = trim($_POST['apellido']       ?? '');
    $fechaNacimiento  = $_POST['fecha_nacimiento']    ?? null;
    $bio              = trim($_POST['bio']            ?? '');
    $privacidad       = $_POST['privacidad']          ?? 'publico';
    // Para la verificación (1 o 0). Asegúrate de que tu SP y BD tengan la columna "cuenta_verificada"
    $cuentaVerificada = isset($_POST['cuenta_verificada']) ? 1 : 0;

    // Manejo de la imagen (avatar)
    // Si el campo "avatar" se envía con un archivo y no hubo error
    $avatar = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $avatar = file_get_contents($_FILES['avatar']['tmp_name']);
    }

    // Crear objeto de la clase Usuarios
    $usuarioBackend = new \Usuarios();

    // Preparar array de datos que pasaremos al SP
    // Nota: Dejamos en null los campos que NO queremos actualizar
    // (username, email, password) para que el SP no los modifique.
    $data = [
        'id_usuario'       => $id_usuario,
        'nombre'           => $nombre,
        'apellido'         => $apellido,
        'username'         => null,    // Campo bloqueado
        'email'            => null,    // Campo bloqueado
        'password'         => null,    // Campo bloqueado
        'fecha_nacimiento' => $fechaNacimiento,
        'avatar'           => $avatar,
        'bio'              => $bio,
        'privacidad'       => $privacidad,
        // Ojo: si tu sp_crud_usuario NO maneja "cuenta_verificada",
        // necesitarás modificarlo para aceptar y actualizar ese campo.
        // De lo contrario, deberás actualizarlo manualmente con una consulta aparte.
    ];

    try {
        // Llamamos al método actualizar (que internamente ejecutará SP con 'U')
        $resultado = $usuarioBackend->actualizar($data);

        // Si además manejas `cuenta_verificada` en tu tabla,
        // pero tu SP no lo contempla, podrías hacer una actualización manual aquí:
        // ejemplo:
        /*
        $pdo = (new Conexion())->conectar();
        $stmt = $pdo->prepare("UPDATE Usuario
                               SET cuenta_verificada = :verificado
                               WHERE id_usuario = :id");
        $stmt->execute([
            ':verificado' => $cuentaVerificada,
            ':id'         => $id_usuario
        ]);
        */

        echo json_encode([
            'status'  => 'success',
            'message' => 'Usuario actualizado correctamente'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
?>
