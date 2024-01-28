<?php
include_once "includes/header.php";
include "../conexion.php";

// Obtener las categorías
$query_categorias = mysqli_query($conexion, "SELECT * FROM categoria ORDER BY categoria_nombre ASC");

?>

<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Productos por Categoría</h1>
  </div>

  <!-- Filter Row -->
  <div class="row">
    <div class="col-lg-6">
      <form action="" method="post">
        <div class="form-group">
          <label for="categoria">Filtrar por categoría:</label>
          <select id="categoria" name="categoria" class="form-control">
            <option value="">Todas las categorías</option>
            <?php
            while ($categoria = mysqli_fetch_array($query_categorias)) {
              $categoriaId = $categoria['categoria_nombre'];
              $categoriaNombre = $categoria['categoria_nombre'];
              echo "<option value='$categoriaId'>$categoriaNombre</option>";
            }
            ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
      </form>
    </div>
  </div>
  <div>          <label for="categoria"></label>

		</div>
  <!-- End of Filter Row -->

  <!-- Content Row -->
  <div class="row">
    <div class="col-lg-12">
      <?php
      if (!empty($_POST['categoria'])) {
        $categoriaSeleccionada = $_POST['categoria'];
        $query_productos = mysqli_query($conexion, "SELECT * FROM vista_productos_por_categoria WHERE categoria = '$categoriaSeleccionada'");

		if (mysqli_num_rows($query_productos) > 0) {
          echo "<h2>Categoría seleccionada: " . $categoriaSeleccionada . "</h2>";
          echo "<table class='table'>";
          echo "<thead>";
          echo "<tr>";
          echo "<th>Producto</th>";
          echo "</tr>";
          echo "</thead>";
          echo "<tbody>";

          while ($producto = mysqli_fetch_array($query_productos)) {
            echo "<tr>";
            echo "<td>" . $producto['producto'] . "</td>";
            echo "</tr>";
          }

          echo "</tbody>";
          echo "</table>";
        } else {
          echo "<p>No se encontraron productos para la categoría seleccionada.</p>";
        }
      } else {
        echo "<p>Selecciona una categoría para ver sus productos.</p>";
      }
      ?>
    </div>
  </div>
  <!-- End of Content Row -->

</div>
<!-- End of Container-fluid -->

<?php include_once "includes/footer.php"; ?>
