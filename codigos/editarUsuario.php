<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION["usuario_id"]) || $_SESSION["tipo"] != 2) {
    header("Location: login.php");
    exit();
}

require "config.php"; // conexión a la base de datos

// Obtener el ID del usuario a editar
$id_usuario = $_GET['id'] ?? null;

if(!$id_usuario){
    header("Location: usuarios.php");
    exit();
}

// Obtener datos actuales del usuario
$stmt = $conn->prepare("SELECT nombre, correo, tipo FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows !== 1){
    header("Location: usuarios.php");
    exit();
}

$user = $result->fetch_assoc();

// Procesar formulario
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $tipo = $_POST['tipo'] ?? 1;

    $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, correo = ?, tipo = ? WHERE id_usuario = ?");
    $stmt->bind_param("ssii", $nombre, $correo, $tipo, $id_usuario);

    if($stmt->execute()){
        $mensaje = "Usuario actualizado correctamente.";
        $tipo_alerta = "success";
    } else {
        $mensaje = "Error al actualizar el usuario.";
        $tipo_alerta = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
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

<!-- CONTENIDO -->
<div class="contenido" style="margin-top:100px;">
    <h1>Editar Usuario</h1>

    <?php if(!empty($mensaje)): ?>
        <div class="alert <?php echo $tipo_alerta; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form class="form-card" method="POST">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>

            <label for="correo">Correo</label>
            <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($user['correo']); ?>" required>

            <label for="tipo">Tipo de Usuario</label>
            <select name="tipo" id="tipo" required>
                <option value="1" <?php if($user['tipo'] == 1) echo "selected"; ?>>Usuario</option>
                <option value="2" <?php if($user['tipo'] == 2) echo "selected"; ?>>Administrador</option>
            </select>

            <button type="submit" class="btn full">Actualizar Usuario</button>
            <a href="usuarios.php" class="btn full" style="background:#555; margin-top:10px;">Cancelar</a>
        </form>
    </div>
</div>

</body>
</html>
