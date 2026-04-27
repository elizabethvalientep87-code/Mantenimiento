<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION["usuario_id"]) || $_SESSION["tipo"] != 2) {
    header("Location: login.php");
    exit();
}

require "config.php"; // conexión a la base de datos

// Obtener ID del usuario a eliminar
$id_usuario = $_GET['id'] ?? null;

if(!$id_usuario){
    header("Location: usuarios.php");
    exit();
}

// Evitar que el administrador se elimine a sí mismo
if($id_usuario == $_SESSION["usuario_id"]){
    $mensaje = "No puedes eliminar tu propia cuenta.";
    $tipo_alerta = "error";
} else {
    // Confirmación por POST
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST['confirmar']) && $_POST['confirmar'] == "Sí"){
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            $stmt->bind_param("i", $id_usuario);
            if($stmt->execute()){
                header("Location: usuarios.php?msg=Usuario eliminado correctamente");
                exit();
            } else {
                $mensaje = "Error al eliminar el usuario.";
                $tipo_alerta = "error";
            }
        } else {
            // Cancelar
            header("Location: usuarios.php");
            exit();
        }
    }

    // Obtener datos del usuario para mostrar
    $stmt = $conn->prepare("SELECT nombre, correo FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows !== 1){
        header("Location: usuarios.php");
        exit();
    }

    $user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Usuario</title>
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
        <a href="cerrarSesion.php" class="btn-salir">Cerrar sesión</a>
    </div>
</div>

<!-- CONTENIDO -->
<div class="contenido" style="margin-top:100px;">
    <h1>Eliminar Usuario</h1>

    <?php if(!empty($mensaje)): ?>
        <div class="alert <?php echo $tipo_alerta; ?>">
            <?php echo $mensaje; ?>
        </div>
        <a href="usuarios.php" class="btn full" style="background:#555; margin-top:10px;">Volver</a>
    <?php else: ?>
        <div class="form-container">
            <div class="form-card">
                <p>¿Estás seguro de eliminar al usuario <strong><?php echo htmlspecialchars($user['nombre']); ?></strong> con correo <strong><?php echo htmlspecialchars($user['correo']); ?></strong>?</p>
                <form method="POST">
                    <button type="submit" name="confirmar" value="Sí" class="btn full">Sí, eliminar</button>
                    <button type="submit" name="confirmar" value="No" class="btn full" style="background:#555; margin-top:10px;">Cancelar</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
