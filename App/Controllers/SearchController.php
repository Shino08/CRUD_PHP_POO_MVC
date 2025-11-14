<?php

namespace App\Controllers;
use APP\Models\MainModel;

class SearchController extends MainModel {

    # Controlador de lo modulos de busqueda

    public function ModulosBusquedaController($modulo) {
        
        $listaModulos = ['userSearch'];

        if (in_array($modulo, $listaModulos)) {
            return false;
        } else {
            return true;
        }
    }

    # Iniciar busqueda
    public function IniciarBuscadorController() {
        
        $url = $this->LimpiarCadena($_POST['modulo_url']);
        $texto = $this->LimpiarCadena($_POST['txt_buscador']);

        if ($this->ModulosBusquedaController($url)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "Modulo no encontrado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        if ($texto == '') {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "No se puede buscar un campo vacio",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        if ($this->VerificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}", $texto)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "El texto debe contener solo letras y espacios",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        $_SESSION[$url] = $texto;

        $alerta = [
            "tipo" => "redireccionar",
            "url" => APP_URL . $url . "/"
        ];
        return json_encode($alerta);
        
    }

    # Controlador de eliminar busqueda
    public function EliminarBuscadorController() {
     
        $url = $this->LimpiarCadena($_POST['modulo_url']);

        if ($this->ModulosBusquedaController($url)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "Modulo no encontrado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        unset($_SESSION[$url]);

        $alerta = [
            "tipo" => "redireccionar",
            "url" => APP_URL . $url . "/"
        ];
        return json_encode($alerta);

    }
    
}