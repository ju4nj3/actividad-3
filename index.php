<?php

    require_once "config.php";

    session_start();

    if (isset($_SESSION["username"])) {
        header("location:home.php");
    }

    if(isset($_POST["login"])) {

        $loginError = false;

        $username = mysqli_real_escape_string($connection, $_POST["username"]);
        $password = mysqli_real_escape_string($connection, $_POST["password"]);    

        // Parametrizamos la consulta para evitar que hayan inyecciones de SQL
        $query = "SELECT * FROM users WHERE username = ?";

        // Preparamos la consulta SQL
        $sentenciaSQL = $connection->prepare($query);

        if ($sentenciaSQL === false) {
            die("Error preparando sentencia SQL: " . $connection->error);
        }

        // Asignamos parámetros
        $sentenciaSQL->bind_param("s", $username);

        // Ejecutamos consulta
        $sentenciaSQL->execute();

        // Obtenemos resultados
        $result = $sentenciaSQL->get_result();

        if(mysqli_num_rows($result) > 0) {

            $row = $result->fetch_assoc();

            if(password_verify($password, $row["password"])) {
                $_SESSION["username"] = $username;
                header("location:home.php");
                exit;
        }         
        } else {
            $loginError = true;
        }

        $sentenciaSQL->close();
    }
    else if(isset($_POST["register"])) {

        $registerError = false;
        $registroFinalizado=false;        
        $mensajeError = "";

        if(empty($_POST["username"]) || empty($_POST["password1"]) || empty($_POST["password2"])) {
            $registerError = true;
            $mensajeError = "Datos de registro incorrectos!";
        } 
        else if($_POST["password1"]!=$_POST["password2"]) {
            $registerError=true;
            $mensajeError = "Las contraseñas no coinciden!";
        }
        else {
            $username = mysqli_real_escape_string($connection, $_POST["username"]);
            $password = mysqli_real_escape_string($connection, $_POST["password1"]);

            $password = password_hash($password, PASSWORD_DEFAULT);
            
            // Parametrizamos la consulta para evitar que hayan inyecciones de SQL
            $query = "INSERT INTO users (username, password) VALUES (?, ?)";

            // Preparamos la consulta SQL
            $sentenciaSQL = $connection->prepare($query);

            if ($sentenciaSQL === false) {
                die("Error preparando sentencia SQL: " . $connection->error);
            }

            // Asignamos parámetros, ss significa que ambos parámetros son cadenas
            $sentenciaSQL->bind_param("ss", $username, $password);

            // Ejecutamos la consulta
            if ($sentenciaSQL->execute()) {
                $registroFinalizado = true;
            } else {
                $registerError=true;
                $mensajeError = "Error insertando registro en la base de datos" . $sentenciaSQL->error;
            }

            $sentenciaSQL->close();
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo(isset($_GET["action"])=="register" ? "Registro" : "Login") ?></title>   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">    
    <?php
    // Incluimos los alerts en el <head>, dado que no pueden estar fuera del <html>
    if($loginError) {
        echo '<script>alert("Error en el login!")</script>';
    }
    else if($registerError) {
        echo '<script>alert("'. $mensajeError .'")</script>';
    }
    else if($registroFinalizado) {
        // Dado que queremos mostrar un alert y redirigir al usuario a login.php, el alert no puede estar fuera del <html> y header() no funciona dentro de <html>
        // Lo hacemos con Javascript
        echo '<script>alert("Registro finalizado!"); window.location.href = "index.php";</script>';
    }
    ?>
</head>

<body>

    <div class="container mt-5">

        <?php

        if(isset($_GET["action"])=="register") 
        {
        ?>
            <form method="POST">
                <div class="row">
                    <div class="offset-4 col-md-4 text-center mb-3">
                        <h4>Registro</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-4 col-md-4 mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" id="username" name="username" class="form-control" maxlength="32" aria-describedby="ayudaUsername" required>
                        <div id="ayudaUsername" class="form-text">Tu nombre de usuario</div>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-4 col-md-4 mb-3">
                        <label for="password1" class="form-label">Contraseña</label>
                        <input type="password" id="password1" name="password1" class="form-control" maxlength="64" required>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-4 col-md-4 mb-3">
                        <label for="password2" class="form-label">Contraseña</label>
                        <input type="password" id="password2" name="password2" class="form-control" maxlength="64" required>
                    </div>
                </div>                
                <div class="row">
                    <div class="offset-4 col-md-4 text-center mb-3">
                        <input type="submit" id="register" name="register" class="btn btn-primary btn-block" value="Registro">                        
                    </div>
                </div>
                <div class="row">
                    <div class="offset-4 col-md-4 text-center">
                        <span>¿Ya estás registrado? <a href="index.php">Accede</a></span>
                    </div>
                </div>
            </form>
        <?php
        } 
        else
        {
        ?>
            <form method="POST">
            <div class="row">
                <div class="offset-4 col-md-4 text-center mb-3">
                    <h4>Login</h4>
                </div>
            </div>
            <div class="row">
                <div class="offset-4 col-md-4 mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" id="username" name="username" class="form-control" maxlength="32" aria-describedby="ayudaUsername" required>
                    <div id="ayudaUsername" class="form-text">Tu nombre de usuario</div>
                </div>
            </div>
            <div class="row">
                <div class="offset-4 col-md-4 mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" maxlength="64" required>
                </div>
            </div>
            <div class="row">
                <div class="offset-4 col-md-4 mb-3">
                    <div class="form-check">
                        <input type="checkbox" id="recordar" name="recordar" class="form-check-input">
                        <label class="form-check-label" for="recordar">Recordar acceso</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="offset-4 col-md-4 text-center mb-3">
                    <input type="submit" id="login" name="login" class="btn btn-primary" value="Acceder">                        
                </div>
            </div>
            <div class="row">
                <div class="offset-4 col-md-4 text-center">
                    <span>¿No estás registrado? <a href="index.php?action=register">Regístrate</a></span>
                </div>
            </div>
        </form>
        <?php
        }
        ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>