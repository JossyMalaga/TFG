<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <?php require '../util/base_de_datos.php' ?>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <video src="imgs/fondo.mp4" autoplay muted></video>
    <?php
    session_start();
    if ($_SESSION["rol"] != "admin") {
        header("Location: principal.php");
    }
    ?>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="principal.php"><img src="imgs/logo.png" alt="" height="40px">Good4Game</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </nav>
    </header>
    <?php // ----------------------------Validacion Producto -------------------
    function depurar($entrada)
    {
        $salida = htmlspecialchars($entrada);
        $salida = trim($salida);
        return $salida;
    }
    if (($_SERVER["REQUEST_METHOD"]) == "POST") {
        if ($_POST["action"] == "productos") {
            //---------------------------Nombre--------------------------------
            $nombre_producto = depurar($_POST["name"]);
            $patern_nombre = "/^[a-zA-Z0-9áéíóúÁÉÍÓÚñ ]+$/";
            if (strlen($nombre_producto) <= 0) {
                $err_producto_nombre = "El campo de nombre debe de estar relleno";
            } else {
                if (strlen($nombre_producto) > 40) {
                    $err_producto_nombre = "El nombre debe de tener menos de 40 caracteres";
                } else {
                    if (!preg_match($patern_nombre, $nombre_producto)) {
                        $err_producto_nombre = "El nombre solo puede tener caracteres y espacios en blanco";
                    } else {
                        $nombre = $nombre_producto;
                        $nombre = ucfirst($nombre);
                    }
                }
            }
            //----------------------------Precio-------------------------------------------
            $precio_producto = depurar((float)$_POST["price"]);
            if ($precio_producto == 0) {
                $err_producto_precio = "El precio debe de estar relleno con numeros";
            } else {
                if ($precio_producto > 99999.99 || $precio_producto < 0) {
                    $err_producto_precio = "El precio debe de estar entre 0,1 € y 99999,99 €";
                } else {
                    if (!is_numeric($precio_producto)) {
                        $err_producto_precio = "Deja de intentar cositas";
                    } else {
                        $precio = $precio_producto;
                    }
                }
            }
            //----------------------------Descripcion-------------------------------------------
            $descripcion_producto = depurar($_POST["description"]);
            if (strlen($descripcion_producto) <= 0) {
                $err_producto_descripcion = "La descripcion debe de estar rellena";
            } else {
                if (strlen($descripcion_producto) > 255) {
                    $err_producto_descripcion = "La descripcion no puede tener mas de 255 caracteres";
                } else {
                    if (!preg_match($patern_nombre, $descripcion_producto)) {
                        $err_producto_descripcion = "¿Que intentas?";
                    } else {
                        $descripcion = $descripcion_producto;
                        $descripcion = ucfirst($descripcion);
                    }
                }
            }
            //----------------------------Cantidad-------------------------------------------
            $cantidad_producto = depurar($_POST["cantidad"]);
            if (strlen($cantidad_producto) <= 0) {
                $err_producto_cantidad = "Minimo debe de añadirse un producto";
            } else {
                if (((int)($cantidad_producto)) > 9999999) {
                    $err_producto_cantidad = "No puedes introducir más de 9999999 productos";
                } else {
                    if (!is_numeric($cantidad_producto)) {
                        $err_producto_cantidad = "Deja de intentar cositas";
                    } else {
                        $cantidad = $cantidad_producto;
                    }
                }
            }
            // -----------------------------------Foto---------------------------------
            $ruta_imagen = $_FILES["imagen"]["tmp_name"];
            $nombre_imagen = $_FILES["imagen"]["name"];
            //nos lo trae en bytes y hay que pasarlo a MB
            $peso_imagen = $_FILES["imagen"]["size"];
            $peso_imagenMB = $peso_imagen / (1024 * 1024);
            $patern_imagen = "/^(.*\.jpg)|(.*\.jpeg)|(.*\.png)$/";
            if (strlen($ruta_imagen) <= 0) {
                $err_imagen = "No se ha subido una foto de producto";
            } else {
                if (!preg_match($patern_imagen, $nombre_imagen)) {
                    $err_imagen = "La foto ha de ser formato .png o .jpg o .jpeg";
                } else {
                    if ($peso_imagenMB > 1) {
                        $err_imagen = "No puede tener un peso superior a 5MB";
                    } else {
                        $ruta_final = "imgs/" . $nombre_imagen;
                        move_uploaded_file($ruta_imagen, $ruta_final);
                    }
                }
            }
            //----------------------------si todo ok a bdd---------------------------------
            if (isset($nombre) && isset($precio) && isset($descripcion) && isset($cantidad) && isset($ruta_final)) {
                require '../util/base_de_datos.php';
                $sql = "INSERT INTO productos (nombreProducto, precio, descripcion, cantidad, imagen) VALUES ('$nombre','$precio', '$descripcion', '$cantidad','$ruta_final')";
                $conexion->query($sql);
                $enviado = "El producto se ha subido correctamente";
                header("Location: principal.php");
            }
        }
    }
    ?>
    <!----------------------------Formulario Producto------------------------->
    <div class="form-group  mt-5">
        <fieldset id="registroProducto" class="container form-group">
            <legend>Productos</legend>
            <form action="" method="POST" class="form-group" enctype="multipart/form-data">
                <label for="name">Nombre</label>
                <input type="text" name="name">
                <?php
                if (isset($err_producto_nombre)) {
                    echo "$err_producto_nombre";
                } else {
                }
                ?>
                <br><br>
                <label for="name">Precio</label>
                <input type="text" name="price">
                <?php
                if (isset($err_producto_precio)) {
                    echo "$err_producto_precio";
                }
                ?>
                <br><br>
                <label for="name">Descripcion</label>
                <input type="text" name="description">
                <?php
                if (isset($err_producto_descripcion)) {
                    echo "$err_producto_descripcion";
                }
                ?>
                <br><br>
                <label for="cantidad">cantidad</label>
                <input type="text" name="cantidad">

                <?php
                if (isset($err_producto_cantidad)) {
                    echo "$err_producto_cantidad";
                }
                ?>
                <br><br>
                <label class="form-label">Imagen</label>
                <input type="file" name="imagen" class="form-control">
                <br><br>
                <?php
                if (isset($err_imagen)) {
                    echo "$err_imagen";
                }
                ?>
                <br><br>
                <input type="hidden" name="action" value="productos">
                <input type="submit" value="Añadir el producto">
                <?php
                if (isset($enviado)) echo $enviado;
                ?>
            </form>
        </fieldset>
    </div>

</body>
<footer>
     <h1 class="mt-3 titulo"><img src="imgs/logo.png" alt="" height="70px">Good4Game</h1>
</footer>

</html>