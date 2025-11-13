<?php

namespace App\Controllers;

use App\Models\MainModel;

class UserController extends MainModel {

    # Controlador registrar usuario #
    public function RegistrarUsuarioController() {
        
        #Almacenar datos del formulario #
        $nombre = $this->LimpiarCadena($_POST['usuario_nombre']);
        $apellido = $this->LimpiarCadena($_POST['usuario_apellido']);
        $usuario = $this->LimpiarCadena($_POST['usuario_usuario']);
        $email = $this->LimpiarCadena($_POST['usuario_email']);
        $clave1 = $this->LimpiarCadena($_POST['usuario_clave_1']);
        $clave2 = $this->LimpiarCadena($_POST['usuario_clave_2']);

        # Verificando datos obligatorios #
        if ($nombre == "" || $apellido == "" || $usuario == "" || $clave1 == "" || $clave2 == "") {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "Faltan datos por ingresar",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        #Verificando integridad de datos #

        if ($this->VerificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)) {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "El nombre debe tener al menos 3 caracteres",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }
        if ($this->VerificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)) {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "El apellido debe tener al menos 3 caracteres",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }
        if ($this->VerificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $usuario)) {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "El usuario debe tener al menos 3 caracteres",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }
        if ($this->VerificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $clave1 || $this->VerificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $clave2))) {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "Las claves deben tener al menos 3 caracteres",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }
    }

}