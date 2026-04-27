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
    <title>Usuarios - Panel Administrador</title>
    <link rel="stylesheet" href="style/style.css"> <!-- Tu CSS -->
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
    <h1>Usuarios Registrados</h1>
    <p>Administrar usuarios del sistema.</p>

    <div class="contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT id_usuario, nombre, correo, tipo FROM usuarios";
                $result = $conn->query($sql);

                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        $tipo = ($row['tipo'] == 2) ? "Administrador" : "Usuario";
                        echo "<tr>
                            <td>{$row['id_usuario']}</td>
                            <td>{$row['nombre']}</td>
                            <td>{$row['correo']}</td>
                            <td>{$tipo}</td>
                            <td>
                                <a href='editarUsuario.php?id={$row['id_usuario']}' class='btnTabla editar'>Editar</a>
                                <a href='eliminarUsuario.php?id={$row['id_usuario']}' class='btnTabla eliminar'>Eliminar</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay usuarios registrados</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Botón para regresar al indexAdmin -->
        <div style="margin-top:20px; text-align:right;">
            <a href="indexAdmin.php" class="btn">Volver al Panel</a>
        </div>
    </div>
</div>

</body>
</html>
