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
    <title>Categorías - Panel Administrador</title>
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
    <h1>Categorías de Artículos</h1>
    <p>Administrar las categorías disponibles en el inventario.</p>

    <div class="contenedor">
        <a href="agregarCategoria.php" class="btn" style="margin-bottom:15px;">Agregar Nueva Categoría</a>

        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT id_categoria, nombre FROM categorias";
                $result = $conn->query($sql);

                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        echo "<tr>
                            <td>{$row['id_categoria']}</td>
                            <td>{$row['nombre']}</td>
                            <td>
                                <a href='editarCategoria.php?id={$row['id_categoria']}' class='btnTabla editar'>Editar</a>
                                <a href='eliminarCategoria.php?id={$row['id_categoria']}' class='btnTabla eliminar'>Eliminar</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No hay categorías registradas</td></tr>";
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
