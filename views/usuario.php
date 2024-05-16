<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <?php require '../util/base_de_datos.php' ?>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <video src="imgs/fondo.mp4" autoplay muted></video>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="principal.php"><img src="imgs/logo.png" alt="" height="40px">Good4Game</a>
            <a  class="navbar-brand" href="logIn.php">Iniciar sesion</a>
        </nav>
    </header>
    <?php
    if (($_SERVER["REQUEST_METHOD"]) == "POST") {
        if ($_POST["action"] == "usuarios") {
            //----------------------------Usuario------------------------------------------- 
            $temp_usuario = $_POST["user"];
            $patern_usuario = "/^[a-zA-Z_]+$/";
            if (strlen($temp_usuario) <= 0) {
                $err_usuario = "El nombre debe de estar relleno";
            } else {
                if (strlen($temp_usuario) > 12) {
                    $err_usuario = "El nombre no puede ser mas largo a 12";
                } else {
                    if (!preg_match($patern_usuario, $temp_usuario)) {
                        $err_usuario = "El nombre solo puede tener caracteres simples y barra baja";
                    } else {
                        $usuario = $temp_usuario;
                        $usuario = ucfirst($usuario);
                    }
                }
            }
            //----------------------------Contraseña-------------------------------------------
            $temp_contrasena = $_POST["pass"];
            $patternContrasena = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,20}$/";
            if (strlen($temp_contrasena) <= 0) {
                $err_contrasena = "La contraseña es obligatoria";
            } else {
                if (strlen($temp_contrasena) < 8 || strlen($temp_contrasena) > 20) {
                    $err_contrasena = "La contraseña debe tener mas de 8 y menos de 20 caracteres";
                } else {
                    if (!preg_match($patternContrasena, $temp_contrasena)) {
                        $err_contrasena = "La contraseña debe contenermínimo un carácter en minúscula, uno en mayúscula, un número y un carácter especial";
                    } else {
                        $contrasena = password_hash($temp_contrasena, PASSWORD_DEFAULT);
                    }
                }
            }
            //----------------------------Nacimiento-------------------------------------------
            $temp_fechaNac = $_POST["date"];
            $fecha_actual = new DateTime();
            if (strlen(($temp_fechaNac)) <= 0) {
                $err_fechNac = "La fecha debe de estar rellena";
            } else {

                $temp_fechaNac2 = new DateTime($temp_fechaNac);
                $dif_fech = $fecha_actual->diff($temp_fechaNac2);
                if ($dif_fech->format("%y") < 12 || $dif_fech->format("%y") > 120) {
                    $err_fechNac = "La edad debe de comprender entre 12 y 120 años";
                } else {
                    $fecha_nacimiento = $temp_fechaNac;
                }
            }
            //----------------------------Si todo ok a bdd-------------------------------------------
            if (isset($usuario) && isset($contrasena) && isset($fecha_nacimiento)) {
                require '../util/base_de_datos.php';
                $sql3 = mysqli_query($conexion, "SELECT * FROM usuarios WHERE usuario = '$usuario'");
                if (mysqli_num_rows($sql3) > 0) {
    ?>
                    <div class="alert alert-danger container">El nombre de usuario ya ha sido escogido</div>
    <?php
                } else {
                    $sql = "INSERT INTO usuarios (usuario,contrasena,fechaNacimiento) VALUES ('$usuario','$contrasena', '$fecha_nacimiento')";
                    $sql2 = "INSERT INTO cestas (usuario, precioTotal) VALUES ('$usuario', 0)";
                    $conexion->query($sql);
                    $conexion->query($sql2);
                    header("Location: logIn.php");
                }
            }
        }
    }
    ?>
    <div class="form-group mt-5 registroUsuario">
        <fieldset class="container form-group p-3">
            <legend>Registro</legend>
            <form action="" method="POST" class="form-group">
                <label for="name">Nombre usuario: </label>
                <input type="text" name="user">
                <?php
                if (isset($err_usuario)) {
                    echo "$err_usuario";
                } else {
                }
                ?>
                <br><br>
                <label for="name">Contraseña: </label>
                <input type="password" name="pass">
                <?php
                if (isset($err_contrasena)) {
                    echo "$err_contrasena";
                }
                ?>
                <br><br>
                <label for="name">Fecha de nacimiento: </label>
                <input type="date" name="date">
                <?php
                if (isset($err_fechNac)) {
                    echo "$err_fechNac";
                }
                ?>
                <br><br>
                <input type="hidden" name="action" value="usuarios">
                <input type="submit" value="Registrarse" class="btn btn-success">
            </form>
        </fieldset>
    </div>
</body>
<footer>
    <h1 class="mt-3 titulo"><img src="imgs/logo.png" alt="" height="70px">Good4Game</h1>
</footer>

</html>