<?php
include_once "includes/header.php";
include "../conexion.php";

if (!empty($_POST)) {
  $alert = "";

  if (empty($_POST['proveedor']) || empty($_POST['producto']) || empty($_POST['precio']) || $_POST['precio'] < 0 || empty($_POST['cantidad']) || $_POST['cantidad'] < 0 || empty($_POST['id_inventario'])) {
    $alert = '<div class="alert alert-danger" role="alert">
                Todos los campos son obligatorios
              </div>';
  } else {
    $proveedor = $_POST['proveedor'];
    $producto = $_POST['producto'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $categoria = "";
    $id_inventario = $_POST['id_inventario'];

    if ($_POST['categoria'] === "otra") {
      $categoria = $_POST['otra_categoria'];
    } else {
      $categoria = $_POST['categoria'];
    }
    $usuario_id = $_SESSION['idUser'];

    // Validar si el producto ya existe
    $producto = mysqli_real_escape_string($conexion, $producto);
    $query_producto_existente = mysqli_query($conexion, "SELECT descripcion FROM producto WHERE descripcion = '$producto'");
    $resultado_producto_existente = mysqli_num_rows($query_producto_existente);

    if ($resultado_producto_existente > 0) {
      $alert = '<div class="alert alert-danger" role="alert">
                El producto ya existe en la base de datos
              </div>';
    } else {
      $query_insert = mysqli_query($conexion, "INSERT INTO producto(proveedor, descripcion, precio, existencia, categoria, usuario_id, id_inventario) VALUES ('$proveedor', '$producto', '$precio', '$cantidad', '$categoria', '$usuario_id', '$id_inventario')");
      if ($query_insert) {
        $alert = '<div class="alert alert-primary" role="alert">
                Producto registrado
              </div>';
      } else {
        $alert = '<div class="alert alert-danger" role="alert">
                Error al registrar el producto
              </div>';
      }
    }
  }
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Panel de Administración</h1>
    <a href="lista_productos.php" class="btn btn-primary">Regresar</a>
  </div>

  <!-- Content Row -->
  <div class="row">
    <div class="col-lg-6 m-auto">
      <form action="" method="post" autocomplete="off">
        <?php echo isset($alert) ? $alert : ''; ?>
        <div class="form-group">
          <label>Proveedor</label>
          <?php
          $query_proveedor = mysqli_query($conexion, "SELECT codproveedor, proveedor FROM proveedor ORDER BY proveedor ASC");
          $resultado_proveedor = mysqli_num_rows($query_proveedor);
          ?>
          <select id="proveedor" name="proveedor" class="form-control">
            <?php
            if ($resultado_proveedor > 0) {
              while ($proveedor = mysqli_fetch_array($query_proveedor)) {
            ?>
                <option value="<?php echo $proveedor['codproveedor']; ?>"><?php echo $proveedor['proveedor']; ?></option>
            <?php
              }
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label for="producto">Producto</label>
          <input type="text" placeholder="Ingrese nombre del producto" name="producto" id="producto" class="form-control">
        </div>
        <div class="form-group">
          <label for="precio">Precio</label>
          <input type="text" placeholder="Ingrese precio" class="form-control" name="precio" id="precio">
        </div>
        <div class="form-group">
          <label for="cantidad">Cantidad</label>
          <input type="number" placeholder="Ingrese cantidad" class="form-control" name="cantidad" id="cantidad">
        </div>
        <div class="form-group">
          <label>Categoria</label>
          <?php
            $query_categoria = mysqli_query($conexion, "SELECT categoria_nombre FROM categoria ORDER BY categoria_nombre ASC");
            $resultado_categoria = mysqli_num_rows($query_categoria);
          ?>
          <select id="categoria" name="categoria" class="form-control">
            <?php
            if ($resultado_categoria > 0) {
              while ($categoria = mysqli_fetch_array($query_categoria)) {
            ?>
                <option value="<?php echo $categoria['categoria_nombre']; ?>"><?php echo $categoria['categoria_nombre']; ?></option>
            <?php
              }
            }
            ?>
          </select>
        </div>
        <div class="form-group">
  <label for="id_inventario">ID Inventario</label>
  <select name="id_inventario" class="form-control">
    <option value="1"> 1</option>
    <option value="2"> 2</option>
    <!-- Agrega más opciones según tus inventarios disponibles -->
  </select>
</div>
  </select>
        <!-- /.container-fluid -->
        <button type="submit" class="btn btn-primary">Registrar Producto</button>
      </form>
    </div>
  </div>
  <!-- End of Main Content -->
  <?php include_once "includes/footer.php"; ?>
