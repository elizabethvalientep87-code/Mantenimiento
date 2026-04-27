<?php
include "config.php";

if (!isset($_GET["id_img"]) || !isset($_GET["id_articulo"])) {
    die("Parámetros incorrectos");
}

$id_imagen = intval($_GET["id_img"]);
$id_articulo = intval($_GET["id_articulo"]);

// 1. Obtener la ruta almacenada en BD
$stmt = $conn->prepare("SELECT ruta FROM imagenes WHERE id_imagen = ?");
$stmt->bind_param("i", $id_imagen);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Imagen no encontrada");
}

$img = $result->fetch_assoc();

// IMPORTANTE: Aquí agregamos la carpeta correcta
$rutaArchivo = "imagenes/" . $img["ruta"];

// 2. Eliminar archivo físico
if (file_exists($rutaArchivo)) {
    unlink($rutaArchivo);
}

// 3. Eliminar registro de BD
$stmtDel = $conn->prepare("DELETE FROM imagenes WHERE id_imagen = ?");
$stmtDel->bind_param("i", $id_imagen);
$stmtDel->execute();

// 4. Regresar al editor
header("Location: editarArticulo.php?id=" . $id_articulo);
exit();

?>
