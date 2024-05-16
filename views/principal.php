<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <?php require '../util/base_de_datos.php';
    require '../util/productoObj.php'; ?>


</head>

<body class="responsive">
    <video src="imgs/fondo.mp4" autoplay muted></video>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="principal.php"><img src="imgs/logo.png" alt="" height="40px">Good4Game</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                        <?php
                        session_start();
                        if (!isset($_SESSION["usuario"])) {
                            $_SESSION["usuario"] = "invitado";
                        }
                        $usuario = $_SESSION["usuario"];
                        ?>
                        <a class="nav-item nav-link" href="#">Bienvenid@ <?php echo $usuario ?> </a>
                        <?php

                        //comprobamos si usuario esta vacio si es asi lo iniciamos como invitado
                        if ($_SESSION["usuario"] == '') {
                            $_SESSION["usuario"] = "invitado";
                            $_SESSION["rol"] = "cliente";
                        }
                        //si es invitado solo se muestra el primer bloque del if
                        if ($_SESSION["usuario"] == "invitado") {
                        ?>

                            <a class="nav-item nav-link" href="usuario.php">Registrarse</a>
                            <a class="nav-item nav-link active" href="logIn.php">LogIn</a>

                        <?php
                        } else {
                        ?>
                            <a class="nav-item nav-link" href="cesta.php">Cesta</a>
                            <?php
                            //si no es cliente y es admin muestra mas cosas
                            if ($_SESSION["rol"] == "admin") { ?>
                                <a class="nav-item nav-link" href="producto.php">Añadir producto</a>
                            <?php } ?>
                            <a class="nav-item nav-link" href="logOut.php">LogOut</a>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main>

        <?php
        //si se pulsa el boton añadir añadimos una vez el producto en la cesta con la cantidad indicada en el select;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($_POST["action"] == "Añadir") {
                $usuario = $_SESSION["usuario"];
                $idProducto = $_POST["idProducto"];
                //como el select puede no tener opciones por falta de producto en caso de que no este seteado lo inicializamos en 0
                if (!isset($_POST["unidades"])) {
                    $unidades = "0";
                } else {
                    $unidades = $_POST["unidades"];
                }
                $stock = $_POST["stockProducto"];
                $precioProducto = $_POST["precio"];
                //sacamos el id de la cesta del usuario en sesion
                $sqlCesta = "SELECT * FROM Cestas where usuario= '$usuario'";
                $idCestaUsuario = $conexion->query($sqlCesta)->fetch_assoc()["idCesta"];
                //comprobamos que en la base de datos no haya ese producto ya en esa cesta para mostrar el mensaje distinto
                $res = $conexion->query("SELECT * FROM productoscestas WHERE idProducto='$idProducto'");
                //flag que controlara si ha sido encontrado
                $existe = false;
                while ($fila = $res->fetch_assoc()) {
                    //en caso de encontrarlo existe pasa a ser true y no lo añadimos a la bdd si no lo sumamos a la cantidad 
                    if ($fila["idCesta"] == $idCestaUsuario && $fila["idProducto"] == $idProducto) {
                        if ($stock > 0) {
                            $cantidadCesta = $fila["cantidad"] + $unidades;
                            //si no supera con los nuevos elemento los 10 elementos en cesta
                            if ($cantidadCesta <= 10) {
                                $conexion->query("UPDATE productoscestas SET cantidad = '$cantidadCesta' WHERE (idProducto = '$idProducto') and (idCesta = '$idCestaUsuario');");
                                //le restamos al stock las unidades al añadirlo a cesta
                                $cantidadStock = $stock - $unidades;
                                $conexion->query("UPDATE productos SET cantidad = '$cantidadStock' WHERE (idProducto = '$idProducto');");
                                //seteamos el total de la cesta para ello primero sacamos el valor que tiene.
                                $precioTotalActual = $conexion->query("SELECT * FROM Cestas WHERE idCesta='$idCestaUsuario'")->fetch_assoc()["precioTotal"];
                                $precioTotalActual += ($precioProducto * $unidades);
                                $conexion->query("UPDATE cestas SET precioTotal = '$precioTotalActual' WHERE (idCesta='$idCestaUsuario');");
                                //levantamos flag
                                $existe = true;
                                //mostramos que ha sido modificada la cantidad en el carrito
        ?>
                                <div class="alert alert-warning container">Cantidad modificada en el carrito</div>
                            <?php
                            } else {
                                //si existe y serian mas de 10 no lo deja y avisa
                                $existe = true;
                            ?>
                                <div class="alert alert-warning container">No puedes tener mas de 10 productos en cesta</div>
                            <?php
                            }
                        } else {
                            //si el producto no tiene stock
                            ?>
                            <div class="alert alert-danger container">Producto agotado</div>
                        <?php
                        }
                    }
                }
                //si no existe el producto en la cesta, en la tabla productosCestas introducimos los valores de idProducto e idCesta y la cantidad a añadir.
                if (!$existe) {
                    if ($stock > 0) {
                        $sqlProductoCesta = "INSERT INTO productoscestas (idProducto , idCesta, cantidad) values ('$idProducto' , '$idCestaUsuario', '$unidades')";
                        $conexion->query($sqlProductoCesta);
                        //le restamos al stock las unidades al añadirlo a cesta
                        $cantidadStock = $stock - $unidades;
                        $conexion->query("UPDATE productos SET cantidad = '$cantidadStock' WHERE (idProducto = '$idProducto');");
                        //seteamos el total de la cesta para ello primero sacamos el valor que tiene
                        $precioTotalActual = $conexion->query("SELECT * FROM Cestas WHERE idCesta='$idCestaUsuario'")->fetch_assoc()["precioTotal"];
                        $precioTotalActual += ($precioProducto * $unidades);
                        $conexion->query("UPDATE cestas SET precioTotal = '$precioTotalActual' WHERE (idCesta='$idCestaUsuario');");
                        ?>
                        <div class="alert alert-success container">Producto introducido en la cesta</div>
                    <?php
                    } else {
                    ?>
                        <div class="alert alert-danger container">Producto agotado</div>
        <?php
                    }
                }
            }
        }
        ?>
        <div class="container mt-5 divTablas">
            <?php if ($usuario != "invitado") {
            ?>
                <h1>Ofertas de esta semana</h1>
                <table class="table table-hover table-dark mt-5 ">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Descripcion</th>
                            <th>Stock</th>
                            <th>Imagen</th>
                            <th>Añadir producto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM productos";
                        $res = $conexion->query($sql);
                        while ($fila = $res->fetch_assoc()) { ?>
                            <?php
                            //creamos un producto con los campos de la tabla recorriendola con fetchassoc()
                            $producto = new Producto($fila["idProducto"], $fila["nombreProducto"], $fila["precio"], $fila["descripcion"], $fila["cantidad"], $fila["imagen"]);
                            ?>
                            <tr>
                                <td><?php echo $producto->idProducto ?></td>
                                <td><?php echo $producto->nombreProducto ?></td>
                                <td><?php echo $producto->precio ?> €</td>
                                <td><?php echo $producto->descripcion ?></td>
                                <td><?php echo $producto->cantidad ?></td>
                                <td><img src="<?php echo $producto->rutaImagen ?>" alt="<?php echo $producto->nombreProducto ?>" width="50px"></td>
                                <?php
                                ?>
                                <td>
                                    <form action="" method="POST">
                                        <!-- pasamos los valores con botones hidden -->
                                        <input type="hidden" name="idProducto" value="<?php echo $producto->idProducto ?>">
                                        <input type="hidden" name="stockProducto" value="<?php echo $producto->cantidad ?>">
                                        <input type="hidden" name="precio" value="<?php echo $producto->precio ?>">
                                        <input type="submit" name="action" value="Añadir" class="btn btn-light">

                                        <select name="unidades" id="" max=5>
                                            <?php
                                            if ($producto->cantidad == 0) {
                                            ?>
                                                <option value="">0</option>
                                                <?php
                                            } else {
                                                for ($i = 1; $i <= intval($producto->cantidad) && $i <= 5; $i++) {
                                                ?>
                                                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                            <?php
                                                }
                                            }

                                            ?>
                                        </select>
                                    </form>
                                </td>
                                <?php
                                ?>
                                <?php
                                ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <br>
        </div>
    <?php

            } else {
    ?>
        <div class="container bienvenidaInvitado" align="center">
            <h1 id="msjBienvenida">Bienvenido a <img src="imgs/logo.png" alt="" height="50px">Good4Game</h1>
            <p>Para poder disfrutar de nuestra variedad de productos y servicios debe de ser usuario registrado de nuestra pagina</p>
            <p>puede o bien <a href="usuario.php">registrarse</a> o bien <a href="logIn.php">iniciar sesion</a> como usuario de nuestra web</p>
        </div>
    <?php
            }
    ?>

    </main>
</body>
<footer>
    <h1 class="mt-3 titulo"><img src="imgs/logo.png" alt="" height="70px">Good4Game</h1>
</footer>

</html>