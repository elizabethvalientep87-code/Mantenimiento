<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION["usuario_id"]) || $_SESSION["tipo"] != 2) {
    header("Location: login.php");
    exit();
}

require "config.php"; // conexión a la base de datos

// Inicializar variables
$nombre = "";
$error = "";
$success = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);

    if (empty($nombre)) {
        $error = "El nombre de la categoría no puede estar vacío.";
    } else {
        // Insertar en la base de datos
        $stmt = $conn->prepare("INSERT INTO categorias (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        if ($stmt->execute()) {
            $success = "Categoría agregada correctamente.";
            $nombre = ""; // limpiar input
        } else {
            $error = "Error al agregar la categoría: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Categoría - Panel Administrador</title>
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
    <h1>Agregar Nueva Categoría</h1>
    <p>Complete el siguiente formulario para agregar una categoría al inventario.</p>

    <div class="contenedor">
        <?php if($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="post" class="formulario">
            <label for="nombre">Nombre de la Categoría:</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

            <button type="submit" class="btn full">Agregar Categoría</button>
        </form>

        <!-- Botón para regresar al listado de categorías -->
        <div style="margin-top:20px; text-align:right;">
            <a href="categorias.php" class="btn">Volver al Listado</a>
        </div>
    </div>
</div>

</body>
</html>
