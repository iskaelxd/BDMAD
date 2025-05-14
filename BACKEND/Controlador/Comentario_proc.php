<?php

/* ../BACKEND/Controlador/Comentario_proc.php */
header('Content-Type: application/json; charset=UTF-8');
require_once '../BDConexion/BD.php';

$accion     = $_POST['accion']     ?? 'S';  // I | S
$idPost     = intval($_POST['id_post'] ?? 0);
$idYo       = intval($_POST['id_usuario'] ?? 0);
$contenido  = $_POST['contenido']  ?? null;

$imagenBlob = null;
if(!empty($_FILES['imagen']['tmp_name'])){
    $imagenBlob = file_get_contents($_FILES['imagen']['tmp_name']);
}

try{
    $pdo  = (new Conexion())->conectar();
    $stmt = $pdo->prepare("CALL spComentario(:a,:p,:yo,:c,:img)");
    $stmt->execute([
        ':a'   => $accion,
        ':p'   => $idPost,
        ':yo'  => $idYo,
        ':c'   => $contenido,
        ':img' => $imagenBlob
    ]);
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status'=>'success','data'=>$datos]);
}catch(PDOException $e){
    /* violación de permisos => status = forbidden  */
    if($e->getCode() === '45000'){
        echo json_encode(['status'=>'forbidden','msg'=>$e->getMessage()]);
    }else{
        echo json_encode(['status'=>'error','msg'=>$e->getMessage()]);
    }
}



?>