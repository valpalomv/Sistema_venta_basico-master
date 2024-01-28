<?php
session_start();
if (!isset($_SESSION['idUser'])) {
    header('Location: login.php');
    exit();
}

// Aquí puedes incluir la lógica de la página principal para los clientes
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal del Cliente</title>
    <!-- Agrega tus enlaces a CSS y JS aquí -->
</head>
<body>
    <h1>Bienvenido al Portal del Cliente</h1>
    <!-- Contenido de la página principal -->
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
