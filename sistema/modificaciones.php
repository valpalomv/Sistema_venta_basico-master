<?php include_once "includes/header.php"; ?>

<!-- Begin Page Content -->
<div class="container-fluid">

	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-800">MODIFICACION A DATOS DEL CLIENTE</h1>
	</div>

	<div class="row">
		<div class="col-lg-12">

			<div class="table-responsive">
				<table class="table table-striped table-bordered" id="table">
					<thead class="thead-dark">
						<tr>
							<th>ID</th>
							<th>ID CLIENTE</th>
							<th>COLUMNA A MODIFICAR</th>
							<th>VALOR A MODIFICAR</th>
							<th>VALOR NUEVO</th>
							<th>FECHA DE MODIFICACION</th>
							<?php if ($_SESSION['rol'] == 1) { ?>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php
						include "../conexion.php";
						$query = mysqli_query($conexion, "SELECT * FROM registro_cambios");
						$result = mysqli_num_rows($query);
						if ($result > 0) {
							while ($data = mysqli_fetch_assoc($query)) { ?>
								<tr>
									<td><?php echo $data['id']; ?></td>
									<td><?php echo $data['cliente_id']; ?></td>
									<td><?php echo $data['columna_modificada']; ?></td>
									<td><?php echo $data['valor_anterior']; ?></td>
									<td><?php echo $data['valor_nuevo']; ?></td>
									<td><?php echo $data['fecha_modificacion']; ?></td>
									<?php if ($_SESSION['rol'] == 1) { ?>

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
