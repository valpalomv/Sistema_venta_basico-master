<?php
require('fpdf/fpdf.php'); // Asegúrate de que la ruta a FPDF sea correcta

if (isset($_GET['cl']) && isset($_GET['f'])) {
    $codCliente = $_GET['cl'];
    $nofactura = $_GET['f'];

    // Crear un nuevo objeto FPDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Resto de tu código para generar el PDF, similar al proporcionado anteriormente.

    $pdfFileName = 'ticket_compra.pdf';

    if ($pdf->Output($pdfFileName, 'D')) {
        exit; // Termina el script si la generación del PDF falla
    }

    // Redirige de nuevo a la página principal o muestra un mensaje de éxito.
    header('Location: generaFactura.php'); // Cambia "generaFactura.php" al nombre de tu página principal.

    // Asegúrate de no tener ninguna salida de texto o HTML después de esta línea.
} else {
    echo "No es posible generar la factura.";
}
?>
