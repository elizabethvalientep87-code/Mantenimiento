<?php
session_start();

// Validación del cliente
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION["tipo"] != 1) {
    header("Location: indexAdmin.php");
    exit();
}

include "menuCliente.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Artículo</title>
    <link rel="stylesheet" href="style/style.css">
</head>

<body>

<div class="contenedor">
    <h1>Agregar Nuevo Artículo</h1>

    <form action="procesarArticulo.php" method="POST" enctype="multipart/form-data" class="formulario">

        <label>Nombre del artículo:</label>
        <input type="text" name="nombre" required>

        <label>Descripción:</label>
        <textarea name="descripcion" rows="3"></textarea>

        <label>Valor estimado:</label>
        <input type="number" step="0.01" name="valor_estimado">

        <label>Fecha de adquisición:</label>
        <input type="date" name="fecha_adquisicion" required>

        <label>Categoría:</label>
        <select name="id_categoria" required>
            <option value="">Seleccione una categoría</option>

            <?php
            require "config.php";
            $query = $conn->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre");

            while ($row = $query->fetch_assoc()) {
                echo "<option value='".$row['id_categoria']."'>".$row['nombre']."</option>";
            }
            ?>
        </select>

        <label>Factura (PDF):</label>
        <input type="file" name="factura_pdf" accept="application/pdf">

        <label>Imágenes del artículo (puede subir varias):</label>
        <input type="file" name="imagenes[]" accept="image/*" multiple>

        <button type="submit" class="btn">Guardar artículo</button>
    </form>
</div>
</body>
</html>
