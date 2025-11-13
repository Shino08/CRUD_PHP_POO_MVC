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

        # Verificando email#

        if ($email != '') {
            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $check_email= $this->EjecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");

                if ($check_email->rowCount()>0) {
                    $alerta=[
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error",
                        "texto" => "El email ya está registrado",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            }else {
                $alerta=[
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error",
                    "texto" => "El email no es válido",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        }

        #Verificando claves#

        if ($clave1 != $clave2) {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "Las claves no coinciden",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }else {
            $clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost" => 10]);
        }

        # Verificando usuario#

        $check_usuario = $this->EjecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");

        if ($check_usuario->rowCount()>0) {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "El usuario ya está registrado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Directorio de imagenes #

        $img_dir = "../Views/Photos/";

        # Comprobar si se selecciono una imagen #

        if ($_FILES['usuario_foto']['name'] != '' && $_FILES['usuario_foto']['size'] > 0) {
            
            # Creando directorio #

            if (!file_exists($img_dir)) {
                if (!mkdir($img_dir, 0777)) {
                    $alerta=[
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error",
                        "texto" => "Error al crear el directorio",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            }

            if (mime_content_type($_FILES['usuario_foto']['tmp_name']) != 'image/jpeg' && mime_content_type($_FILES['usuario_foto']['tmp_name']) != 'image/png') {
                
                $alerta=[
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error",
                    "texto" => "El archivo no es una imagen",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            # Verificando peso de imagen #

            if (($_FILES['usuario_foto']['size']/1024) > 5120) {
                
                $alerta=[
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error",
                    "texto" => "El archivo es demasiado grande",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

            # Nombre de la imagen #

            $foto = str_ireplace('', '_', $nombre);
            $foto = $foto.'_'.rand(0, 100);
            
            # Extension de la imagen #
            
            switch (mime_content_type($_FILES['usuario_foto']['tmp_name'])) {
                case 'image/jpeg':
                    $foto = $foto.'.jpg';
                    break;

                case 'image/png':
                    $foto = $foto.'.png';
                    break;
            }

            chmod($img_dir, 0777);

            # Moviendo imagen al directorio #

            if(!move_uploaded_file($_FILES['usuario_foto']['tmp_name'], $img_dir.$foto)) {
                $alerta=[
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error",
                    "texto" => "Error al subir la imagen",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }

        } else {
            $foto = '';
        }

        $usuario_datos_reg = [
            [
                "campo_nombre" => "usuario_nombre",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "usuario_apellido",
                "campo_marcador" => ":Apellido",
                "campo_valor" => $apellido
            ],
            [
                "campo_nombre" => "usuario_usuario",
                "campo_marcador" => ":Usuario",
                "campo_valor" => $usuario
            ],
            [
                "campo_nombre" => "usuario_clave",
                "campo_marcador" => ":Clave",
                "campo_valor" => $clave
            ],
            [
                "campo_nombre" => "usuario_foto",
                "campo_marcador" => ":Foto",
                "campo_valor" => $foto
            ],
            [
                "campo_nombre" => "usuario_creado",
                "campo_marcador" => ":Creado",
                "campo_valor" => date('Y-m-d H:i:s')
            ],
            [
                "campo_nombre" => "usuario_actualizado",
                "campo_marcador" => ":Actualizado",
                "campo_valor" => date('Y-m-d H:i:s')
            ],
        ];

        $registrar_usuario = $this->GuardarDatos("usuario", $usuario_datos_reg);

        if($registrar_usuario->rowCount() == 1){
            $alerta=[
                "tipo" => "limpiar",
                "titulo" => "Usuario registrado",
                "texto" => "El usuario ".$nombre. " se registro correctamente",
                "icono" => "success"
            ];

        }else {

            if (is_file($img_dir.$foto)) {
                chmod($img_dir.$foto, 0777);
                unlink($img_dir.$foto);
            }

            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "El usuario no se registro correctamente",
                "icono" => "error"
            ];
        }
        return json_encode($alerta);
        

        
    }

}