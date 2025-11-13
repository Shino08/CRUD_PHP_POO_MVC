<?php

    namespace App\Models;
    use \PDO;

    if (file_exists(__DIR__."/../../Config/Server.php")) {
       require_once __DIR__."/../../Config/Server.php";
    }

class MainModel{

    private $server = DB_SERVER;
    private $db = DB_NAME;
    private $user = DB_USER;
    private $pass = DB_PASS;

    protected function Conectar(){
        $conexion = new PDO("mysql:host=".$this->server.";dbname=".$this->db, $this->user, $this->pass);
        $conexion->exec(("SET CHARACTER SET utf8"));
        return $conexion;
    }

    protected function EjecutarConsulta($consulta) {
        $sql = $this->Conectar()->prepare($consulta);
        $sql->execute();
        return $sql;
    }

    public function LimpiarCadena($cadena){
        $palabras = ["<script>", "</script>", "<script src>", "<script type=>", "SELECT * FROM", "INSERT INTO", "DELETE FROM", "DROP TABLE", "DROP DATABASE", "TRUNCATE TABLE", "SHOW TABLES", "SHOW DATABASES", "<?php", "?>", "--", "^", "<", ">", "==", "=", ";", "::"];
        
        $cadena = trim($cadena);
        $cadena = stripslashes($cadena);
        $cadena = htmlspecialchars($cadena);

        foreach ($palabras as $palabra) {
            $cadena = str_ireplace($palabra, "", $cadena);
                        
        }
        $cadena = trim($cadena);
        $cadena = stripslashes($cadena);
        $cadena = htmlspecialchars($cadena);

        return $cadena;
    }

    protected function VerificarDatos($filtro, $cadena){
        if (preg_match("/^".$filtro."$/", $cadena)) {
            return false;
        } else {
            return true;
        }
    }

    protected function GuardarDatos($tabla, $datos){
        $query = "INSERT INTO $tabla VALUES (";

        $C=0;
        foreach ($datos as $clave) {
            if ($C>=1) { $query.=","; }
            $query.=$clave["campo_nombre"];
            $C++;
        }

        $query.=") VALUES (";

        $C=0;
        foreach ($datos as $clave) {
            if ($C>=1) { $query.=","; }
            $query.=$clave["campo_marcador"];
            $C++;
        }

        $query.=")";

        $sql = $this->Conectar()->prepare($query);

        foreach ($datos as $clave) {
            $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
        }

        $sql->execute();

        return $sql;
    }

    public function SeleccionarDatos($tipo, $tabla, $campo, $id){
        $tipo = $this->LimpiarCadena($tipo);
        $tabla = $this->LimpiarCadena($tabla);
        $campo = $this->LimpiarCadena($campo);
        $id = $this->LimpiarCadena($id);

        if ($tipo == "Unico") {
            $sql = $this->Conectar()->prepare("SELECT * FROM $tabla WHERE $campo=:ID");
            $sql->bindParam(":ID", $id);
            $sql->execute();
            return $sql;
        } elseif($tipo == "Normal") {
            $sql = $this->Conectar()->prepare("SELECT $campo FROM $tabla");
            $sql->execute();
            return $sql;
        }
        
    }

    protected function ActualizarDatos($tabla, $datos, $condicion){
        $query = "UPDATE $tabla SET ";

        $C=0;
        foreach ($datos as $clave) {
            if ($C>=1) { $query.=" , "; }
            $query.=$clave["campo_nombre"]."=".$clave["campo_marcador"];
            $C++;
        }

        $query.=" WHERE " .$condicion["condicion_campo"] ."=".$condicion["condicion_marcador"];

        $sql = $this->Conectar()->prepare($query);

        foreach ($datos as $clave) {
            $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
        }

        $sql->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);

        $sql->execute();

        return $sql;
    }

    protected function EliminarRegistro($tabla, $campo, $id){
        $sql = $this->Conectar()->prepare("DELETE FROM $tabla WHERE $campo=:id");
        $sql->bindParam(":id", $id);
        $sql->execute();
        return $sql;
    }

    protected function PaginadorTablas($pagina, $numeroPagina, $url, $botones){
        $tabla = '<nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">';
        
        if ($pagina<=1) {
            $tabla.='
            <a class="pagination-previous is-disabled" disabled>Anterior</a>

            <ul class="pagination-list">
            ';
        } else {
            $tabla.='
            <a class="pagination-previous" href="'.$url.($pagina-1).'/">Anterior</a>
            <ul class="pagination-list">
            <li>
                <a class="pagination-link" href="'.$url.'1/">1</a>
            </li>
            <li><span class="pagination-ellipsis">&hellip;</span></li>
            ';
        }

        $ci = 0;
        for ($i=$pagina; $i<=$numeroPagina; $i++){

            if ($ci >= $botones) {
                break;
            }

            if ($pagina == $i) {
                $tabla.='<li>
                <a class="pagination-link is-current" href="'.$url.$i.'/">'.$i.'</a>
            </li>';
            } else {
                $tabla.='<li>
                <a class="pagination-link" href="'.$url.$i.'/">'.$i.'</a>
            </li>';
            }

            $ci++;
             
        }

        if ($pagina == $numeroPagina) {
            $tabla.='
            </ul>
            <a class="pagination-next is-disabled" disabled>Siguiente</a>
            </nav>
            ';
        } else {
            $tabla.='
            <li><span class="pagination-ellipsis">&hellip;</span></li>
            <li>
                <a class="pagination-link" href="'.$url.$numeroPagina.'/">'.$numeroPagina.'</a>
            </li>
            </ul>
            <a class="pagination-next" href="'.$url.($pagina+1).'/">Siguiente</a>
            </nav>
            ';
        }
        
        $tabla .= "</ul></nav>";
        
        return $tabla;
    }
}