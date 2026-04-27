<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

require "config.php";

$id_usuario = $_SESSION["usuario_id"];

// =======================================================
// OBTENER CATEGORÍAS PARA EL SELECT
// =======================================================
$cats = $conn->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC");

include "menuCliente.php";

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Generar Reporte</title>
    <link rel="stylesheet" href="style/style.css">


<style>
    body {
        font-family: Arial;
        background: #f3f3f3;
        padding: 20px;
    }
    .contenedor {
        max-width: 1000px;
        margin: auto;
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0px 0px 15px rgba(0,0,0,0.1);
    }

    h2 {
        color: #7b002c;
        margin-bottom: 20px;
        text-align: center;
    }

    h3 {
        color: #7b002c;
        margin-top: 35px;
    }

    .bloque {
        padding: 15px;
        background: #faf7f8;
        border-left: 5px solid #7b002c;
        margin-bottom: 30px;
        border-radius: 8px;
    }

    .button {
        padding: 10px 20px;
        background: #b3003c;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
        margin-top: 10px;
    }

    .button:hover {
        background: #7b002c;
    }

    .input-date, select {
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
        margin: 5px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px;
    }

    table, th, td {
        border: 1px solid #ccc;
    }

    th {
        background-color: #7b002c;
        color: white;
        padding: 10px;
        text-align: left;
    }

    td {
        padding: 8px;
        vertical-align: top;
    }

    .img-mini {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ccc;
    }

    .volver {
        display: inline-block;
        margin-top: 25px;
        padding: 10px 15px;
        background: #444;
        color: white;
        border-radius: 6px;
        text-decoration: none;
    }

    .volver:hover {
        background: black;
    }
</style>
</head>
<body>

<div class="contenedor">
    <h2>Generación de Reportes</h2>


<!-- ==========================================
     REPORTE GENERAL
=========================================== -->
<div class="bloque">
    <h3>Reporte General</h3>
    <p>Genera un listado completo de todos tus artículos.</p>

    <form method="GET">
        <button class="button" type="submit" name="reporte" value="general">
            Generar Reporte General
        </button>
    </form>
</div>

