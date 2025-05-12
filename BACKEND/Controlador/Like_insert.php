<?php
header('Content-Type: application/json; charset=UTF-8');
require_once '../BDConexion/BD.php';

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST')
      throw new Exception('Método no permitido', 405);

  foreach (['id_post','id_usuario'] as $f)
      if (empty($_POST[$f]) || !is_numeric($_POST[$f]))
          throw new Exception("Falta $f", 400);

  $pdo  = (new Conexion())->conectar();
  $stmt = $pdo->prepare('CALL sp_like_post(?,?)');
  $stmt->execute([(int)$_POST['id_post'], (int)$_POST['id_usuario']]);
  $out  = $stmt->fetch(PDO::FETCH_ASSOC);

  echo json_encode(['status'=>'success', 'delta'=>(int)$out['delta']]);
} catch (Exception $e) {
  http_response_code($e->getCode() ?: 500);
  echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
?>
