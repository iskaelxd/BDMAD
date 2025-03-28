<?php

class Usuario {
    public $id_usuario;
    public $nombre;
    public $apellido;
    public $username;
    public $email;
    public $password;
    public $fecha_nacimiento;
    public $cuenta_verificada;
    public $fecha_registro;
    public $avatar;
    public $bio;
    public $privacidad;

    public function __construct($nombre, $apellido, $username, $email, $password, $fecha_nacimiento, $cuenta_verificada = 0, $avatar = null, $bio = null, $privacidad = 'publico') {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->fecha_nacimiento = $fecha_nacimiento;
        $this->cuenta_verificada = $cuenta_verificada;
        $this->avatar = $avatar;
        $this->bio = $bio;
        $this->privacidad = $privacidad;
    }
}


?>