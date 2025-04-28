<?php
header('Content-Type: application/json; charset=UTF-8');
ob_clean();               // evita espacios previos

require_once __DIR__ . '/../BDConexion/BD.php';
require_once __DIR__ . '/../Publicacion.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }
    if (empty($_POST['id_usuario']) || !is_numeric($_POST['id_usuario'])) {
        throw new Exception('Falta id_usuario', 400);
    }

    $dao   = new Publicaciones();
    $posts = $dao->obtener(['id_usuario' => (int)$_POST['id_usuario']]);

    /* —— Convierte BLOBs a Base64 —— */
    foreach ($posts as &$p) {
        if (isset($p['imagen']) && $p['imagen'] !== null) {
            $p['imagen'] = base64_encode($p['imagen']);
        }
    }

    echo json_encode(['status'=>'success','data'=>$posts], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
?>