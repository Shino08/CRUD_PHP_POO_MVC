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

    # Controlador lista de usuarios #

    public function listaUsuarios($pagina, $registros, $url, $busqueda){
     
        $pagina = $this->LimpiarCadena($pagina);
        $registros = $this->LimpiarCadena($registros);

        $url = $this->LimpiarCadena($url);
        $url = APP_URL.$url.'/';

        $busqueda = $this->LimpiarCadena($busqueda);
        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1; 
        $inicio = ($pagina > 1) ? ($pagina * $registros) - $registros : 0;

        if (isset($busqueda) && $busqueda != '') {

            $consulta_datos = "SELECT * FROM usuario WHERE 
            ((usuario_id!='".$_SESSION['id']."' AND usuario_id!='1') AND (usuario_nombre LIKE '%$busqueda%' OR usuario_apellido LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%')) ORDER BY usuario_nombre ASC LIMIT $inicio, $registros";

            $consulta_total = "SELECT * FROM usuario WHERE 
            ((usuario_id!='".$_SESSION['id']."' AND usuario_id!='1') AND (usuario_nombre LIKE '%$busqueda%' OR usuario_apellido LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%')) ";
            
        } else {
            $consulta_datos = "SELECT * FROM usuario WHERE 
            usuario_id!='".$_SESSION['id']."' AND usuario_id!='1' ORDER BY usuario_nombre ASC LIMIT $inicio, $registros";

            $consulta_total = "SELECT COUNT(usuario_id) FROM usuario WHERE usuario_id!='".$_SESSION['id']."' AND usuario_id!='1'";
        }

        $datos = $this->EjecutarConsulta($consulta_datos);
        $total = $datos->fetchAll();

        $total = $this->EjecutarConsulta($consulta_total);
        $total = (int) $total->fetchColumn();

        $numeroPaginas = ceil($total/$registros); 

        $tabla.='
            <div class="table-container">
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
            <thead>
                <tr>
                    <th class="has-text-centered">#</th>
                    <th class="has-text-centered">Nombre</th>
                    <th class="has-text-centered">Usuario</th>
                    <th class="has-text-centered">Email</th>
                    <th class="has-text-centered">Creado</th>
                    <th class="has-text-centered">Actualizado</th>
                    <th class="has-text-centered" colspan="3">Opciones</th>
                </tr>
            </thead>
            <tbody>
        ';

        if ($total >= 1 && $pagina <= $numeroPaginas) {
            
            $contador = $inicio + 1;
            $pag_inicio = $inicio + 1;

            foreach($datos as $rows){
                $tabla.='
                <tr class="has-text-centered">
					<td>'.$contador.'</td>
					<td>'.$rows['usuario_nombre'].' '.$rows['usuario_apellido'].'</td>
					<td>'.$rows['usuario_usuario'].'</td>
					<td>'.$rows['usuario_email'].'</td>
					<td>'.date("d-m-Y h:i:s", strtotime($rows['usuario_creado'])).'</td>
					<td>'.date("d-m-Y h:i:s", strtotime($rows['usuario_actualizado'])).'</td>
					<td>
	                    <a href="'.APP_URL.'userPhoto/'.$rows['usuario_id'].'" class="button is-info is-rounded is-small">Foto</a>
	                </td>
	                <td>
                    <a href="'.APP_URL.'userUpdate/'.$rows['usuario_id'].'" class="button is-success is-rounded is-small">Actualizar</a>
                </td>
                <td>
                	<form class="FormularioAjax" action="'.APP_URL.'App/Ajax/usuarioAjax.php" method="POST" autocomplete="off">

                		<input type="hidden" name="modulo_usuario" value="eliminar">
                		<input type="hidden" name="usuario_id" value="'.$rows['usuario_id'].'">

                    	<button type="submit" class="button is-danger is-rounded is-small">Eliminar</button>
	                    </form>
	                </td>
				</tr>
                ';
                $contador++;
            }
            $pag_final = $contador - 1;
        } else {
            if ($total >= 1) {
                $tabla.='
                <tr class="has-text-centered" >
	                <td colspan="7">
	                    <a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">
	                        Haga clic acá para recargar el listado
	                    </a>
	                </td>
	            </tr>
                ';
            } else {
                $tabla.='
                <tr class="has-text-centered" >
                    <td colspan="7">
                        No hay registros en el sistema
                    </td>
                </tr>
                ';
            }
            
        }
        

        $tabla.='
            </tbody></table></div>
        ' ;
        
        if ($total >= 1 && $pagina <= $numeroPaginas) {
            $tabla.='<p class="has-text-right">Mostrando usuarios <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';

            $tabla.=$this->PaginadorTablas($pagina, $numeroPaginas, $url, 10);
        }

        return $tabla;
    }

    # Controlador eliminar usuario #

    public function EliminarUsuarioController(){
        
        $id = $this->LimpiarCadena($_POST['usuario_id']);

        if ($id == 1) {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "No se puede eliminar el usuario",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        #Verficar Usuario #

        $datos = $this->EjecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");

        if ($datos->rowCount()<= 0) {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "No hemos encontrado el usuario",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        } else {
            $datos = $datos->fetch();
        }

        #Eliminar Usuario #
        
        $eliminar_usuario = $this->EliminarRegistro("usuario", "usuario_id", $id);

        if ($eliminar_usuario->rowCount() == 1) {

            if (is_file("../Views/Photos/".$datos['usuario_foto'])) {
                chmod("../Views/Photos/".$datos['usuario_foto'], 0777);
                unlink("../Views/Photos/".$datos['usuario_foto']);
            }

            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "El usuario". $datos['usuario_nombre']. " ". $datos['usuario_apellido']. " se elimino correctamente",
                "icono" => "success"
            ];
        } else {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "El usuario". $datos['usuario_nombre']. " ". $datos['usuario_apellido']. " no se elimino correctamente",
                "icono" => "error"
            ];
        }
        
        return json_encode($alerta);
        
    }

    # Controlador actualizar usuario #

    public function ActualizarUsuarioController(){

        $id = $this->LimpiarCadena($_POST['usuario_id']);

        $datos = $this->EjecutarConsulta("SELECT * FROM usuario WHERE usuario_id='$id'");
        if ($datos->rowCount()<= 0) {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "No hemos encontrado el usuario",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        } else {
            $datos = $datos->fetch();
        }

        $admin_usuario = $this->LimpiarCadena($_POST['admin_usuario']);

        $admin_clave = $this->LimpiarCadena($_POST['admin_clave']);

        # Verificar usuario y clave #

        if ($admin_usuario != '' || $admin_clave != '') {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "El usuario o clave no coinciden",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        # Verificando integridad de datos #

        if ($this->VerificarDatos("[a-zA-Z0-9]{4,20}", $admin_usuario)) {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "El usuario no coinciden",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        if ($this->VerificarDatos("[a-zA-Z0-9$@.-]{7,100}", $admin_clave)) {
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "La clave no coinciden",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }
        
        # Verificar datos del usuario #

        $check_admin = $this->EjecutarConsulta("SELECT * FROM usuario WHERE usuario_id='".$_SESSION['id']."'");

        if ($check_admin->rowCount() == 1) {
            $check_admin = $check_admin->fetch();

            if ($check_admin['usario_usuario'] != $admin_usuario || !password_verify($admin_clave, $check_admin['usuario_clave'])) {
                $alerta=[
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error",
                    "texto" => "El usuario o clave no coinciden",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        }else{
            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "El usuario o clave no coinciden",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        #Almacenar datos del formulario #
        $nombre = $this->LimpiarCadena($_POST['usuario_nombre']);
        $apellido = $this->LimpiarCadena($_POST['usuario_apellido']);
        $usuario = $this->LimpiarCadena($_POST['usuario_usuario']);
        $email = $this->LimpiarCadena($_POST['usuario_email']);
        $clave1 = $this->LimpiarCadena($_POST['usuario_clave_1']);
        $clave2 = $this->LimpiarCadena($_POST['usuario_clave_2']);

        # Verificando datos obligatorios #
        if ($nombre == "" || $apellido == "" || $usuario == "") {
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

        # Verificando email#

        if ($email != '' && $datos['usuario_email'] != $email) {
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

        if ($clave1 != '' || $clave2 != '') {

            if ($this->VerificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave1) || $this->VerificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave2)) {

            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "Las claves deben tener al menos 7 caracteres",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }else {
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
        }

        }else {
            $clave = $datos['usuario_clave'];
        }

        # Verificando usuario#
        if ($datos['usuario_usuario'] != $usuario) {
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
            
        }

                $usuario_datos_up = [
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
                "campo_nombre" => "usuario_actualizado",
                "campo_marcador" => ":Actualizado",
                "campo_valor" => date('Y-m-d H:i:s')
            ],
        ];

        $condicion = [
            "condicion_campo" => "usuario_id",
            "condicion_marcador" => ":ID",
            "condicion_valor" => $id
        ];

                if($this->ActualizarDatos("usuario", $usuario_datos_up, $condicion)){

                    if ($id == $_SESSION['id']) {
                        $_SESSION['nombre'] = $nombre;
                        $_SESSION['apellido'] = $apellido;
                        $_SESSION['usuario'] = $usuario;

                    }
            $alerta=[
                "tipo" => "recargar",
                "titulo" => "Usuario actualizado",
                "texto" => "El usuario ".$datos['usuario_nombre']. " ".$datos['usuario_apellido']. " se actualizó correctamente",
                "icono" => "success"
            ];

        }else {

            $alerta=[
                "tipo" => "simple",
                "titulo" => "Ocurrió un error",
                "texto" => "El usuario no se pudo actualizar correctamente",
                "icono" => "error"
            ];
        }
        return json_encode($alerta);
        
    }

}