<?php
header('Content-Type: application/json; charset=UTF-8');
require_once '../BDConexion/BD.php';

try {
    if ($_SERVER['REQUEST_METHOD']!=='POST') throw new Exception('Método no permitido',405);
    if (empty($_POST['id_notificacion']) || !is_numeric($_POST['id_notificacion']))
        throw new Exception('Falta id_notificacion',400);

    $id = (int)$_POST['id_notificacion'];
    $pdo = (new Conexion())->conectar();

    // Marca como vista
    $stmt = $pdo->prepare("UPDATE Notificacion SET visto = TRUE WHERE id_notificacion = ?");
    $stmt->execute([$id]);

    echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
} catch(Exception $e) {
    http_response_code($e->getCode()?:500);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
?>