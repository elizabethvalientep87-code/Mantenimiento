<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION["usuario_id"]) || $_SESSION["tipo"] != 2) {
    header("Location: login.php");
    exit();
}

require "config.php"; // conexión a la base de datos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrador</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

<!-- MENÚ SUPERIOR -->
<div class="menu">
    <div class="menu-logo">
        <h2>Administración</h2>
    </div>
    <div class="menu-links">
        <span>Bienvenido, <?php echo $_SESSION["usuario_nombre"]; ?></span>
        <a href="perfilAdmin.php">Perfil</a>
        <a href="salir.php" class="btn-salir">Cerrar sesión</a>
    </div>
</div>

<!-- CONTENIDO PRINCIPAL -->
<div class="contenido" style="margin-top:100px;">
    <h1>Panel de Administración</h1>
    <p>Aquí puede administrar el sistema</p>

    <div class="tarjetas">
        <!-- Usuarios -->
        <div class="card">
            <h3>Usuarios</h3>
            <p>Ver, editar o eliminar usuarios registrados.</p>
            <a href="usuarios.php" class="btn">Ir a Usuarios</a>
        </div>

        <!-- Categorías de Artículos -->
        <div class="card">
            <h3>Categorías de Artículos</h3>
            <p>Administrar categorías del inventario.</p>
            <a href="categorias.php" class="btn">Ir a Categorías</a>
        </div>

        <!-- Perfil -->
        <div class="card">
            <h3>Perfil</h3>
            <p>Ver y actualizar la información del administrador.</p>
            <a href="perfilAdmin.php" class="btn">Ir al Perfil</a>
        </div>
    </div>
</div>

</body>
</html>
