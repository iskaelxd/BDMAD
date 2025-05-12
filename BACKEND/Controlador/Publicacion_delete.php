<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

require_once '../BDConexion/BD.php';
require_once '../Modelo/Publicacion.php';
require_once '../Publicacion.php';     // DAO

try {
    /* ---------- 1. SOLO POST ---------- */
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    /* ---------- 2. id_post ---------- */
    if (empty($_POST['id_post']) || !is_numeric($_POST['id_post'])) {
        throw new Exception('Falta el id_post', 400);
    }
    $id_post = (int) $_POST['id_post'];

    /* (opcional) ---------- 3. autorizar ----------
       Si manejas login por sesión:
       if (!isset($_SESSION['id_usuario'])) { … }
       // y compara que la publicación sea del usuario logueado
    */

    /* ---------- 4. ELIMINAR ---------- */
    $dao = new Publicaciones();
    $dao->eliminar($id_post);   // TransaCode 'D' dentro del DAO

    /* ---------- 5. OK ---------- */
    echo json_encode([
        'status'  => 'success',
        'message' => 'Publicación eliminada'
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
