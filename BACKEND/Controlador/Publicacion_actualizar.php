<?php
/* ─────────────────────────────────────────────────────────────
   ACTUALIZA una publicación:
   • Cambia texto, privacidad o restricción de comentarios
   • (Opcional) borra lógicamente TODAS las imágenes / vídeos antiguos
   • Añade imágenes y/o vídeos nuevos

   Front-end:
      fd.append('id_post' , 36);              // ← obligatorio
      fd.append('id_usuario', usuario.id_usuario);

      // si el usuario quitó todos los adjuntos viejos:
      fd.append('replace_media', 1);

   Respuesta JSON:
      { "status":"success", "message":"Publicación actualizada" }
   ──────────────────────────────────────────────────────────── */
header('Content-Type: application/json; charset=UTF-8');
ob_clean();

require_once __DIR__ . '/../BDConexion/BD.php';
require_once __DIR__ . '/../Publicacion.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    /* ---------- 1. campos obligatorios ---------- */
    foreach (['id_post','id_usuario'] as $f) {
        if (empty($_POST[$f]) || !is_numeric($_POST[$f])) {
            throw new Exception("Falta $f", 400);
        }
    }
    $id_post    = (int) $_POST['id_post'];
    $id_usuario = (int) $_POST['id_usuario'];

    /* ---------- 2. variables opcionales ---------- */
    $contenido   = trim($_POST['contenido'] ?? '');
    $privacidad  = $_POST['privacidad']             ?? null;
    $restringir  = $_POST['restringir_comentarios'] ?? null;
    $replaceAll  = !empty($_POST['replace_media']);      // ‘1’ o true

    /* ---------- 3. DAO ---------- */
    $dao = new Publicaciones();

    /* 3-a. Actualiza la tabla Publicacion (TransaCode 'U') */
    $dao->actualizar([
        'id_post'                => $id_post,
        'id_usuario'             => $id_usuario,          // por seguridad
        'contenido'              => $contenido !== '' ? $contenido : null,
        'restringir_comentarios' => $restringir,
        'privacidad'             => $privacidad
    ]);                                                   // 'U' por defecto

    /* 3-b. (Opcional) borrar lógicamente TODOS los adjuntos previos */
    if ($replaceAll) {
        /*  TransaCode personalizado: 'C' = Clear media  */
        $dao->ejecutarSP(['id_post' => $id_post], transaCode: 'C');
    }

    /* 3-c. Insertar IMÁGENES nuevas (TransaCode 'M') */
    if (!empty($_FILES['imagenes']['tmp_name'])) {
        foreach ($_FILES['imagenes']['tmp_name'] as $i => $tmp) {
            if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
                $blob = file_get_contents($tmp);

                $dao->ejecutarSP([
                    'id_post'           => $id_post,
                    'imagen'            => $blob,
                    'id_usuario_imagen' => $id_usuario
                ], 'M');
            }
        }
    }

    /* 3-d. Insertar VIDEOS nuevos (TransaCode 'V') */
    if (!empty($_FILES['videos']['tmp_name'])) {
        $root = dirname(__DIR__, 2);
        $dir  = $root . '/Multimedia/Videos';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        foreach ($_FILES['videos']['tmp_name'] as $j => $tmp) {
            if ($_FILES['videos']['error'][$j] === UPLOAD_ERR_OK) {
                $ext  = pathinfo($_FILES['videos']['name'][$j], PATHINFO_EXTENSION);
                $name = "{$id_post}_{$id_usuario}_" . uniqid() . ".$ext";
                move_uploaded_file($tmp, "$dir/$name");

                $rel = "Multimedia/Videos/$name";          // ruta a guardar

                $dao->ejecutarSP([
                    'id_post'              => $id_post,
                    'ruta_video'           => $rel,
                    'nombre_archivo_video' => $name,
                    'id_usuario_video'     => $id_usuario
                ], 'V');
            }
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Publicación actualizada']);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
