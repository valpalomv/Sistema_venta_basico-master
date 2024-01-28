<?php
include_once "includes/header.php";
include "../conexion.php";

// Realiza la consulta para obtener los datos del cliente según el ID
$sql = "SELECT * FROM cliente WHERE dni = $dni";
$resultado = mysqli_query($conexion, $sql);

if (mysqli_num_rows($resultado) > 0) {
    $row = mysqli_fetch_assoc($resultado);

    // Asigna los valores a las variables
    $dni_cliente = $row['dni'];
    $nom_cliente = $row['nombre'];
    $apellido_paterno = $row['apellido_paterno'];
    $apellido_materno = $row['apellido_materno'];
    $tel_cliente = $row['telefono'];
    $pais = $row['pais'];
    $estado = $row['estado'];
    $municipio = $row['municipio'];
    $calle = $row['calle'];
    $cp = $row['CP'];
} else {
    // Manejo de error si el cliente no se encuentra
    echo "Cliente no encontrado";
    exit;
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <h4 class="text-center">Datos del Cliente</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <form method="post" name="form_new_cliente_venta" id="form_new_cliente_venta">
                        <input type="hidden" name="action" value="addCliente">
                        <input type="hidden" id="idcliente" value="1" name="idcliente" required>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Dni</label>
                                    <input type="number" name="dni_cliente" id="dni_cliente" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" name="nom_cliente" id="nom_cliente" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class "form-group">
                                    <label>Apellido Paterno</label>
                                    <input type="text" name="apellido_paterno" id="apellido_paterno"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Apellido Materno</label>
                                    <input type="text" name="apellido_materno" id="apellido_materno"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Teléfono</label>
                                    <input type="number" name="tel_cliente" id="tel_cliente" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Estado</label>
                                    <input type="text" name="estado" id="estado" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Municipio</label>
                                    <input type="text" name="municipio_cliente" id="municipio_cliente"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Calle</label>
                                    <input type="text" name="calle_cliente" id="calle_cliente" class="form-control"
                                        required>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Código Postal</label>
                                    <input type="text" name="cp_cliente" id="cp_cliente" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div id="div_registro_cliente">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
            <h4 class="text-center">Datos Venta</h4>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> VENDEDOR</label>
                        <p style="font-size: 16px; text-transform: uppercase; color: blue;">
                            <?php echo $_SESSION['nombre']; ?>
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <label>Acciones</label>
                    <div id="acciones_venta" class="form-group">
                        <a href="#" class="btn btn-danger" id="btn_anular_venta">Anular</a>
                        <a href="#" class="btn btn-primary" id="btn_facturar_venta"><i class="fas fa-save"></i> Generar
                            Venta</a>
                    </div>
                </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th width="100px">Código</th>
                            <th>Des.</th>
                            <th>Stock</th>
                            <th width="100px">Cantidad</th>
                            <th class="textright">Precio</th>
                            <th class="textright">Precio Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tr>
                        <td><input type="number" name="txt_codproducto" id="txt_codproducto"></td>
                        <td id="txt_descripcion">-</td>
                        <td id="txt_existencia">-</td>
                        <td><input type="number" name="txt_cant_producto" id="txt_cant_producto" value="0" min="1"
                                required></td>
                        <td id="txt_precio" class="textright">0.00</td>
                        <td id="txt_precio_total" class="textright">0.00</td>
                        <td><button type="button" id="add_product_venta" class="btn btn-dark"
                                style="display: none;">Agregar</button></td>
                    </tr>
                    <tr>
                        <th>Código</th>
                        <th colspan="2">Descripción</th>
                        <th>Cantidad</th>
                        <th class="textright">Precio</th>
                        <th class="textright">Precio Total</th>
                        <th>Acciones</th>
                    </tr>
                    <tbody id="detalle_venta">
                        <!-- Contenido ajax -->
                    </tbody>
                    <tfoot id="detalle_totales">
                        <!-- Contenido ajax -->
                    </tfoot>

                </table>
            </div>
        </div>
    </div>


<!-- /.container-fluid -->

<?php include_once "includes/footer.php"; ?>

</body>

</html>
