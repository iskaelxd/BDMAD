<?php
header('Content-Type: application/json; charset=UTF-8');
require_once '../BDConexion/BD.php';

try {
    if ($_SERVER['REQUEST_METHOD']!=='POST') throw new Exception('Método no permitido',405);
    if (empty($_POST['viewer'])||!is_numeric($_POST['viewer'])) throw new Exception('Falta viewer',400);

    $usuario = (int)$_POST['viewer'];
    $pdo      = (new Conexion())->conectar();
    $stmt     = $pdo->prepare('CALL sp_count_unread(?)');
    $stmt->execute([$usuario]);
    $row      = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['status'=>'success','unread'=>$row['unread_count']], JSON_UNESCAPED_UNICODE);
} catch(Exception $e) {
    http_response_code($e->getCode()?:500);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
?>
