<?php
session_start();

// Obtener datos del formulario
$correo = $_POST['correo'] ?? '';
$password = $_POST['password'] ?? '';

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "inventario");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Buscar usuario por correo
$stmt = $conn->prepare("SELECT id_usuario, nombre, correo, contrasena, tipo 
                        FROM usuarios 
                        WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $row = $result->fetch_assoc();

    // Verificar contraseña hasheada
    if (password_verify($password, $row['contrasena'])) {

        // Crear sesión
        $_SESSION["usuario_id"] = $row['id_usuario'];
        $_SESSION["usuario_nombre"] = $row['nombre'];
        $_SESSION["tipo"] = $row['tipo'];

        // Redirigir según tipo
        if ($row['tipo'] == 2) {
            header("Location: indexAdmin.php");  // Administrador
        } else {
            header("Location: indexCliente.php"); // Usuario normal
        }

        exit();
        
    } else {
        // Contraseña incorrecta
        header("Location: errorPassword.php");
        exit();
    }

} else {
    // Usuario no encontrado
    header("Location: errorUsuario.php");
    exit();
}
?>
