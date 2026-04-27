<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

require "config.php";

if (!isset($_GET["id"])) {
    echo "Artículo no especificado.";
    exit();
}

$id_usuario = $_SESSION["usuario_id"];
$id_articulo = intval($_GET["id"]);

// ---------------------------------------------
// 1. OBTENER DATOS DEL ARTÍCULO
// ---------------------------------------------
$stmt = $conn->prepare("
    SELECT a.*, c.nombre AS categoria_nombre 
    FROM articulos a
    INNER JOIN categorias c ON a.id_categoria = c.id_categoria
    WHERE a.id_articulo = ? AND a.id_usuario = ?
");
$stmt->bind_param("ii", $id_articulo, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Artículo no encontrado.";
    exit();
}

$articulo = $result->fetch_assoc();

// ---------------------------------------------
// 2. OBTENER IMÁGENES
// ---------------------------------------------
$stmtImg = $conn->prepare("SELECT * FROM imagenes WHERE id_articulo = ?");
$stmtImg->bind_param("i", $id_articulo);
$stmtImg->execute();
$imagenes = $stmtImg->get_result();

// ---------------------------------------------
// 3. OBTENER CATEGORÍAS
// ---------------------------------------------
$cats = $conn->query("SELECT * FROM categorias ORDER BY nombre ASC");

// ---------------------------------------------------------
// 3b. OBTENER FACTURAS (si existen) - evita variable indefinida
// ---------------------------------------------------------
$stmt_fact = $conn->prepare("SELECT id_factura, archivo_pdf FROM facturas WHERE id_articulo = ?");
$stmt_fact->bind_param("i", $id_articulo);
$stmt_fact->execute();
$facturas = $stmt_fact->get_result(); // puede ser 0 o más filas


?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Artículo</title>

<style>
    body {
        font-family: Arial;
        background-color: #f2f2f2;
        padding: 20px;
    }

    .contenedor {
        max-width: 850px;
        margin: auto;
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 0 8px rgba(0,0,0,0.15);
    }

    h2 {
        color: #7b002c;
        margin-bottom: 15px;
    }

    label {
        font-weight: bold;
        display: block;
        margin-top: 10px;
    }

    input[type="text"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 6px;
        margin-top: 5px;
    }

    textarea {
        height: 80px;
    }

    button {
        margin-top: 20px;
        padding: 12px 18px;
        background: #990000;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background: #7b002c;
    }

    .imagenes {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 10px;
    }

    .imagenes img {
        width: 140px;
        height: 140px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #ddd;
    }

    .btn-eliminar {
        display: block;
        margin-top: 5px;
        background: #cc0000;
        color: white;
        padding: 6px;
        font-size: 14px;
        border-radius: 5px;
        text-align: center;
        text-decoration: none;
    }

    .btn-eliminar:hover {
        background: #a00000;
    }

</style>

</head>
<body>

<div class="contenedor">

    <h2>Editar Artículo</h2>

    <form action="procesarEditar.php" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="id_articulo" value="<?= $id_articulo ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($articulo["nombre"]) ?>" required>

        <label>Categoría:</label>
        <select name="id_categoria" required>
            <?php while ($c = $cats->fetch_assoc()) { ?>
                <option value="<?= $c["id_categoria"] ?>"
                    <?= ($c["id_categoria"] == $articulo["id_categoria"]) ? "selected" : "" ?>>
                    <?= htmlspecialchars($c["nombre"]) ?>
                </option>
            <?php } ?>
        </select>

        <label>Descripción:</label>
        <textarea name="descripcion"><?= htmlspecialchars($articulo["descripcion"]) ?></textarea>

        <label>Valor estimado:</label>
        <input type="number" step="0.01" name="valor_estimado"
               value="<?= $articulo["valor_estimado"] ?>">

        <label>Fecha de adquisición:</label>
        <input type="date" name="fecha_adquisicion"
               value="<?= $articulo["fecha_adquisicion"] ?>">

        <hr>

        <h3>Imágenes actuales</h3>

        <div class="imagenes">
    <?php while ($img = $imagenes->fetch_assoc()) { ?>
        
        <div>
            <img src="imagenes/<?= htmlspecialchars($img["ruta"]) ?>">
            
            <a class="btn-eliminar"
               href="eliminarImagen.php?id_img=<?= $img["id_imagen"] ?>&id_articulo=<?= $id_articulo ?>"
               onclick="return confirm('¿Eliminar imagen?');">
               Eliminar
            </a>
        </div>

    <?php } ?>
</div>


        <br>

        <label>Agregar nuevas imágenes:</label><br>
        <input type="file" name="imagenes[]" multiple accept="image/*">


<hr>
<h3>Factura</h3>

<?php
if (isset($facturas) && $facturas->num_rows > 0) {
    // Si permites una sola factura, puedes tomar la primera:
    $f = $facturas->fetch_assoc();
    // Si en BD guardaste sólo el nombre del archivo (ej: 1764_fact.pdf), la ruta completa será:
    $rutaFact = (strpos($f['archivo_pdf'], 'facturas/') === 0) ? $f['archivo_pdf'] : 'facturas/' . $f['archivo_pdf'];

    echo '<p>Factura actual: <a class="pdf-link" href="'.htmlspecialchars($rutaFact).'" target="_blank">Ver factura</a></p>';
} else {
    echo '<p>No se ha subido factura.</p>';
}
?>

<!-- Input para subir/ reemplazar factura -->
<label>Reemplazar factura (PDF):</label>
<input type="file" name="factura_pdf" accept="application/pdf">


        <button type="submit">Guardar Cambios</button>


    </form>

    <br>
    <a href="miInventario.php?id=<?= $id_articulo ?>">Regresar</a>

</div>

</body>
</html>
