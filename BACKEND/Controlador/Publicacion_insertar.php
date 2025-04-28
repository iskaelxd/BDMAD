<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

require_once '../BDConexion/BD.php';
require_once '../Modelo/Publicacion.php';
require_once '../Publicacion.php';   // DAO que llama al SP

try {
    /* ---------- request básica ---------- */
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }
    if (empty($_POST['id_usuario']) || !is_numeric($_POST['id_usuario'])) {
        throw new Exception('Falta el id_usuario', 400);
    }

    /* ---------- límites ---------- */
    $maxImgs = 10;
    $maxVids = 4;
    $imgCount = isset($_FILES['imagenes']['tmp_name'])
              ? count($_FILES['imagenes']['tmp_name']) : 0;
    $vidCount = isset($_FILES['videos']['tmp_name'])
              ? count($_FILES['videos']['tmp_name'])   : 0;

    if ($imgCount > $maxImgs) {
        throw new Exception("Solo se permiten $maxImgs imágenes", 400);
    }
    if ($vidCount > $maxVids) {
        throw new Exception("Solo se permiten $maxVids videos", 400);
    }

    /*  tamaño total del cuerpo (en bytes)  */
        $contentLength = $_SERVER['CONTENT_LENGTH'] ?? 0;
        $maxTotal      = 64 * 1024 * 1024;          // 64 MB

        if ($contentLength > $maxTotal) {
            throw new Exception('Los archivos superan el límite de 64 MB', 400);
        }


    /* ---------- variables ---------- */
    $id_usuario = (int) $_POST['id_usuario'];
    $contenido  = trim($_POST['contenido'] ?? '');
    $privacidad = $_POST['privacidad'] ?? 'publico';
    $restringir = $_POST['restringir_comentarios'] ?? 'publico';

    $dao = new Publicaciones();

    /* ---------- 1. inserta publicación (I) ---------- */
    $dao->ejecutarSP([
        'id_post'                => null,
        'id_usuario'             => $id_usuario,
        'contenido'              => $contenido,
        'restringir_comentarios' => $restringir,
        'likes'                  => 0,
        'fecha_post'             => null,
        'privacidad'             => $privacidad,
        'deletedOn'              => null,

        'imagen'                 => null,
        'id_usuario_imagen'      => null,

        'ruta_video'             => null,
        'nombre_archivo_video'   => null,   // <-- AÑADIDO
        'id_usuario_video'       => null,

        'id_video'               => null,
        'id_imagen'              => null
    ], 'I');

    /* ---------- id_post recién creado ---------- */
    $id_post = (int) $dao->pdo
                ->query('SELECT LAST_INSERT_ID() AS id_post')
                ->fetch(PDO::FETCH_ASSOC)['id_post'];

    /* ---------- 2. imágenes (M) ---------- */
    if (!empty($_FILES['imagenes']['tmp_name'])) {
        foreach ($_FILES['imagenes']['tmp_name'] as $i => $tmpPath) {
            if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
                $blob = file_get_contents($tmpPath);

                $dao->ejecutarSP([
                    'id_post'                => $id_post,
                    'id_usuario'             => null,
                    'contenido'              => null,
                    'restringir_comentarios' => null,
                    'likes'                  => null,
                    'fecha_post'             => null,
                    'privacidad'             => null,
                    'deletedOn'              => null,

                    'imagen'                 => $blob,
                    'id_usuario_imagen'      => $id_usuario,

                    'ruta_video'             => null,
                    'nombre_archivo_video'   => null,
                    'id_usuario_video'       => null,

                    'id_video'               => null,
                    'id_imagen'              => null
                ], 'M');
            }
        }
    }

    /* ---------- 3. videos (V) ---------- */
    if (!empty($_FILES['videos']['tmp_name'])) {

        /* carpeta */
        $projectRoot = dirname(__DIR__, 2);
        $vidDir      = $projectRoot . '/Multimedia/Videos';
        if (!is_dir($vidDir)) mkdir($vidDir, 0755, true);

        foreach ($_FILES['videos']['tmp_name'] as $j => $tmpPath) {
            if ($_FILES['videos']['error'][$j] === UPLOAD_ERR_OK) {

                $ext      = pathinfo($_FILES['videos']['name'][$j],
                                     PATHINFO_EXTENSION);
                $fileName = "{$id_post}_{$id_usuario}_vid{$j}.{$ext}";
                $dest     = "$vidDir/$fileName";
                move_uploaded_file($tmpPath, $dest);

                $rutaRel = "Multimedia/Videos/$fileName";

                $dao->ejecutarSP([
                    'id_post'                => $id_post,
                    'id_usuario'             => null,
                    'contenido'              => null,
                    'restringir_comentarios' => null,
                    'likes'                  => null,
                    'fecha_post'             => null,
                    'privacidad'             => null,
                    'deletedOn'              => null,

                    'imagen'                 => null,
                    'id_usuario_imagen'      => null,

                    'ruta_video'             => $rutaRel,
                    'nombre_archivo_video'   => $fileName,  // <-- IMPORTANTE
                    'id_usuario_video'       => $id_usuario,

                    'id_video'               => null,
                    'id_imagen'              => null
                ], 'V');
            }
        }
    }

    /* ---------- OK ---------- */
    echo json_encode([
        'status'  => 'success',
        'message' => 'Publicación creada correctamente',
        'id_post' => $id_post
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
