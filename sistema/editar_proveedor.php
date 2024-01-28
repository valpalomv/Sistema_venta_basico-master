<?php
include "includes/header.php";
include "../conexion.php";

if (!empty($_POST)) {
  $alert = "";
  if (empty($_POST['proveedor']) || empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['calle']) || empty($_POST['municipio']) || empty($_POST['estado']) || empty($_POST['cp'])) {
    $alert = '<p class="msg_error">Todos los campos son requeridos</p>';
  } else {
    $idproveedor = $_POST['id'];
    $proveedor = $_POST['proveedor'];
    $contacto = $_POST['contacto'];
    $telefono = $_POST['telefono'];
    $calle = $_POST['calle'];
    $municipio = $_POST['municipio'];
    $estado = $_POST['estado'];
    $cp = $_POST['cp'];

    $sql_update = mysqli_query($conexion, "UPDATE proveedor SET proveedor = '$proveedor', contacto = '$contacto' , telefono = $telefono, calle = '$calle', municipio = '$municipio', estado = '$estado', CP = '$cp' WHERE codproveedor = $idproveedor");

    if ($sql_update) {
      $alert = '<p class="msg_save">Proveedor actualizado correctamente</p>';
    } else {
      $alert = '<p class="msg_error">Error al actualizar el proveedor</p>';
    }
  }
}

// Mostrar Datos
if (empty($_REQUEST['id'])) {
  header("Location: lista_proveedor.php");
  mysqli_close($conexion);
}

$idproveedor = $_REQUEST['id'];
$sql = mysqli_query($conexion, "SELECT * FROM proveedor WHERE codproveedor = $idproveedor");
mysqli_close($conexion);
$result_sql = mysqli_num_rows($sql);

if ($result_sql == 0) {
  header("Location: lista_proveedor.php");
} else {
  while ($data = mysqli_fetch_array($sql)) {
    $idproveedor = $data['codproveedor'];
    $proveedor = $data['proveedor'];
    $contacto = $data['contacto'];
    $telefono = $data['telefono'];
    $calle = $data['calle'];
    $municipio = $data['municipio'];
    $estado = $data['estado'];
    $cp = $data['CP'];
  }
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-6 m-auto">
      <?php echo isset($alert) ? $alert : ''; ?>
      <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $idproveedor; ?>">
        <div class="form-group">
          <label for="proveedor">Proveedor</label>
          <input type="text" placeholder="Ingrese proveedor" name="proveedor" class="form-control" id="proveedor" value="<?php echo $proveedor; ?>">
        </div>
        <div class="form-group">
          <label for="contacto">Contacto</label>
          <input type="text" placeholder="Ingrese contacto" name="contacto" class="form-control" id="contacto" value="<?php echo $contacto; ?>">
        </div>
        <div class="form-group">
          <label for="telefono">Teléfono</label>
          <input type="number" placeholder="Ingrese Teléfono" name="telefono" class="form-control" id="telefono" value="<?php echo $telefono; ?>">
        </div>
        <div class="form-group">
          <label for="calle">Calle</label>
          <input type="text" placeholder="Ingrese calle" name="calle" class="form-control" id="calle" value="<?php echo $calle; ?>">
        </div>
        <div class="form-group">
          <label for="municipio">Municipio</label>
          <input type="text" placeholder="Ingrese municipio" name="municipio" class="form-control" id="municipio" value="<?php echo $municipio; ?>">
        </div>
        <div class="form-group">
          <label for="estado">Estado</label>
          <input type="text" placeholder="Ingrese estado" name="estado" class="form-control" id="estado" value="<?php echo $estado; ?>">
        </div>
        <div class="form-group">
          <label for="cp">Código Postal</label>
          <input type="text" placeholder="Ingrese código postal" name="cp" class="form-control" id="cp" value="<?php echo $cp; ?>">
        </div>
        <input type="submit" value="Editar Proveedor" class="btn btn-primary">
      </form>
    </div>
  </div>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php include_once "includes/footer.php"; ?>
