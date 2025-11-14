<?php
    namespace App\Models;

    class ViewsModel {
        protected function obtenerVistasModelo($vista){
            $listaBlanca = ["dashboard","userNew","userList","userSearch", "userUpdate", "userPhoto", "logOut"];

            if (in_array($vista, $listaBlanca)) {
                if (is_file("./App/Views/Content/".$vista."-view.php")) {
                    $contenido = "./App/Views/Content/".$vista."-view.php";
                } else {
                    $contenido = "404";
                }
                
            } elseif($vista == "login" || $vista == "index"){
                $contenido = "login";
            }else{
                $contenido = "404";
                
            }
            return $contenido;
        }
    }