<?php include_once "includes/header.php"; ?>

<!-- Begin Page Content -->
<div class="container-fluid">

	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">Ventas por cliente</h1>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div class="table-responsive">
				<table class="table table-striped table-bordered" id="table">
					<thead class="thead-dark">
						<tr>
                            <th>DNI </th>
                            <th>NOMBRE</th>
							<th>APELLIDO PATERNO</th>
                            <th>APELLIDO MATERNO</th>
                            <th>NUMERO DE VECES QUE HA COMPRADO</th>
                            <th>ULTIMA COMPRA</th>
							<th>ANTERIOR COMPRA</th>
							<?php if ($_SESSION['rol'] == 1) { ?>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php
						include "../conexion.php";

						$query = mysqli_query($conexion, "SELECT * FROM vista_clientes_total");
						$result = mysqli_num_rows($query);
						if ($result > 0) {
							while ($data = mysqli_fetch_assoc($query)) { ?>
								<tr>
									<td><?php echo $data['idcliente']; ?></td>
									<td><?php echo $data['nombre']; ?></td>
                                    <td><?php echo $data['apellido_paterno']; ?></td>
									<td><?php echo $data['apellido_materno']; ?></td>
                                    <td><?php echo $data['veces_compradas']; ?></td>
									<td><?php echo $data['ultima_compra']; ?></td>
									<td><?php echo $data['primera_compra']; ?></td>
										<?php if ($_SESSION['rol'] == 2) { ?>

										<?php } ?>
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
