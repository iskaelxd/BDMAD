<?php
header('Content-Type: application/json; charset=UTF-8');
ob_clean();

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/error.log');


require_once __DIR__ . '/../BDConexion/BD.php';
require_once __DIR__ . '/../Publicacion.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception('Método no permitido', 405);

    if (empty($_POST['id_post']) || !is_numeric($_POST['id_post']))
        throw new Exception('Falta id_post', 400);

    $dao  = new Publicaciones();
    $rows = $dao->obtener(['id_post' => (int)$_POST['id_post']]);   // «S»

    if (!$rows) throw new Exception('No existe la publicación', 404);

    /* ── Reagrupar imágenes (BLOB) y videos (ruta) ── */
    $out = [
        'id_post'                => $rows[0]['id_post'],
        'id_usuario'             => $rows[0]['id_usuario'],
        'contenido'              => $rows[0]['contenido'],
        'privacidad'             => $rows[0]['privacidad'],
        'restringir_comentarios' => $rows[0]['restringir_comentarios'],
        'imagenes'               => [],
        'videos'                 => []
    ];

    foreach ($rows as $r) {
        if ($r['imagen'] !== null)
            $out['imagenes'][] = base64_encode($r['imagen']);
        if ($r['ruta_video'])
            $out['videos'][]   = $r['ruta_video'];
    }

    echo json_encode(['status'=>'success','data'=>[$out]], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
?>