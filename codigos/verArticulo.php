<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

require "config.php";

// Validar ID recibido
if (!isset($_GET["id"])) {
    echo "Artículo no especificado.";
    exit();
}

$id_usuario = $_SESSION["usuario_id"];
$id_articulo = intval($_GET["id"]);

//---------------------------------------------------------
// 1. OBTENER DATOS DEL ARTÍCULO
//---------------------------------------------------------
$stmt = $conn->prepare("
    SELECT a.*, c.nombre AS categoria_nombre 
    FROM articulos a
    INNER JOIN categorias c ON a.id_categoria = c.id_categoria
    WHERE a.id_articulo = ? AND a.id_usuario = ?");

$stmt->bind_param("ii", $id_articulo, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Artículo no encontrado o no tienes permiso para verlo.";
    exit();
}

$articulo = $result->fetch_assoc();


//---------------------------------------------------------
// 2. OBTENER IMÁGENES
//---------------------------------------------------------
$stmt_img = $conn->prepare("SELECT ruta FROM imagenes WHERE id_articulo = ?");
$stmt_img->bind_param("i", $id_articulo);
$stmt_img->execute();
$imagenes = $stmt_img->get_result();


//---------------------------------------------------------
// 3. OBTENER FACTURA PDF
//---------------------------------------------------------
$stmt_pdf = $conn->prepare("SELECT archivo_pdf FROM facturas WHERE id_articulo = ?");
$stmt_pdf->bind_param("i", $id_articulo);
$stmt_pdf->execute();
$factura = $stmt_pdf->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Artículo</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f2f2f2;
            padding: 20px;
        }

        .contenedor {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }

        h2 {
            color: #7b002c;
        }

        .imagenes {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .imagenes img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 2px solid #ddd;
            border-radius: 8px;
        }

        a.pdf-link {
            display: inline-block;
            padding: 10px 15px;
            background: #990000;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 15px;
        }

        a.pdf-link:hover {
            background: #7b002c;
        }

        #imgModal {
    display: none;
    position: fixed;
    z-index: 1000;
    padding-top: 60px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.8);
}

#imgModal img {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 90%;
    border-radius: 10px;
}

#cerrarModal {
    position: absolute;
    top: 20px;
    right: 35px;
    color: white;
    font-size: 40px;
    cursor: pointer;
}

    </style>
</head>
<body>

<div class="contenedor">
    <h2>Detalles del Artículo</h2>

    <p><strong>Nombre:</strong> <?= htmlspecialchars($articulo["nombre"]) ?></p>
    <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($articulo["descripcion"])) ?></p>
    <p><strong>Valor estimado:</strong> $<?= number_format($articulo["valor_estimado"], 2) ?></p>
    <p><strong>Fecha de adquisición:</strong> <?= $articulo["fecha_adquisicion"] ?></p>
    <p><strong>Categoría:</strong> <?= htmlspecialchars($articulo["categoria_nombre"]) ?></p>

    <hr>

    <h3>Imágenes</h3>
    <div class="imagenes">
        <?php 
        if ($imagenes->num_rows > 0) {
            while ($img = $imagenes->fetch_assoc()) {
                echo "<img src='imagenes/" . htmlspecialchars($img["ruta"]) . "' 
                       class='imagenArticulo'
                       style='width:130px; cursor:pointer; border-radius:10px; margin:5px'>";
            }
        } else {
            echo "<p>No se han subido imágenes.</p>";
        }
    ?>
    </div>


    <hr>

    <h3>Factura</h3>
<?php if ($factura) { ?>
    <a class="pdf-link" 
       href="<?= htmlspecialchars($factura["archivo_pdf"]) ?>" 
       target="_blank">
        Ver Factura PDF
    </a>
<?php } else { ?>
    <p>No se ha subido factura.</p>
<?php } ?>


    <br><br>
    <a href="miInventario.php">Regresar a mi inventario</a>
</div>

<div id="imgModal">
    <span id="cerrarModal">&times;</span>
    <img id="imgModalContenido">
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("imgModal");
    const modalImg = document.getElementById("imgModalContenido");
    const cerrar = document.getElementById("cerrarModal");

    document.querySelectorAll(".imagenArticulo").forEach(img => {
        img.onclick = function() {
            modal.style.display = "block";
            modalImg.src = this.src;
        }
    });

    cerrar.onclick = function() {
        modal.style.display = "none";
    }

    modal.onclick = function(e) {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    }
});
</script>
</body>
</html>
