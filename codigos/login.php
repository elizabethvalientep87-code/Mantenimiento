<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión - Inventario Personal</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

    <div class="login-container">
        <h2>Inicio de Sesión</h2>

        <form action="validacion.php" method="POST">

            <label for="correo">Correo electrónico:</label>
            <input type="email" id="correo" name="correo" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Ingresar</button>

        </form>

        <p class="link">¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
    </div>

</body>
</html>
