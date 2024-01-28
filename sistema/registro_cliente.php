<?php include_once "includes/header.php";
include "../conexion.php";
// Consulta para obtener el último DNI registrado
$query_ultimo_dni = mysqli_query($conexion, "SELECT MAX(dni) AS ultimo_dni FROM cliente");
$ultimo_dni_result = mysqli_fetch_assoc($query_ultimo_dni);

// Calcula el siguiente DNI
$ultimo_dni = $ultimo_dni_result['ultimo_dni'];
$siguiente_dni = $ultimo_dni + 1;

if (!empty($_POST)) {
    $alert = "";
    if (empty($_POST['nombre']) || empty($_POST['apellido_paterno']) || empty($_POST['apellido_materno']) || empty($_POST['telefono']) || empty($_POST['pais']) || empty($_POST['estado']) || empty($_POST['municipio']) || empty($_POST['calle']) || empty($_POST['cp'])) {
        $alert = '<div class="alert alert-danger" role="alert">
                    Todos los campos son obligatorios
                  </div>';
    } else {
        $dni = $_POST['dni'];
        $nombre = $_POST['nombre'];
        $apellido_paterno = $_POST['apellido_paterno'];
        $apellido_materno = $_POST['apellido_materno'];
        $telefono = $_POST['telefono'];
        $pais = $_POST['pais'];
        $estado = $_POST['estado'];
        $municipio = $_POST['municipio'];
        $calle = $_POST['calle'];
        $cp = $_POST['cp'];
        $usuario_id = $_SESSION['idUser'];



        $result = 0;
        if (is_numeric($dni) and $dni != 0) {
            $query = mysqli_query($conexion, "SELECT * FROM cliente where dni = '$dni'");
            $result = mysqli_fetch_array($query);
        }
        if ($result > 0) {
            $alert = '<div class="alert alert-danger" role="alert">
                                    El dni ya existe
                                </div>';
        } else {
            $query_insert = mysqli_query($conexion, "INSERT INTO cliente(dni, nombre, telefono, pais, calle, municipio, estado, cp, usuario_id, apellido_paterno, apellido_materno) VALUES ('$dni', '$nombre', '$telefono', '$pais', '$calle', '$municipio', '$estado', '$cp', '$usuario_id', '$apellido_paterno', '$apellido_materno')");
            if ($query_insert) {
                $alert = '<div class="alert alert-primary" role="alert">
                                    Cliente Registrado
                                </div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">
                                    Error al Guardar
                            </div>';
            }
        }
    }
    mysqli_close($conexion);
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Panel de Administración</h1>
        <a href="lista_cliente.php" class="btn btn-primary">Regresar</a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-6 m-auto">
            <form action="" method="post" autocomplete="off">
                <?php echo isset($alert) ? $alert : ''; ?>
                <div class="form-group">
                    <label for="dni">Dni</label>
                    <input type="number" placeholder="Ingrese dni" name="dni" id="dni" class="form-control">
                </div>
                <div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" placeholder="Ingrese Nombre" name="nombre" id="nombre" class="form-control">
</div>
<div class="form-group">
    <label for="apellido_paterno">Apellido Paterno</label>
    <input type="text" placeholder="Ingrese Apellido Paterno" name="apellido_paterno" id="apellido_paterno" class="form-control">
</div>
<div class="form-group">
    <label for="apellido_materno">Apellido Materno</label>
    <input type="text" placeholder="Ingrese Apellido Materno" name="apellido_materno" id="apellido_materno" class="form-control">
</div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="number" placeholder="Ingrese Teléfono" name="telefono" id="telefono" class="form-control">
                </div>
                <div class="form-group">
    <label for="pais">País</label>
    <select name="pais" id="pais" class="form-control" required>
        <option value="Mexico">Mexico</option>
        <option value="Argentina">Argentina</option>
        <option value="Brasil">Brasil</option>
        <option value="Chile">Chile</option>
        <!-- Agrega más opciones de países según sea necesario -->
    </select>
</div>
        <div class="form-group" id="estados-mexico">
    <label for="estado">Estado</label>
    <select name="estado" class="form-control">
        <!-- Lista de estados de México -->
        <option value="Aguascalientes">Aguascalientes</option>
        <option value="Baja California">Baja California</option>
        <option value="Baja California Sur">Baja California Sur</option>
        <option value="Campeche">Campeche</option>
        <option value="Chiapas">Chiapas</option>
        <option value="Chihuahua">Chihuahua</option>
        <option value="Coahuila">Coahuila</option>
        <option value="Colima">Colima</option>
        <option value="Durango">Durango</option>
        <option value="Guanajuato">Guanajuato</option>
        <option value="Guerrero">Guerrero</option>
        <option value="Hidalgo">Hidalgo</option>
        <option value="Jalisco">Jalisco</option>
        <option value="México City">Ciudad de México</option>
        <option value="Mexico State">Estado de México</option>
        <option value="Michoacán">Michoacán</option>
        <option value="Morelos">Morelos</option>
        <option value="Nayarit">Nayarit</option>
        <option value="Nuevo León">Nuevo León</option>
        <option value="Oaxaca">Oaxaca</option>
        <option value="Puebla">Puebla</option>
        <option value="Querétaro">Querétaro</option>
        <option value="Quintana Roo">Quintana Roo</option>
        <option value="San Luis Potosí">San Luis Potosí</option>
        <option value="Sinaloa">Sinaloa</option>
        <option value="Sonora">Sonora</option>
        <option value="Tabasco">Tabasco</option>
        <option value="Tamaulipas">Tamaulipas</option>
        <option value="Tlaxcala">Tlaxcala</option>
        <option value="Veracruz">Veracruz</option>
        <option value="Yucatán">Yucatán</option>
        <option value="Zacatecas">Zacatecas</option>
        <!-- Agrega más estados de México aquí -->
    </select>
</div>

<script>
document.getElementById("pais").addEventListener("change", function () {
    var selectedCountry = this.value;
    var estadosMexico = document.getElementById("estados-mexico");
    var estadosArgentina = document.getElementById("estados-argentina");
    var estadosBrasil = document.getElementById("estados-brasil");
    var estadosChile = document.getElementById("estados-chile");

    if (selectedCountry === "Mexico") {
        estadosMexico.style.display = "block";
        estadosArgentina.style.display = "none";
        estadosBrasil.style.display = "none";
        estadosChile.style.display = "none";
    } else if (selectedCountry === "Argentina") {
        estadosMexico.style.display = "none";
        estadosArgentina.style.display = "block";
        estadosBrasil.style.display = "none";
        estadosChile.style.display = "none";
    } else if (selectedCountry === "Brasil") {
        estadosMexico.style.display = "none";
        estadosArgentina.style.display = "none";
        estadosBrasil.style.display = "block";
        estadosChile.style.display = "none";
    } else if (selectedCountry === "Chile") {
        estadosMexico.style.display = "none";
        estadosArgentina.style.display = "none";
        estadosBrasil.style.display = "none";
        estadosChile.style.display = "block";
    } else {
        estadosMexico.style.display = "none";
        estadosArgentina.style.display = "none";
        estadosBrasil.style.display = "none";
        estadosChile.style.display = "none";
    }
});
</script>
                <div class="form-group">
                    <label for="municipio">Municipio</label>
                    <input type="text" placeholder="Ingrese Municipio" name="municipio" id="municipio" class="form-control">
                </div>
                <div class="form-group">
                    <label for="calle">Calle</label>
                    <input type="text" placeholder="Ingrese Calle" name="calle" id="calle" class="form-control">
                </div>
                <div class="form-group">
                    <label for="cp">Código Postal</label>
                    <input type="number" placeholder="Ingrese Código Postal" name="cp" id="cp" class="form-control">
                </div>
                <input type="submit" value="Guardar Cliente" class="btn btn-primary">
            </form>
        </div>
    </div>


</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php include_once "includes/footer.php"; ?>
