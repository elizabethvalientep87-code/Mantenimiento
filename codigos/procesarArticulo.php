<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

require "config.php";

$id_usuario = $_SESSION["usuario_id"];
$nombre = $_POST["nombre"];
$descripcion = $_POST["descripcion"];
$valor_estimado = $_POST["valor_estimado"];
$fecha_adquisicion = $_POST["fecha_adquisicion"];
$id_categoria = $_POST["id_categoria"];


// 1. Insertar el artículo en la tabla articulos
$stmt = $conn->prepare("INSERT INTO articulos 
    (id_usuario, id_categoria, nombre, descripcion, valor_estimado, fecha_adquisicion, fecha_registro)
    VALUES (?, ?, ?, ?, ?, ?, NOW())");

$stmt->bind_param("iissds", $id_usuario, $id_categoria, $nombre, $descripcion, $valor_estimado, $fecha_adquisicion);
$stmt->execute();

$id_articulo = $stmt->insert_id; // ID del artículo recién creado

//---------------------------------------------
// 2. SUBIR FACTURA PDF
//---------------------------------------------
if (!empty($_FILES["factura_pdf"]["name"])) {
    
    $directorioPDF = "facturas/";
    if (!file_exists($directorioPDF)) {
        mkdir($directorioPDF, 0777, true);
    }

    $nombrePDF = time() . "_" . basename($_FILES["factura_pdf"]["name"]);
    $rutaPDF = $directorioPDF . $nombrePDF;

    if (move_uploaded_file($_FILES["factura_pdf"]["tmp_name"], $rutaPDF)) {

        $stmt_pdf = $conn->prepare("INSERT INTO facturas (id_articulo, archivo_pdf, fecha_subida) VALUES (?, ?, NOW())");
        $stmt_pdf->bind_param("is", $id_articulo, $rutaPDF);
        $stmt_pdf->execute();
    }
}

//---------------------------------------------
// 3. SUBIR IMÁGENES (MÚLTIPLES)
//---------------------------------------------
if (!empty($_FILES["imagenes"]["name"][0])) {

    $directorioIMG = "imagenes/";
    if (!file_exists($directorioIMG)) {
        mkdir($directorioIMG, 0777, true);
    }

    foreach ($_FILES["imagenes"]["name"] as $index => $imgName) {

        $nuevoNombre = time() . "_" . $imgName;
        $rutaImg = $directorioIMG . $nuevoNombre;

        if (move_uploaded_file($_FILES["imagenes"]["tmp_name"][$index], $rutaImg)) {

            $stmt_img = $conn->prepare("INSERT INTO imagenes (id_articulo, ruta) VALUES (?, ?)");
            $stmt_img->bind_param("is", $id_articulo, $nuevoNombre);
            $stmt_img->execute();
        }
    }
}

//---------------------------------------------
// 4. Redirección final
//---------------------------------------------
header("Location: miInventario.php?exito=1");
exit();

?>
