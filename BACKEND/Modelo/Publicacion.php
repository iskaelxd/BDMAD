<?php
class Publicacion {
    public $id_post;
    public $id_usuario;
    public $contenido;
    public $restringir_comentarios;
    public $likes;
    public $fecha_post;
    public $privacidad;

    // Nuevos para video
    public $id_video;
    public $id_usuario_video;
    public $nombre_archivo_video;
    public $ruta_video;

    // Nuevos para imagen
    public $id_imagen;
    public $id_usuario_imagen;
    public $nombre_archivo_imagen;
    public $ruta_imagen;

    public function __construct(
        $id_usuario,
        $contenido,
        $restringir_comentarios,
        $likes = 0,
        $privacidad = 'publico',
        $id_video = null,
        $id_usuario_video = null,
        $nombre_archivo_video = null,
        $ruta_video = null,
        $id_imagen = null,
        $id_usuario_imagen = null,
        $nombre_archivo_imagen = null,
        $ruta_imagen = null
    ) {
        $this->id_usuario             = $id_usuario;
        $this->contenido              = $contenido;
        $this->restringir_comentarios = $restringir_comentarios;
        $this->likes                  = $likes;
        $this->privacidad             = $privacidad;
        $this->fecha_post             = date('Y-m-d H:i:s');

        // Video
        $this->id_video             = $id_video;
        $this->id_usuario_video     = $id_usuario_video;
        $this->nombre_archivo_video = $nombre_archivo_video;
        $this->ruta_video           = $ruta_video;

        // Imagen
        $this->id_imagen             = $id_imagen;
        $this->id_usuario_imagen     = $id_usuario_imagen;
        $this->nombre_archivo_imagen = $nombre_archivo_imagen;
        $this->ruta_imagen           = $ruta_imagen;
    }
}
?>
