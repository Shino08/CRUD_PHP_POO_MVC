<?php

require_once "./Config/App.php";
require_once "./Autoload.php";
require_once "./App/Views/Inc/SessionStart.php";

if (isset($_GET['views'])) {
    $url = explode("/", $_GET['views']);
} else {
    $url = ['login'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "./App/Views/Inc/Head.php"; ?>
</head>
<body>
    
    <?php 
    use App\Controllers\ViewsControllers;
    use App\Controllers\LoginController;

    $insLogin = new LoginController();

    $viewsController = new viewsControllers();
    $vista = $viewsController->obtenerVistasControlador($url[0]);

    if ($vista == "login" || $vista == "404") {
        require_once "./App/Views/Content/".$vista."-view.php";
    } else {
        require_once "./App/Views/Inc/nav.php";
        require_once $vista;
    }

    require_once "./App/Views/Inc/Script.php"; 
    ?>

</body>
</html>