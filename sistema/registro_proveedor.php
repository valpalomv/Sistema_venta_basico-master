<?php
include_once "includes/header.php";
include "../conexion.php";

if (!empty($_POST)) {
    $alert = "";
    if (empty($_POST['proveedor']) || empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['calle']) || empty($_POST['municipio']) || empty($_POST['estado']) || empty($_POST['cp'])) {
        $alert = '<div class="alert alert-danger" role="alert">
                        Todos los campos son obligatorios
                    </div>';
    } else {
        $proveedor = $_POST['proveedor'];
        $contacto = $_POST['contacto'];
        $telefono = $_POST['telefono'];
        $calle = $_POST['calle'];
        $municipio = $_POST['municipio'];
        $estado = $_POST['estado'];
        $cp = $_POST['cp'];
        $usuario_id = $_SESSION['idUser'];

        // Validar si el contacto ya existe
        $contacto = mysqli_real_escape_string($conexion, $contacto);
        $query_contacto_existente = mysqli_query($conexion, "SELECT * FROM proveedor WHERE contacto = '$contacto'");
        $resultado_contacto_existente = mysqli_num_rows($query_contacto_existente);

        if ($resultado_contacto_existente > 0) {
            $alert = '<div class="alert alert-danger" role="alert">
                        El contacto ya está registrado
                    </div>';
        } else {
            $query_insert = mysqli_query($conexion, "INSERT INTO proveedor (proveedor, contacto, telefono, calle, municipio, estado, CP, usuario_id) VALUES ('$proveedor', '$contacto', '$telefono', '$calle', '$municipio', '$estado', '$cp', '$usuario_id')");

            if ($query_insert) {
                $alert = '<div class="alert alert-primary" role="alert">
                            Proveedor Registrado
                        </div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">
                            Error al registrar proveedor
                        </div>';
            }
        }
    }
}

mysqli_close($conexion);
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-6 m-auto">
            <div class="card-header bg-primary text-white">
                Registro de Proveedor
            </div>
            <div class="card">
                <form action="" autocomplete="off" method="post" class="card-body p-2">
                    <?php echo isset($alert) ? $alert : ''; ?>
                    <div class="form-group">
                        <label for="nombre">NOMBRE</label>
                        <input type="text" placeholder="Ingrese nombre" name="proveedor" id="nombre" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="contacto">Correo</label>
                        <input type="text" placeholder="Ingrese el Correo" name="contacto" id="contacto" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="telefono">TELÉFONO</label>
                        <input type="number" placeholder="Ingrese teléfono" name="telefono" id="telefono" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="calle">CALLE</label>
                        <input type="text" placeholder="Ingrese calle" name="calle" id="calle" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="municipio">MUNICIPIO</label>
                        <input type="text" placeholder="Ingrese municipio" name="municipio" id="municipio" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="estado">ESTADO</label>
                        <input type="text" placeholder="Ingrese estado" name="estado" id="estado" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="cp">CÓDIGO POSTAL</label>
                        <input type="text" placeholder="Ingrese código postal" name="cp" id="cp" class="form-control">
                    </div>
                    <input type="submit" value="Guardar Proveedor" class="btn btn-primary">
                    <a href="lista_proveedor.php" class="btn btn-danger">Regresar</a>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php include_once "includes/footer.php"; ?>
