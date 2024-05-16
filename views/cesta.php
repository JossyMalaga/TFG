<?php
session_start();
$usuario = $_SESSION["usuario"];
if ($_SESSION["usuario"] == "invitado") {
    header("Location: logIn.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <?php require '../util/base_de_datos.php';
    require '../util/productoObj.php' ?>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php
    //traemos las variables que nos van a hacer falta de tablas distintas 
    $idCestaUsuario = $conexion->query("SELECT * FROM Cestas where usuario= '$usuario'")->fetch_assoc()["idCesta"];
    $totalCesta = $conexion->query("SELECT * FROM cestas where idCesta = '$idCestaUsuario'")->fetch_assoc()["precioTotal"];

    ?>
    <video src="imgs/fondo.mp4" autoplay muted></video>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="principal.php"><img src="imgs/logo.png" alt="" height="40px">Good4Game</a>
            <a class="nav-item nav-link" href="logOut.php">LogOut</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </nav>
    </header>
    <main>
        <div class="container divTablas">
            <h1 class="mt-5" align="center" id="inicio">Cesta de <?php echo $usuario ?></h1>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if ($_POST["action"] == "finalizar") {
                    //traemos el total de la cesta de la tabla para crear el pedido  
                    $totalCesta = $conexion->query("SELECT * FROM cestas where idCesta = '$idCestaUsuario'")->fetch_assoc()["precioTotal"];
                    //solo permitimos crear un pedido si la cesta esta con productos
                    if ($totalCesta != "0.00") {
                        //generamos un pedido (el que hemos creado con este usuario)
                        $conexion->query("INSERT INTO pedidos (usuario, precioTotal) VALUES ('$usuario','$totalCesta');");
                        //borrar todos los productos e insertarlo antes en linea de pedido y crear un contador
                        $contador=0;
                        while ($fila = $conexion->query("SELECT * FROM productoscestas WHERE idCesta='$idCestaUsuario';")->fetch_assoc()) {
                            //aumentamos el contador para la linea de pedido
                            $contador++;
                            $idProducto = $fila["idProducto"];
                            //sacamos los valores que necesitamos para linea pedidos con el id del producto que estamos fetcheando
                            $producto = $conexion->query("SELECT * FROM productos WHERE idProducto = '$idProducto'")->fetch_assoc();
                            $precioProducto = $producto["precio"];
                            $productoCesta = $conexion->query("SELECT * FROM productoscestas WHERE idProducto = '$idProducto'")->fetch_assoc();
                            $unidades = $productoCesta["cantidad"];
                            //sacamos el id del ultimo pedido 
                            $idUltimoPedido = $conexion->query("SELECT * FROM pedidos WHERE usuario = '$usuario' ORDER BY idPedido DESC LIMIT 1")->fetch_assoc()["idPedido"];
                            //introducimos en linea pedido cada producto antes de borrarlo
                            $conexion->query("INSERT INTO lineaspedidos (lineaPedido, idProducto, idPedido,  precioUnitario, cantidad) VALUES ('$contador','$idProducto',' $idUltimoPedido' , '$precioProducto', '$unidades')");
                            //borramos el producto de la tabla productoscestas
                            $conexion->query("DELETE FROM productoscestas where (idCesta='$idCestaUsuario' and idProducto='$idProducto')");
                        }
                        //seteamos el total de la cesta a 0
                        $conexion->query("UPDATE cestas SET precioTotal = '0.00' WHERE (usuario = '$usuario');");

            ?>
                        <br><br><br>
                        <div class="alert alert-success container">Pedido creado</div>
                    <?php
                    } else {

                    ?>
                        <br><br><br>
                        <div class="alert alert-danger container">Debe añadir algo a la cesta</div>
            <?php
                    }
                }
            }
            ?>
            <table class="table table-dark table-hover mt-5">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Imagen</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //primero sacamos el id de la cesta del usuario
                    $sqlCesta = "SELECT * FROM Cestas where usuario= '$usuario'";
                    $idCestaUsuario = $conexion->query($sqlCesta)->fetch_assoc()["idCesta"];
                    //miramos que productos estan en su cesta y que cantidad hay
                    $resCestasUsuarios = $conexion->query("SELECT * FROM productoscestas WHERE idCesta='$idCestaUsuario'");
                    //recorremos en busca de cada producto con el id de cesta de nuestro usuario y almacenamos el producto y la cantidad
                    while ($cestaUsuario = $resCestasUsuarios->fetch_assoc()) {
                        $idProducto = $cestaUsuario["idProducto"];
                        $cantidadProducto = $cestaUsuario["cantidad"];
                        //miramos en productos que productos hay con ese id
                        $resProductos = $conexion->query("SELECT * FROM productos WHERE idProducto = '$idProducto'");
                        //los recorremos si hay
                        if ($resProductos->num_rows > 0) {
                            while ($resProducto = $resProductos->fetch_assoc()) {
                                //si hay fila creamos un objeto producto para poder mostrar lo que nos interesa
                                $producto = new Producto($resProducto["idProducto"], $resProducto["nombreProducto"], $resProducto["precio"], $resProducto["descripcion"], $resProducto["cantidad"], $resProducto["imagen"]);
                    ?>
                                <tr>
                                    <td><?php echo $producto->nombreProducto ?></td>
                                    <td><img src="<?php echo $producto->rutaImagen ?>" alt="<?php echo $producto->nombreProducto ?>" width="50px"></td>
                                    <td><?php echo $producto->precio ?> €</td>
                                    <td><?php echo $cantidadProducto; ?></td>
                                </tr>
                    <?php
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
            <?php
            $totalCesta = $conexion->query("SELECT * FROM cestas where idCesta = '$idCestaUsuario'")->fetch_assoc()["precioTotal"];
            ?>
            <div class="totalCesta">
                <h5 class="resultado"><?php echo "Total de la cesta: " . $totalCesta . " €" ?></h5>
                <form action="" method="POST" class="form-group">
                    <input type="submit" value="Realizar pedido" class="btn btn-info finalizar">
                    <input type="hidden" name="action" value="finalizar">
                </form>
            </div>
        </div>


    </main>
</body>
<footer>
    <h1 class="mt-3 titulo"><img src="imgs/logo.png" alt="" height="70px">Good4Game</h1>
</footer>

</html>