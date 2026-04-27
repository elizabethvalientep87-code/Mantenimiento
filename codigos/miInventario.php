<?php
session_start();

// Verificar sesión de cliente
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 1) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Conectar a la BD
$conn = new mysqli("localhost", "root", "", "inventario");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consultar artículos del usuario
$sql = "SELECT a.id_articulo, a.nombre, a.descripcion, a.valor_estimado, a.fecha_adquisicion, c.nombre AS categoria 
        FROM articulos a
        LEFT JOIN categorias c ON a.id_categoria = c.id_categoria
        WHERE a.id_usuario = ?
        ORDER BY a.fecha_registro DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Inventario</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

    <!-- Menú superior -->
    <nav class="menu">
        <div class="logo">Inventario Personal</div>
        <ul>
            <li><a href="indexCliente.php">Inicio</a></li>
            <li><a href="miInventario.php" class="activo">Mi Inventario</a></li>
            <li><a href="agregarArticulo.php">Agregar Artículo</a></li>
            <li><a href="generarReporte.php">Generar Reportes</a></li>
            <li><a href="verPerfil.php">Mi Perfil</a><li>
            <li><a href="salir.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <!-- Contenido -->
    <div class="contenido">
        <h1>Mis Artículos</h1>
        <p>Aquí puedes ver y gestionar todo tu inventario personal.</p>

        <table class="tabla">
            <thead>
                <tr>
                    <th>Artículo</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Valor Estimado</th>
                    <th>Fecha Adquisición</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row['nombre']."</td>";
                        echo "<td>".$row['descripcion']."</td>";
                        echo "<td>".$row['categoria']."</td>";
                        echo "<td>$".$row['valor_estimado']."</td>";
                        echo "<td>".$row['fecha_adquisicion']."</td>";

                        echo "<td>
                                <a class='btnTabla' href='verArticulo.php?id=".$row['id_articulo']."'>Ver</a>
                                <a class='btnTabla editar' href='editarArticulo.php?id=".$row['id_articulo']."'>Editar</a>
                                <a class='btnTabla eliminar' href='eliminarArticulo.php?id=".$row['id_articulo']."' onclick=\"return confirm('¿Seguro que deseas eliminar este artículo?');\">Eliminar</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No tienes artículos registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>

    </div>

</body>
</html>
