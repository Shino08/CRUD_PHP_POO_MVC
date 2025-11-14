<div class="container is-fluid">
    <h1 class="title">Dashboard</h1>
    <div class="columns is-flex is-justify-content-center">
        <figure class="image is-128x128">
            <?php
                if (is_file("./App/Views/Photos/".$_SESSION['foto'])) {
                    echo '<img class="is-rounded" src="'.APP_URL.'App/Views/Photos/'.$_SESSION['foto'].'">';
                } else {
                    echo '<img class="is-rounded" src="'.APP_URL.'App/Views/Photos/default.png">';
                }
                
            ?>
        </figure> 
    </div>
    <div class="columns is-flex is-justify-content-center">
        <h2 class="subtitle">Bienvenido <?php echo $_SESSION['nombre']; ?></h2>
    </div>
</div>    