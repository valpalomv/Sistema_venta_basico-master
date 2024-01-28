<?php include_once "includes/header.php"; ?>

<!-- Begin Page Content -->
<div class="container-fluid">

	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">Panel de Administración</h1>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="table-responsive">
				<table class="table table-striped table-bordered" id="table">
					<thead class="thead-dark">
						<tr>
							<th>PROVEEDOR</th>
							<th>NUMERO DE PRODUCTOS</th>
                            <th>TOTAL</th>
						</tr>
					</thead>
					<tbody>
						<?php
						require "../conexion.php";
						$query = mysqli_query($conexion, "SELECT pr.proveedor, COUNT(p.codproducto) AS total_productos, SUM(p.precio) AS promedio_precio
                        FROM proveedor pr
                        JOIN producto p ON pr.codproveedor = p.proveedor
                        GROUP BY pr.proveedor;

                        ");
						mysqli_close($conexion);
						$cli = mysqli_num_rows($query);

						if ($cli > 0) {
							while ($dato = mysqli_fetch_array($query)) {
						?>
								<tr>
                                <td><?php echo $dato['proveedor']; ?></td>
									<td><?php echo $dato['total_productos']; ?></td>
									<td><?php echo $dato['promedio_precio']; ?></td>

								</tr>
						<?php }
						} ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>



</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->


<?php include_once "includes/footer.php"; ?>