<?php
if (isset($_GET["reporte"]) && $_GET["reporte"] === "general") {

    echo "<h3>Reporte General</h3>";

    // LLAMADA AL PROCEDIMIENTO ALMACENADO CORRECTO
    $stmt = $conn->prepare("CALL reporte_general(?)");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();

    $res = $stmt->get_result();

    if ($res->num_rows > 0) {

        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Valor Estimado</th>
                    <th>Fecha de Adquisición</th>
                </tr>";

        while ($row = $res->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["id_articulo"] . "</td>
                    <td>" . htmlspecialchars($row["nombre"]) . "</td>
                    <td>" . htmlspecialchars($row["categoria"]) . "</td>
                    <td>$" . number_format($row["valor_estimado"], 2) . "</td>
                    <td>" . $row["fecha_adquisicion"] . "</td>
                </tr>";
        }

        echo "</table><br>";

    } else {
        echo "<p>No se encontraron resultados.</p>";
    }

    $stmt->close();
    $conn->next_result(); // Muy importante para permitir siguientes consultas
    unset($_GET["reporte"]);

}
?>


    <!-- ==========================================
         REPORTE POR CATEGORÍA
    =========================================== -->
    <div class="bloque">
        <h3>Reporte por Categoría</h3>
        <p>Selecciona una categoría para filtrar los artículos.</p>

        <form method="GET">
            <label><strong>Categoría:</strong></label><br>
            <select name="categoria" required>
                <option value="">-- Seleccionar Categoría --</option>
                <?php while ($c = $cats->fetch_assoc()) { ?>
                    <option value="<?= $c['id_categoria'] ?>">
                        <?= htmlspecialchars($c['nombre']) ?>
                    </option>
                <?php } ?>
            </select>

            <br>
            <button class="button" type="submit" name="reporte" value="categoria">Generar Reporte por Categoría</button>
        </form>
    </div>


    <!-- ==========================================
         REPORTE POR FECHAS
    =========================================== -->
    <div class="bloque">
        <h3>Reporte por Fechas</h3>
        <p>Filtra los artículos según la fecha de adquisición.</p>

        <form method="GET">
            <label><strong>Fecha inicio:</strong></label><br>
            <input class="input-date" type="date" name="inicio" required><br>

            <label><strong>Fecha fin:</strong></label><br>
            <input class="input-date" type="date" name="fin" required><br>

            <button class="button" type="submit" name="reporte" value="fechas">
                Generar Reporte por Fechas
            </button>
        </form>
    </div>


    <!-- ==========================================
         RESULTADOS DEL REPORTE
    =========================================== -->
    <?php
    if (isset($_GET["reporte"]) && $_GET["reporte"] !== "general") {

        $tipo = $_GET["reporte"];
        $res = null;

        // ----------------------------- REPORTE POR CATEGORÍA
        if ($tipo == "categoria" && !empty($_GET["categoria"])) {

            $id_cat = intval($_GET["categoria"]);

            $sql = "
                SELECT a.*, c.nombre AS categoria
                FROM articulos a
                INNER JOIN categorias c ON a.id_categoria = c.id_categoria
                WHERE a.id_usuario = ? AND a.id_categoria = ?
                ORDER BY a.fecha_adquisicion DESC
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id_usuario, $id_cat);
            $stmt->execute();
            $res = $stmt->get_result();

            echo "<h3>Resultado: Reporte por Categoría</h3>";
        }

        // ----------------------------- REPORTE POR FECHAS
        elseif ($tipo == "fechas" && !empty($_GET["inicio"]) && !empty($_GET["fin"])) {

            $inicio = $_GET["inicio"];
            $fin = $_GET["fin"];

            $sql = "
                SELECT a.*, c.nombre AS categoria
                FROM articulos a
                INNER JOIN categorias c ON a.id_categoria = c.id_categoria
                WHERE a.id_usuario = ?
                AND a.fecha_adquisicion BETWEEN ? AND ?
                ORDER BY a.fecha_adquisicion DESC
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $id_usuario, $inicio, $fin);
            $stmt->execute();
            $res = $stmt->get_result();

            echo "<h3>Reporte por Fechas ($inicio → $fin)</h3>";
        }

        // ----------------------------- ERRORES
        else {
            echo "<p style='color:red;'>No se pudo generar el reporte. Faltan parámetros.</p>";
            exit();
        }

        // ====================== MOSTRAR TABLA ======================
        if ($res->num_rows > 0) {

            echo "<table>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Valor</th>
                        <th>Fecha adquisición</th>
                    </tr>";

            while ($row = $res->fetch_assoc()) {

                // Obtener miniatura
                $imgQuery = $conn->prepare("SELECT ruta FROM imagenes WHERE id_articulo = ? LIMIT 1");
                $imgQuery->bind_param("i", $row["id_articulo"]);
                $imgQuery->execute();
                $imgRes = $imgQuery->get_result()->fetch_assoc();

                $mini = $imgRes ? "imagenes/" . $imgRes["ruta"] : "sin_imagen.png";

                echo "<tr>
                    <td><img class='img-mini' src='$mini'></td>
                    <td>".htmlspecialchars($row["nombre"])."</td>
                    <td>".htmlspecialchars($row["categoria"])."</td>
                    <td>$".number_format($row["valor_estimado"], 2)."</td>
                    <td>".$row["fecha_adquisicion"]."</td>
                </tr>";
            }

            echo "</table>";

        } else {
            echo "<p><strong>No se encontraron resultados.</strong></p>";
        }
    }
    ?>

    <a class="volver" href="miInventario.php">⮜ Regresar</a>

</div>
</body>
</html>
