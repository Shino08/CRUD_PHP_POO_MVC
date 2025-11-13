<?php

namespace App\Controllers;
use App\Models\ViewsModel;

class ViewsControllers extends ViewsModel{
   
    public function obtenerVistasControlador($vista){

        if ($vista != '') {
            $respuesta = $this->obtenerVistasModelo($vista);
        } else {
            $respuesta = "login";
        }

        return $respuesta;
        
    }
}