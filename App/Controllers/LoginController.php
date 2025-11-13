<?php

namespace App\Controllers;
use App\Models\MainModel;

class LoginController extends MainModel {

    #Controlador iniciar sesion#
    public function IniciarSesionController() {
     
        #Almacenar datos del formulario#

        $usuario = $this->LimpiarCadena($_POST['login_usuario']);
        $clave = $this->LimpiarCadena($_POST['login_clave']);

        #Verifiacando campos obligatorios#

        if ($usuario == '' || $clave == '') {
            echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Faltan datos por ingresar',
                    text: 'No se han proporcionado todos los datos',
                    confirmButtonText: 'Aceptar'
                });
            </script>
            ";
        }else{
            # Verificando integridad de los datos #

            if ($this->VerificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
                echo "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Usuario no válido',
                        text: 'El usuario debe tener al menos 4 caracteres',
                        confirmButtonText: 'Aceptar'
                    });
                </script>
                ";
            } else {
                if ($this->VerificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave)) {
                    echo "
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Clave no válida',
                            text: 'La clave debe tener al menos 7 caracteres',
                            confirmButtonText: 'Aceptar'
                        });
                    </script>
                    ";
                } else {
                    
                    # Verificando usuario #

                    $check_usuario = $this->EjecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario='$usuario'");

                    if ($check_usuario->rowCount() == 1) {

                        $check_usuario = $check_usuario->fetch();

                        if ($check_usuario['usuario_usuario'] == $usuario && password_verify($clave, $check_usuario['usuario_clave'])) {

                            $_SESSION['id'] = $check_usuario['usuario_id'];
                            $_SESSION['nombre'] = $check_usuario['usuario_nombre'];
                            $_SESSION['apellido'] = $check_usuario['usuario_apellido'];
                            $_SESSION['usuario'] = $check_usuario['usuario_usuario'];
                            $_SESSION['foto'] = $check_usuario['usuario_foto'];

                            if (headers_sent()) {
                                echo "
                                <script>
                                    window.location.href = '".APP_URL."dashboard/';
                                </script>
                                ";
                            } else {
                                header("Location: ".APP_URL."dashboard/");
                            }
                            
                            
                        } else {
                            echo "
                            <script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Usuario no válido',
                                    text: 'Usuario o clave incorrectos',
                                    confirmButtonText: 'Aceptar'
                                });
                            </script>
                        ";
                        }
                        
                        
                    } else {
                        echo "
                            <script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Usuario no válido',
                                    text: 'Usuario o clave incorrectos',
                                    confirmButtonText: 'Aceptar'
                                });
                            </script>
                        ";
                    }
                    
                }
            }
            
        }

    }

    #Controlador cerrar sesion#
    public function CerrarSesionController() {
        session_destroy();
        
        if (headers_sent()) {
            echo "
            <script>
                window.location.href = '".APP_URL."login/';
            </script>
            ";
        } else {
            header("Location: ".APP_URL."login/");
        }
    }
}