<?php

require_once "../../Config/App.php";
require_once "../Views/Inc/SessionStart.php";
require_once "../../Autoload.php";

use App\Controllers\SearchController;

if (isset($_POST['modulo_buscador'])) {

    $inBuscador = new SearchController();
    
    if ($_POST['modulo_buscador'] == "buscar") {
        $inBuscador->IniciarBuscadorController();
    }
    if ($_POST['modulo_buscador'] == "eliminar") {
        $inBuscador->EliminarBuscadorController();
    }

} else {
    session_destroy();
    header("Location: " . APP_URL. "login/");
}
