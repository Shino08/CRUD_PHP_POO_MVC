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
} else {
    session_destroy();
    header("Location: " . APP_URL. "login/");
}
