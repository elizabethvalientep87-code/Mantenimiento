<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

require "config.php";

$id_usuario = $_SESSION["usuario_id"];

// =====================================
// OBTENER DATOS DEL USUARIO
// =====================================
$stmt = $conn->prepare("SELECT nombre, correo, fecha_registro FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// =====================================
// SI SE ENVIÓ FORMULARIO
// =====================================
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $password_actual = $_POST["password_actual"];
    $password_nueva = $_POST["password_nueva"];
    $password_confirmar = $_POST["password_confirmar"];

    // VALIDACIONES BÁSICAS
    if ($nombre === "" || $correo === "") {
        $mensaje = "<p style='color:red;'>Nombre y correo no pueden estar vacíos.</p>";
    } else {

        // ================================
        // 1. ACTUALIZAR NOMBRE Y CORREO
        // ================================
        $stmtUpd = $conn->prepare("UPDATE usuarios SET nombre = ?, correo = ? WHERE id_usuario = ?");
        $stmtUpd->bind_param("ssi", $nombre, $correo, $id_usuario);
        $stmtUpd->execute();

        // ================================
        // 2. ACTUALIZAR CONTRASEÑA (solo si se llenó)
        // ================================
        if (!empty($password_nueva) || !empty($password_actual) || !empty($password_confirmar)) {

            // Obtener contraseña actual de BD
            $stmtPass = $conn->prepare("SELECT contrasena FROM usuarios WHERE id_usuario = ?");
            $stmtPass->bind_param("i", $id_usuario);
            $stmtPass->execute();
            $passBD = $stmtPass->get_result()->fetch_assoc()["contrasena"];

            // Validar contraseña actual
            if (!password_verify($password_actual, $passBD)) {
                $mensaje = "<p style='color:red;'>La contraseña actual es incorrecta.</p>";
            } elseif ($password_nueva !== $password_confirmar) {
                $mensaje = "<p style='color:red;'>Las contraseñas no coinciden.</p>";
            } elseif (strlen($password_nueva) < 6) {
                $mensaje = "<p style='color:red;'>La nueva contraseña debe tener al menos 6 caracteres.</p>";
            } else {
                // Guardar nueva contraseña
                $nuevaHash = password_hash($password_nueva, PASSWORD_DEFAULT);

                $stmtNew = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id_usuario = ?");
                $stmtNew->bind_param("si", $nuevaHash, $id_usuario);
                $stmtNew->execute();

                $mensaje = "<p style='color:green;'>Contraseña actualizada correctamente.</p>";
            }
        }

        if ($mensaje === "") {
            $mensaje = "<p style='color:green;'>Datos actualizados correctamente.</p>";
        }
    }
}

include "menuCliente.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi perfil</title>
    <link rel="stylesheet" href="style/style.css">
<style>
    body {
        font-family: Arial;
        background: #f2f2f2;
        padding: 20px;
    }

    .contenedor {
        max-width: 700px;
        margin: auto;
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }

    h2 {
        color: #7b002c;
        text-align: center;
    }

    label {
        font-weight: bold;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="date"] {
        width: 100%;
        padding: 10px;
        margin: 8px 0 15px 0;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    button {
        background: #7b002c;
        padding: 12px;
        width: 100%;
        border: none;
        color: white;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
    }

    button:hover {
        background: #52001e;
    }

    a {
        display: inline-block;
        margin-top: 15px;
        color: #7b002c;
        text-decoration: none;
    }
</style>
</head>
<body>

<div class="contenedor">
    <h2>Mi Perfil</h2>

    <?= $mensaje ?>

    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($usuario["nombre"]) ?>">

        <label>Correo:</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($usuario["correo"]) ?>">

        <label>Fecha de Registro:</label>
        <input type="text" value="<?= $usuario["fecha_registro"] ?>" readonly>

        <hr>
        <h3>Cambiar Contraseña</h3>

        <label>Contraseña actual:</label>
        <input type="password" name="password_actual">

        <label>Nueva contraseña:</label>
        <input type="password" name="password_nueva">

        <label>Confirmar nueva contraseña:</label>
        <input type="password" name="password_confirmar">

        <button type="submit">Guardar Cambios</button>
    </form>

    <a href="miInventario.php">Regresar</a>

</div>

</body>
</html>
