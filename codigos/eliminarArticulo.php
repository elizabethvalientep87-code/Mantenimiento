<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

require "config.php";

if (!isset($_GET["id"])) {
    die("ID de artículo no recibido.");
}

$id_usuario = $_SESSION["usuario_id"];
$id_articulo = intval($_GET["id"]);

//-------------------------------------------------------
// 1. VALIDAR QUE EL ARTÍCULO PERTENECE AL USUARIO
//-------------------------------------------------------
$stmt = $conn->prepare("SELECT id_articulo FROM articulos WHERE id_articulo = ? AND id_usuario = ?");
$stmt->bind_param("ii", $id_articulo, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("No tienes permiso para borrar este artículo.");
}


//-------------------------------------------------------
// 2. ELIMINAR IMÁGENES FÍSICAS Y REGISTROS
//-------------------------------------------------------
$stmt_img = $conn->prepare("SELECT ruta FROM imagenes WHERE id_articulo = ?");
$stmt_img->bind_param("i", $id_articulo);
$stmt_img->execute();
$res_img = $stmt_img->get_result();

while ($img = $res_img->fetch_assoc()) {
    $rutaImg = "imagenes/" . $img["ruta"];
    if (file_exists($rutaImg)) {
        unlink($rutaImg);
    }
}

$stmtDelImg = $conn->prepare("DELETE FROM imagenes WHERE id_articulo = ?");
$stmtDelImg->bind_param("i", $id_articulo);
$stmtDelImg->execute();


//-------------------------------------------------------
// 3. ELIMINAR FACTURA PDF
//-------------------------------------------------------
$stmt_pdf = $conn->prepare("SELECT archivo_pdf FROM facturas WHERE id_articulo = ?");
$stmt_pdf->bind_param("i", $id_articulo);
$stmt_pdf->execute();
$res_pdf = $stmt_pdf->get_result();

if ($pdf = $res_pdf->fetch_assoc()) {
    if (file_exists($pdf["archivo_pdf"])) {
        unlink($pdf["archivo_pdf"]);
    }
}

$stmtDelPDF = $conn->prepare("DELETE FROM facturas WHERE id_articulo = ?");
$stmtDelPDF->bind_param("i", $id_articulo);
$stmtDelPDF->execute();


//-------------------------------------------------------
// 4. ELIMINAR EL ARTÍCULO
//-------------------------------------------------------
$stmtDelArticulo = $conn->prepare("DELETE FROM articulos WHERE id_articulo = ?");
$stmtDelArticulo->bind_param("i", $id_articulo);
$stmtDelArticulo->execute();


//-------------------------------------------------------
// 5. REDIRECCIÓN FINAL
//-------------------------------------------------------
header("Location: miInventario.php?eliminado=1");
exit();
?>
