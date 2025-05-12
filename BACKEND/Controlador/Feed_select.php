<?php
header('Content-Type: application/json; charset=UTF-8');
require_once '../BDConexion/BD.php';
require_once '../Publicacion.php';   //  → contiene la clase Publicaciones
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')  throw new Exception('Método no permitido',405);
    if (empty($_POST['viewer']) || !is_numeric($_POST['viewer'])) throw new Exception('Falta viewer',400);

    $viewer = (int)$_POST['viewer'];
    $lim    = !empty($_POST['limit'])  ? (int)$_POST['limit']  : 20;
    $off    = !empty($_POST['offset']) ? (int)$_POST['offset'] : 0;

    /* llamada directa al procedure */
    $pdo = (new Conexion())->conectar();
    $stmt = $pdo->prepare('CALL sp_feed(?,?,?)');
    $stmt->execute([$viewer,$lim,$off]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status'=>'success','data'=>$rows], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
?>