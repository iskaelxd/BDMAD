<?php
require_once '../Modelo/Usuario.php';
require_once '../BDConexion/BD.php';

class Usuarios {

    private $pdo;

    public function __construct() {
        $this->pdo = (new Conexion())->conectar();
    }

    public function agregar($data) {
        return $this->ejecutarSP($data, 'I');
    }

    public function actualizar($data) {
        return $this->ejecutarSP($data, 'U');
    }

    public function eliminar($id_usuario) {
        $data = ['id_usuario' => $id_usuario];
        return $this->ejecutarSP($data, 'D');
    }

    public function obtener($id_usuario = null, $username = null, $email = null) {
        $data = [
            'id_usuario' => $id_usuario,
            'username' => $username,
            'email' => $email
        ];
        return $this->ejecutarSP($data, 'S');
    }
    

    private function ejecutarSP($data, $transaCode) {
        $stmt = $this->pdo->prepare("CALL sp_crud_usuario(
            :id_usuario, :nombre, :apellido, :username, :email, :password, 
            :fecha_nacimiento, :avatar, :bio, :privacidad, :TransaCode)");
    
        $stmt->bindValue(':id_usuario', $data['id_usuario'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $data['nombre'] ?? null);
        $stmt->bindValue(':apellido', $data['apellido'] ?? null);
        $stmt->bindValue(':username', $data['username'] ?? null);
        $stmt->bindValue(':email', $data['email'] ?? null);
        $stmt->bindValue(':password', $data['password'] ?? null);
        $stmt->bindValue(':fecha_nacimiento', $data['fecha_nacimiento'] ?? null);
        $stmt->bindValue(':avatar', $data['avatar'] ?? null, PDO::PARAM_LOB);
        $stmt->bindValue(':bio', $data['bio'] ?? null);
        $stmt->bindValue(':privacidad', $data['privacidad'] ?? null);
        $stmt->bindValue(':TransaCode', $transaCode);
    
        $stmt->execute();
    
        if ($transaCode == 'S') {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
        return true;
    }
    
}
?>
