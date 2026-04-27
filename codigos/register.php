<?php
include 'header.php';
require_once 'config.php'; // Archivo donde estará la conexión a MySQL

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);
    $confirmar = trim($_POST['confirmar']);

    // Validaciones básicas
    if (empty($nombre) || empty($correo) || empty($contrasena) || empty($confirmar)) {
        $mensaje = "<div class='alert error'>Todos los campos son obligatorios.</div>";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "<div class='alert error'>El correo no es válido.</div>";
    } elseif ($contrasena !== $confirmar) {
        $mensaje = "<div class='alert error'>Las contraseñas no coinciden.</div>";
    } else {
        // Verificar si ya existe el correo
        $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = "<div class='alert error'>El correo ya está registrado.</div>";
        } else {
            // Insertar nuevo usuario
            $hash = password_hash($contrasena, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena, tipo, fecha_registro) VALUES (?, ?, ?, 1, NOW())");
            $stmt->bind_param("sss", $nombre, $correo, $hash);

            if ($stmt->execute()) {
                $mensaje = "<div class='alert success'>¡Registro exitoso! Ahora puedes iniciar sesión.</div>";
            } else {
                $mensaje = "<div class='alert error'>Error al registrar usuario.</div>";
            }
        }
    }
}
?>

<section class="section">
  <div class="container form-container">
    <h2>Crear cuenta</h2>
    <p>Regístrate y se parte de este nuevo proyecto.</p>

    <?= $mensaje ?>

    <form action="" method="POST" class="form-card">
      
      <label for="nombre">Nombre completo</label>
      <input type="text" id="nombre" name="nombre" required>

      <label for="correo">Correo electrónico</label>
      <input type="email" id="correo" name="correo" required>

      <label for="contrasena">Contraseña</label>
      <input type="password" id="contrasena" name="contrasena" required>

      <label for="confirmar">Confirmar contraseña</label>
      <input type="password" id="confirmar" name="confirmar" required>

      <button type="submit" class="btn btn-primary full">Registrarse</button>
    </form>
  </div>
</section>

<?php
include 'footer.php';
?>
