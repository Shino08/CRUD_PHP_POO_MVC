<?php

require_once "../../Config/App.php";
require_once "../Views/Inc/SessionStart.php";
require_once "../../Autoload.php";

use App\Controllers\UserController;

if (isset($_POST['modulo_usuario'])) {
    $inUsuario = new UserController();
    
    if($_POST['modulo_usuario'] == "registrar") {
       echo $inUsuario->RegistrarUsuarioController();
    }
    if($_POST['modulo_usuario'] == "eliminar") {
       echo $inUsuario->EliminarUsuarioController();
    }
    if($_POST['modulo_usuario'] == "actualizar") {
       echo $inUsuario->ActualizarUsuarioController();
    }
    if($_POST['modulo_usuario'] == "actualizarFoto") {
       echo $inUsuario->ActualizarFotoUsuarioController();
    }
    if($_POST['modulo_usuario'] == "eliminarFoto") {
       echo $inUsuario->EliminarFotoUsuarioController();
    }

} else {
    session_destroy();
    header("Location: " . APP_URL. "login/");
}
