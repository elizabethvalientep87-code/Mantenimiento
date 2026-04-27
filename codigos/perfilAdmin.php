<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION["usuario_id"]) || $_SESSION["tipo"] != 2) {
    header("Location: login.php");
    exit();
}

require "config.php"; // conexión a la base de datos

$id_usuario = $_SESSION["usuario_id"];
$error = "";
$success = "";

// Procesar formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($nombre) || empty($correo)) {
        $error = "Nombre y correo son obligatorios.";
    } else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, correo=?, contrasena=? WHERE id_usuario=?");
            $stmt->bind_param("sssi", $nombre, $correo, $hashed_password, $id_usuario);
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, correo=? WHERE id_usuario=?");
            $stmt->bind_param("ssi", $nombre, $correo, $id_usuario);
        }

        if ($stmt->execute()) {
            $success = "Perfil actualizado correctamente.";
            $_SESSION["usuario_nombre"] = $nombre; // actualizar sesión
        } else {
            $error = "No se pudo actualizar el perfil.";
        }
        $stmt->close();
    }
}

// Obtener datos actuales del usuario
$stmt = $conn->prepare("SELECT nombre, correo FROM usuarios WHERE id_usuario=?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil Administrador</title>
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
        <a href="indexAdmin.php">Panel</a>
        <a href="salir.php" class="btn-salir">Cerrar sesión</a>
    </div>
</div>

<!-- CONTENIDO PRINCIPAL -->
<div class="contenido" style="margin-top:100px;">
    <h1>Perfil del Administrador</h1>
    <div class="contenedor form-container">
        <?php if($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" class="form-card">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>

            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>

            <label for="password">Contraseña (dejar en blanco para no cambiar):</label>
            <input type="password" id="password" name="password">

            <button type="submit" class="btn full">Actualizar Perfil</button>
        </form>

        <div style="margin-top:20px; text-align:right;">
            <a href="indexAdmin.php" class="btn">Volver al Panel Administrador</a>
        </div>
    </div>
</div>

</body>
</html>
