<?php
include_once "includes/header.php";
include "../conexion.php";

if (!empty($_POST)) {
    $alert = "";
    if (empty($_POST['nombre']) || empty($_POST['apellido_paterno']) || empty($_POST['apellido_materno']) || empty($_POST['telefono']) || empty($_POST['pais']) || empty($_POST['estado']) || empty($_POST['municipio']) || empty($_POST['calle']) || empty($_POST['cp'])) {
        $alert = '<div class="alert alert-danger" role="alert">
                    Todos los campos son obligatorios
                  </div>';
    } else {
        $idcliente = $_POST['id'];
        $dni = $_POST['dni'];
        $nombre = $_POST['nombre'];
        $apellido_paterno = $_POST['apellido_paterno'];
        $apellido_materno = $_POST['apellido_materno'];
        $telefono = $_POST['telefono'];
        $pais = $_POST['pais'];
        $calle = $_POST['calle'];
        $municipio = $_POST['municipio'];
        $estado = isset($_POST['estado']) ? $_POST['estado'] : '';
        $CP = $_POST['cp'];
        $result = 0;

        if (is_numeric($dni) && $dni != 0) {
            $query = mysqli_query($conexion, "SELECT * FROM cliente WHERE dni = '$dni' AND idcliente != $idcliente");
            $result = mysqli_fetch_array($query);
            $resul = mysqli_num_rows($query);
        }

        if ($resul >= 1) {
            $alert = '<p class="error">El DNI ya existe</p>';
        } else {
            if ($dni == '') {
                $dni = 0;
            }
            $sql_update = mysqli_query($conexion, "UPDATE cliente SET dni = $dni, nombre = '$nombre', apellido_paterno = '$apellido_paterno', apellido_materno = '$apellido_materno', telefono = '$telefono', calle = '$calle', municipio = '$municipio', estado = '$estado', cp = '$CP', pais = '$pais' WHERE idcliente = $idcliente");
            if ($sql_update) {
                $alert = '<p class="exito">Cliente actualizado correctamente</p>';
            } else {
                $alert = '<p class="error">Error al actualizar el cliente</p>';
            }
        }
    }
}

// Mostrar Datos
if (empty($_REQUEST['id'])) {
    header("Location: lista_cliente.php");
    exit;
}

$idcliente = $_REQUEST['id'];
$sql = mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = $idcliente");
$result_sql = mysqli_num_rows($sql);
if ($result_sql == 0) {
    header("Location: lista_cliente.php");
    exit;
} else {
    while ($data = mysqli_fetch_array($sql)) {
        $idcliente = $data['idcliente'];
        $dni = $data['dni'];
        $nombre = $data['nombre'];
        $apellido_paterno = $data['apellido_paterno'];
        $apellido_materno = $data['apellido_materno'];
        $pais = $data ['pais'];
        $calle = $data['calle'];
        $municipio = $data['municipio'];
        $estado = $data['estado'];
        $cp = $data['CP'];
        $telefono = $data['telefono'];
    }
}

?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 m-auto">
            <form action="" method="post">
                <?php echo isset($alert) ? $alert : ''; ?>
                <input type="hidden" name="id" value="<?php echo $idcliente; ?>">
                <div class="form-group">
                    <label for="dni">DNI</label>
                    <input type="number" placeholder="Ingrese DNI" name="dni" id="dni" class="form-control" value="<?php echo $dni; ?>">
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" placeholder="Ingrese Nombre" name="nombre" class="form-control" id="nombre" value="<?php echo $nombre; ?>">
                </div>
                <div class="form-group">
                    <label for="apellido_paterno">Apellido Paterno</label>
                    <input type="text" placeholder="Ingrese Apellido Paterno" name="apellido_paterno" class="form-control" id="apellido_paterno" value="<?php echo $apellido_paterno; ?>">
                </div>
                <div class="form-group">
                    <label for="apellido_materno">Apellido Materno</label>
                    <input type="text" placeholder="Ingrese Apellido Materno" name="apellido_materno" class="form-control" id="apellido_materno" value="<?php echo $apellido_materno; ?>">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="number" placeholder="Ingrese Teléfono" name="telefono" class="form-control" id="telefono" value="<?php echo $telefono; ?>">
                </div>

                <div class="form-group">
    <label for="pais">País</label>
    <select name="pais" id="pais" class="form-control" required>
        <option value="Mexico">Mexico</option>
        <!-- Agrega más opciones de países según sea necesario -->
    </select>
</div>

<div class="form-group" id="estados-mexico">
    <label for="estado">Estado</label>
    <select name="estado" class="form-control">
        <?php
        $estados_mexico = [
            "Aguascalientes",
            "Baja California",
            "Baja California Sur",
            "Campeche",
            "Chiapas",
            "Chihuahua",
            "Coahuila",
            "Colima",
            "Durango",
            "Guanajuato",
            "Guerrero",
            "Hidalgo",
            "Jalisco",
            "México City",
            "Mexico State",
            "Michoacán",
            "Morelos",
            "Nayarit",
            "Nuevo León",
            "Oaxaca",
            "Puebla",
            "Querétaro",
            "Quintana Roo",
            "San Luis Potosí",
            "Sinaloa",
            "Sonora",
            "Tabasco",
            "Tamaulipas",
            "Tlaxcala",
            "Veracruz",
            "Yucatán",
            "Zacatecas"
        ];

        foreach ($estados_mexico as $estado_mexico) {
            $selected = ($estado == $estado_mexico) ? "selected" : "";
            echo "<option value='$estado_mexico' $selected>$estado_mexico</option>";
        }
        ?>
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
    <label for="calle">Calle</label>
    <input type="text" placeholder="Ingrese Calle" name="calle" id="calle" class="form-control" value="<?php echo isset($_POST['calle']) ? $_POST['calle'] : $calle; ?>">
</div>
<div class="form-group">
    <label for="municipio">Municipio</label>
    <input type="text" placeholder="Ingrese Municipio" name="municipio" id="municipio" class="form-control" value="<?php echo isset($_POST['municipio']) ? $_POST['municipio'] : $municipio; ?>">
</div>
                <div class="form-group">
                    <label for="cp">Código Postal</label>
                    <input type="number" placeholder="Ingrese Código Postal" name="cp" class="form-control" id="cp" value="<?php echo $cp; ?>">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-user-edit"></i> Editar Cliente</button>
            </form>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include_once "includes/footer.php"; ?>
