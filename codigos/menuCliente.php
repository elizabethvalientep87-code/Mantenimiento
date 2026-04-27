<?php
// Iniciar sesión si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar que el usuario haya iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

// Validar que sea tipo = 1 (cliente)
if ($_SESSION["tipo"] != 1) {
    header("Location: indexAdmin.php");
    exit();
}
?>

<!-- MENÚ DEL CLIENTE -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css"> <!-- Tu CSS global -->
</head>

<body>
<header class="menu">
    <div class="menu-logo">
        <h2>Inventario Personal</h2>
    </div>

    <nav class="menu-links">
        <a href="indexCliente.php">Inicio</a>
        <a href="miInventario.php">Mi Inventario</a>
        <a href="agregarArticulo.php">Agregar Artículo</a>
        <a href="generarReporte.php">Generar Reporte</a>
        <a href="verPerfil.php">Mi Perfil</a>
        <a href="salir.php" class="btn-salir">Cerrar Sesión</a>
    </nav>
</header>

<!-- ESPACIO PARA QUE EL CONTENIDO NO SE OCUPE EL HEADER -->
<div style="margin-top: 90px;"></div>

</body>
</html>
