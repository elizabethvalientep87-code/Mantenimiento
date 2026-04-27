<?php
include "config.php";

if (!isset($_POST["id_articulo"])) {
    die("ID inválido");
}

$id_articulo = intval($_POST["id_articulo"]);
$nombre = $_POST["nombre"];
$id_categoria = $_POST["id_categoria"];
$descripcion = $_POST["descripcion"];
$valor_estimado = $_POST["valor_estimado"];
$fecha_adquisicion = $_POST["fecha_adquisicion"];

// -------------------------------------
// 1. ACTUALIZAR DATOS DEL ARTÍCULO
// -------------------------------------
$stmt = $conn->prepare("
    UPDATE articulos 
    SET nombre = ?, id_categoria = ?, descripcion = ?, valor_estimado = ?, fecha_adquisicion = ?
    WHERE id_articulo = ?
");

$stmt->bind_param(
    "sisdsi",
    $nombre,
    $id_categoria,
    $descripcion,
    $valor_estimado,
    $fecha_adquisicion,
    $id_articulo
);

$stmt->execute();

// -------------------------------------
// 2. SUBIR NUEVAS IMÁGENES
// -------------------------------------
if (!empty($_FILES["imagenes"]["name"][0])) {

    // Carpeta donde se guardan
    $carpeta = "imagenes/";

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    foreach ($_FILES["imagenes"]["tmp_name"] as $key => $tmp_name) {

        if ($_FILES["imagenes"]["error"][$key] == 0) {

            $nombreOriginal = basename($_FILES["imagenes"]["name"][$key]);
            $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);

            // Nuevo nombre único
            $nuevoNombre = time() . "_" . rand(1000, 9999) . "." . $extension;

           $carpeta = "imagenes/";
$rutaCompleta = $carpeta . $nuevoNombre;

// mover archivo
move_uploaded_file($tmp_name, $rutaCompleta);

// *** GUARDAR SOLO EL NOMBRE DEL ARCHIVO ***
//$rutaAguardar = $nuevoNombre;

$stmtImg = $conn->prepare("
    INSERT INTO imagenes (id_articulo, ruta)
    VALUES (?, ?)
");
$stmtImg->bind_param("is", $id_articulo, $nuevoNombre);
$stmtImg->execute();

        }
    }
}


// ----------------------
// SUBIR / REEMPLAZAR FACTURA (opcional)
// ----------------------
if (!empty($_FILES['factura_pdf']['name'])) {

    $directorioPDF = "facturas/";
    if (!is_dir($directorioPDF)) mkdir($directorioPDF, 0777, true);

    $nombrePDF = time() . "_" . basename($_FILES['factura_pdf']['name']);
    $rutaCompleta = $directorioPDF . $nombrePDF;

    if (move_uploaded_file($_FILES['factura_pdf']['tmp_name'], $rutaCompleta)) {

        // Comprobar si ya existe factura para este artículo
        $check = $conn->prepare("SELECT id_factura, archivo_pdf FROM facturas WHERE id_articulo = ?");
        $check->bind_param("i", $id_articulo);
        $check->execute();
        $resCheck = $check->get_result();

        if ($resCheck->num_rows > 0) {
            // Borrar archivo viejo (si existe)
            $old = $resCheck->fetch_assoc();
            if (!empty($old['archivo_pdf'])) {
                $oldPath = (strpos($old['archivo_pdf'], 'facturas/') === 0) ? $old['archivo_pdf'] : 'facturas/' . $old['archivo_pdf'];
                if (file_exists($oldPath)) unlink($oldPath);
            }
            // Actualizar registro con nuevo nombre (aquí guardamos ruta relativa 'facturas/nombre' o sólo nombre según tu estándar)
            $stmtUpd = $conn->prepare("UPDATE facturas SET archivo_pdf = ?, fecha_subida = NOW() WHERE id_articulo = ?");
            // Decide qué guardar: si quieres guardar solo el NOMBRE usa $nombrePDF, si prefieres la ruta, usa $rutaCompleta
            $stmtUpd->bind_param("si", $rutaCompleta, $id_articulo);
            $stmtUpd->execute();
        } else {
            // Insertar nuevo registro
            $stmtIns = $conn->prepare("INSERT INTO facturas (id_articulo, archivo_pdf, fecha_subida) VALUES (?, ?, NOW())");
            $stmtIns->bind_param("is", $id_articulo, $rutaCompleta);
            $stmtIns->execute();
        }
    }
}



header("Location: verArticulo.php?id=" . $id_articulo);
exit();
?>
