<?php
    require_once "config.php";

    session_start();

    // Si no hay una sesión logueada, lo redirigimos al login
    if (!isset($_SESSION["username"])) {
        header("location:index.php");
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home · Bienvenidos a nuestra App!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>

    <div class="container  mt-5 align-middle">
        
        <h4>Bienvenidos a nuestra App!</h4>
        <p>Hola, <?php echo($_SESSION["username"]) ?><br/>
            <a href="logout.php" class="btn btn-sm btn-primary mt-3">Cerrar sesión</a>
        </p>
 
        <div class="row">

            <?php

                $query = "SELECT * FROM users";
                $result = mysqli_query($connection, $query);

                if(mysqli_num_rows($result) > 0) {

                    while($row = mysqli_fetch_assoc($result)) {
                        echo '
                        <div class="col-md-4 my-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">'. $row["username"] .'</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">ID: '. $row["id"] .'</h6>
                                    <p class="card-text">Aquí estaría bien un texto de presentación acerca del usuario.</p>
                                    <a href="#" class="card-link">LinkedIn</a>
                                    <a href="#" class="card-link">Instagram</a>
                                </div>
                            </div>
                        </div>';
                    }                           
                }
            ?>            
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>