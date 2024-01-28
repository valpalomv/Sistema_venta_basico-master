<?php
session_start();

if (empty($_SESSION['active'])) {
    header('Location: ../');
    exit; // Termina el script después de redirigir
}

if (empty($_REQUEST['cl']) || empty($_REQUEST['f'])) {
    echo "No es posible generar la factura.";
    exit; // Termina el script si falta información
} else {
    $codCliente = $_REQUEST['cl'];
    $nofactura = $_REQUEST['f'];

    // Obtén los datos de la base de datos
    include "../../conexion.php";
    $consulta = mysqli_query($conexion, "SELECT * FROM configuracion");
    $resultado = mysqli_fetch_assoc($consulta);
    $factura = mysqli_query($conexion, "SELECT * FROM factura WHERE nofactura = $nofactura");
    $result_venta = mysqli_fetch_assoc($factura);
    $clientes = mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = $codCliente");
    $result_cliente = mysqli_fetch_assoc($clientes);
    $productos = mysqli_query($conexion, "SELECT d.nofactura, d.codproducto, d.cantidad, p.codproducto, p.descripcion, p.precio, d.cambio, d.pago FROM detallefactura d INNER JOIN producto p ON d.codproducto = p.codproducto WHERE d.nofactura = $nofactura");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ticket de Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        img {
            display: block;
            margin: 0 auto;
        }

        p {
            text-align: center;
        }

        h2 {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ccc;
            margin-right: auto;
            margin-left: auto;
        }

        th, td {
            padding: 8px;
            text-align: left;
            margin-right: auto;
            margin-left: auto;
        }
    </style>
</head>
<body>
<h1>Ticket de Compra</h1>

<p><?php echo $resultado['nombre']; ?></p>
<img src="img/logo.jpg" alt="Logo" width="100" height="100">

<p>ID: <?php echo $resultado['dni']; ?></p>
<p>Teléfono: <?php echo $resultado['telefono']; ?></p>

<p>Ticket: <?php echo $nofactura; ?></p>
<p>Fecha: <?php echo $result_venta['fecha']; ?></p>

<h2>Datos del cliente</h2>
<p>Nombre: <?php echo $result_cliente['nombre']; ?></p>
<p>Apellido Materno: <?php echo $result_cliente['apellido_materno']; ?></p>
<p>Apellido Paterno: <?php echo $result_cliente['apellido_paterno']; ?></p>
<p>Teléfono: <?php echo $result_cliente['telefono']; ?></p>
<p>Calle: <?php echo $result_cliente['calle']; ?></p>
<p>Municipio: <?php echo $result_cliente['municipio']; ?></p>
<p>Estado: <?php echo $result_cliente['estado']; ?></p>
<p>País: <?php echo $result_cliente['pais']; ?></p>
<p>Código Postal: <?php echo $result_cliente['CP']; ?></p>

<h2>Detalle de Productos</h2>
<table>
    <tr>
        <th>Descripción</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Total</th>
    </tr>
    <?php
    $totalCompra = 0; // Variable para calcular el total de la compra

    while ($row = mysqli_fetch_assoc($productos)) {
        $subtotal = $row['cantidad'] * $row['precio'];
        $totalCompra += $subtotal;
        echo '<tr>
            <td>' . $row['descripcion'] . '</td>
            <td>' . $row['cantidad'] . '</td>
            <td>' . number_format($row['precio'], 2, '.', ',') . '</td>
            <td>' . number_format($subtotal, 2, '.', ',') . '</td>
        </tr>';
    }
    ?>
</table>

<h2>Total: <?php echo number_format($totalCompra, 2, '.', ','); ?></h2>

<p>Gracias por su preferencia</p>
<br><nr>
<center>
<button onclick="printPage()">Imprimir</button></center>
<script>
    function printPage() {
        window.print();
    }
</script>
</body>
</html>
