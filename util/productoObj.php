<?PHP
class Producto
{
    public $idProducto;
    public $nombreProducto;
    public  $precio;
    public  $descripcion;
    public  $cantidad;
    public  $rutaImagen;

    function __construct(
        $idProducto,
        $nombreProducto,
        $precio,
        $descripcion,
        $cantidad,
        $rutaImagen
    ) {
        $this -> idProducto= $idProducto;
        $this -> nombreProducto= $nombreProducto;
        $this -> precio= $precio;
        $this -> descripcion= $descripcion;
        $this -> cantidad= $cantidad;
        $this -> rutaImagen= $rutaImagen;
    }
}
