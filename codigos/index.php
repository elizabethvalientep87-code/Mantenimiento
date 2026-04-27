<?php
// index.php - Página principal para internautas (visitantes)
include 'header.php';
?>
<section class="hero">
  <div class="container hero-inner">
    <div class="hero-text">
      <h1>Sistema de Mantenimiento</h1>
      <h1>Apoyo a laboratorios FCC</h1>
      <p class="lead">Accesibilidad, reportes, soluciones y apoyos para los estudiantes de la FCC</p>
      <div class="hero-cta">
        <a href="register.php" class="btn btn-primary">Regístrate</a>
        <a href="login.php" class="btn btn-outline">Iniciar sesión</a>
      </div>
    </div>

    <div class="hero-illustration" aria-hidden="true">
      <div class="card-sample">
        <h3>Encuentra soluciones</h3>
        <img src="img/compu.jpg" alt="fcc">
      </div>
    </div>
  </div>
</section>

<section id="features" class="section">
  <div class="container">
    <h2>Características principales del sistema</h2>
    <div class="grid-3">
      <article class="feature-card">
        <h3>Reporte Inteligente de Fallas</h3>
        <p>Permite a alumnos y maestros notificar incidencias en tiempo real seleccionando el equipo específico. 
          El sistema genera una notificación automática inmediata a los administradores para reducir los tiempos de respuesta.</p>
      </article>
      <article class="feature-card">
        <h3>Gestión Jerárquica de Soluciones</h3>
        <p>Optimiza el mantenimiento mediante un flujo de trabajo por nivelses. Los administradores diagnostican y resuelven problemas comunes,
           mientras que las fallas técnicas complejas son derivadas automáticamente al encargado del laboratorio.</p>
      </article>
      <article class="feature-card">
        <h3>Control de Inventario y Estado</h3>
        <p>Visualiza el estado operativo de todo el laboratorio en un solo clic. Consulta historiales de mantenimiento, 
          gestiona altas o bajas de equipos y mantén actualizada la disponibilidad real de las máquinas para la comunidad académica.</p>
      </article>
    </div>
  </div>
</section>

<section id="about" class="section section-muted">
  <div class="container about-flex-container">
    <div class="about-text">
      <h2>Sobre el sistema</h2>
      <p>El <strong>Sistema de Gestión de Mantenimiento para Laboratorios de la FCC</strong> es una plataforma integral diseñada para optimizar nuestras salas de cómputo. Su objetivo es facilitar la comunicación entre alumnos, maestros y el equipo técnico.</p>
      <p>Buscamos fomentar una cultura de corresponsabilidad: al reportar una falla a tiempo, aseguras que todos contemos con un espacio de aprendizaje funcional y de alta calidad.</p>
    </div>
    
    <div class="about-features-list">
      <div class="feature-item">
        <strong>Rapidez:</strong>
        <p>Elimina trámites y agiliza la atención técnica inmediata.</p>
      </div>
      <div class="feature-item">
        <strong>Transparencia:</strong>
        <p>Consulta en todo momento el estado real de tus reportes enviados.</p>
      </div>
      <div class="feature-item">
        <strong>Eficiencia:</strong>
        <p>Prioriza las fallas críticas para mantener el laboratorio al 100%.</p>
      </div>
    </div>
  </div>
</section>

<?php
include 'footer.php';
?>
