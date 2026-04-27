<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION["usuario_id"]) || $_SESSION["tipo"] != 2) {
    header("Location: login.php");
    exit();
}

require "config.php"; // conexión a la base de datos

// Obtener ID de la categoría
$id = $_GET['id'] ?? '';
if (empty($id)) {
    header("Location: categorias.php");
    exit();
}

$error = "";
$success = "";

// Eliminar categoría
$stmt = $conn->prepare("DELETE FROM categorias WHERE id_categoria = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    $success = "Categoría eliminada correctamente.";
} else {
    $error = "No se pudo eliminar la categoría. Es posible que esté en uso en algún artículo.";
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Categoría - Panel Administrador</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

<!-- MENÚ SUPERIOR FIJO -->
<div class="menu">
    <div class="menu-logo">
        <h2>Inventario Admin</h2>
    </div>
    <div class="menu-links">
        <span>Bienvenido, <?php echo $_SESSION["usuario_nombre"]; ?></span>
        <a href="perfilAdmin.php">Perfil</a>
        <a href="salir.php" class="btn-salir">Cerrar sesión</a>
    </div>
</div>

<!-- CONTENIDO PRINCIPAL -->
<div class="contenido" style="margin-top:100px;">
    <h1>Eliminar Categoría</h1>
    <div class="contenedor">
        <?php if($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php else: ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Botones de navegación -->
        <div style="margin-top:20px; text-align:right;">
            <a href="categorias.php" class="btn">Volver al Listado</a>
            <a href="indexAdmin.php" class="btn">Ir al Panel Administrador</a>
        </div>
    </div>
</div>

</body>
</html>
