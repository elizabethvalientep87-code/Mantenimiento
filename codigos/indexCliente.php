<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 1) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Inventario</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

    <nav class="menu">
        <div class="logo">Comunidad FCC</div>
        <ul>
            <li><a href="indexCliente.php">Inicio</a></li>
            <li><a href="miInventario.php">Laboratorio CCO3</a></li>
            <li><a href="generarReporte.php">Generar Reporte</a></li>
            <li><a href="verPerfil.php">Mi Perfil</a><li>
            <li><a href="salir.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
       <section class="section">
         <div class="container text-center">
         <h2>Nuestros Laboratorios</h2>
    
    <div class="img-container-responsive">
      <div class="image-wrapper">
          <img src="img/lab.png" alt="Laboratorio CCO3 FCC" class="img-panel-control">
      </div>
    </div>

    <div class="cards-container">
        </div>
        </div>
    </section>

        <div class="tarjetas">
            <div class="card">
                <h3>Inventario de Equipos</h3>
                <p>Consulta la lista completa de computadoras y periféricos disponibles en el Laboratorio CCO3. 
                    Revisa especificaciones técnicas y la ubicación exacta de cada unidad.</p>
                <a class="btn" href="miInventario.php">Ver Equipos</a>
            </div>

            <div class="card">
                <h3>Reportar Incidencia</h3>
                <p>¿Detectaste una falla en una máquina? Registra el problema aquí detallando el tipo de error
                     (software, hardware o periféricos) para que el equipo técnico pueda atenderlo.</p>
                <a class="btn" href="agregarArticulo.php">Crear Reporte</a>
            </div>

            <div class="card">
                <h3>Seguimiento de Fallas</h3>
                <p>Revisa el estado de los reportes que has enviado. 
                    Podrás ver si tu incidencia ya fue recibida, si está en revisión o si el problema ya ha sido resuelto.</p>
                <a class="btn" href="generarReporte.php">Ver mis Reportes</a>
            </div>

        </div>
    </div>

</body>
</html>
