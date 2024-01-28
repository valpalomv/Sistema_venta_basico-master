<?php
include_once "includes/header.php";
include "../conexion.php";

// Validar producto
if (empty($_REQUEST['id'])) {
    header("Location: lista_productos.php");
    exit;
}

$id_producto = $_REQUEST['id'];

if (!is_numeric($id_producto)) {
    header("Location: lista_productos.php");
    exit;
}

$query_producto = mysqli_query($conexion, "SELECT codproducto, descripcion, proveedor, precio, existencia FROM producto WHERE codproducto = $id_producto");
$result_producto = mysqli_num_rows($query_producto);

if ($result_producto <= 0) {
    header("Location: lista_productos.php");
    exit;
}

$data_producto = mysqli_fetch_assoc($query_producto);

// Agregar Productos a entrada
if (!empty($_POST)) {
    $alert = "";

    if (isset($_POST['cantidad']) && isset($_POST['precio'])) {
        $precio = $_POST['precio'];
        $cantidad = $_POST['cantidad'];
        $producto_id = $_GET['id'];
        $usuario_id = $_SESSION['idUser'];
        $existencia = $data_producto['existencia'];

        if ($cantidad <= 0) {
            echo "La cantidad debe ser mayor que cero.";
        } elseif ($existencia === 0) {
            echo "No hay existencias disponibles para este producto.";
        } else {
            $query_insert = mysqli_query($conexion, "INSERT INTO entradas(codproducto,cantidad,precio,usuario_id) VALUES ($producto_id, $cantidad, $precio, $usuario_id)");

            if ($query_insert) {
                // Ejecutar procedimiento almacenado
                $query_upd = mysqli_query($conexion, "CALL actualizar_precio_producto($cantidad,$precio,$producto_id)");

                if ($query_upd) {
                    $alert = '<div class="alert alert-primary" role="alert">
                        Producto actualizado con éxito
                    </div>';
                } else {
                    echo "Error al actualizar el producto.";
                }
            } else {
                echo "Error al agregar producto.";
            }
        }
        mysqli_close($conexion);
    } else {
        echo "Error al agregar producto.";
    }
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 m-auto">
            <form action="" method="post">
                <?php echo isset($alert) ? $alert : ''; ?>
                <div class="form-group">
                    <label for="precio">Precio Actual</label>
                    <input type="number" class="form-control" value="<?php echo $data_producto['precio']; ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="precio">Cantidad de productos Disponibles</label>
                    <input type="number" class="form-control" value="<?php echo $data_producto['existencia']; ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="precio">Nuevo Precio</label>
                    <input type="number" placeholder="Ingrese nuevo precio" name="precio" class="form-control" value="<?php echo $data_producto['precio']; ?>">
                </div>
                <div class="form-group">
                    <label for="cantidad">Agregar Cantidad</label>
                    <input type="number" placeholder="Ingrese cantidad" name="cantidad" id="cantidad" class="form-control">
                </div>

                <input type="submit" value="Actualizar" class="btn btn-primary">
                <a href="lista_productos.php" class="btn btn-danger">Regresar</a>
            </form>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->
<?php include_once "includes/footer.php"; ?>
