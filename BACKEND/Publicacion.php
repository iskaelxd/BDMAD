<?php
require_once '../Modelo/Publicacion.php';
require_once '../BDConexion/BD.php';

class Publicaciones {
    public $pdo;

    public function __construct() {
        $this->pdo = (new Conexion())->conectar();
    }

    public function agregar(array $data) {
        $this->ejecutarSP($data, 'I');
        return $this->pdo->lastInsertId();
    }

    public function actualizar(array $data) {
        return $this->ejecutarSP($data, 'U');
    }

    public function eliminar(int $id_post) {
        return $this->ejecutarSP(['id_post' => $id_post], 'D');
    }

    /**
     * @param array $filters puede contener id_post, id_usuario, privacidad, restringir_comentarios
     */
    public function obtener(array $filters = []) {
        return $this->ejecutarSP($filters, 'S');
    }

    public function ejecutarSP(array $d, string $transaCode) {
        $stmt = $this->pdo->prepare("CALL sp_crud_publicacion(
                :id_post,
                :id_usuario,
                :contenido,
                :restringir_comentarios,
                :likes,
                :fecha_post,
                :privacidad,
                :deletedOn,
                :imagen,
                :id_usuario_imagen,
                :ruta_video,
                :nombre_archivo_video,
                :id_usuario_video,
                :id_video,
                :id_imagen,
                :TransaCode
        )");

      

        // ---- PUBLICACIÓN ----
        $stmt->bindValue(':id_post',                 $d['id_post'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':id_usuario',              $d['id_usuario'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':contenido',               $d['contenido'] ?? null);
        $stmt->bindValue(':restringir_comentarios',  $d['restringir_comentarios'] ?? null);
        $stmt->bindValue(':likes',                   $d['likes'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':fecha_post',              $d['fecha_post'] ?? null);
        $stmt->bindValue(':privacidad',              $d['privacidad'] ?? null);
        $stmt->bindValue(':deletedOn',               $d['deletedOn'] ?? null);

        // ---- IMAGEN (BLOB) ----
        $stmt->bindValue(':imagen',                  $d['imagen'] ?? null, PDO::PARAM_LOB);
        $stmt->bindValue(':id_usuario_imagen',       $d['id_usuario_imagen'] ?? null, PDO::PARAM_INT);

        // ---- VIDEO (ruta) ----
        $stmt->bindValue(':ruta_video',              $d['ruta_video'] ?? null);
        $stmt->bindValue(':id_usuario_video',        $d['id_usuario_video'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':nombre_archivo_video',    $d['nombre_archivo_video'] ?? null);

        // IDs auxiliares para UPDATE
        $stmt->bindValue(':id_video',                $d['id_video'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':id_imagen',               $d['id_imagen'] ?? null, PDO::PARAM_INT);

        // ACCIÓN
        $stmt->bindValue(':TransaCode',              $transaCode);


        $stmt->execute();

        if ($transaCode === 'S') {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return true;
    }
}
?>
