<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <?php require '../util/base_de_datos.php' ?>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <video src="imgs/fondo.mp4" autoplay muted></video>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="principal.php"><img src="imgs/logo.png" alt="" height="40px">Good4Game</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-item nav-link" href="usuario.php">¿No eres usuario? Registrarse</a>
                </div>
            </div>
        </nav>
    </header>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usuario = $_POST["usuario"];
        $contrasena = $_POST["contrasena"];
        //hemos traido las variables
        $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
        $res = $conexion->query($sql);
        //buscamos al usuario con su nombre si devuelve 0 es false y si no entra en el if
        if ($res->num_rows === 0) {
            //si no encuentra res
    ?>
            <div class="container mt-4">
                <div class="container mt-4 alert alert-warning">No existe el usuario</div>
            </div>

            <?php
        } else {
            //recorremos todas las filas si encuentra con fetch_assoc()
            while ($fila = $res->fetch_assoc()) {
                //sacamos el campo contrasena de la $filas (en este caso una porque el nombre es PK)
                $pasword_cifrada = $fila["contrasena"];
                $rol = $fila["rol"];
            }
            //creamos un boolean con el return de esta funcion que nos compara contraseña incluso con los hash
            $acceso_valido = password_verify($contrasena, $pasword_cifrada);
            if ($acceso_valido) {
                //si las contraseñas coinciden lo llevamos a la pagina de inicio y en $_sesion guardamos en el campo usuario el usuario de la sesion.
                session_start();
                $_SESSION["usuario"] = $usuario;
                $_SESSION["rol"] = $rol;
                header('location: principal.php');
            } else {
                //si no coinciden las pass damos la notificacion
            ?>
                <div class="container mt-4">
                    <div class="alert alert-danger">No coinciden las contraseñas</div>
                </div>
    <?php
            }
        }
    }
    ?>
    <div  id="login" class="container registroUsuario mt-5">
        <h1>LogIn</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Usuario: </label>
                <input class="form-control" type="text" name="usuario">

            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña: </label>
                <input class="form-control" type="password" name="contrasena">

            </div>
            <input class="btn btn-primary" type="submit" value="LogIn">
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>
<footer>
     <h1 class="mt-3 titulo"><img src="imgs/logo.png" alt="" height="70px">Good4Game</h1>
</footer>

</html>