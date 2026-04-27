<?php
// header.php
// Archivo que se incluye en todas las páginas públicas (internauta / visitante).
// Coloca esto al inicio de cada página: <?php include 'header.php'; ?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sistema de Mantenimiento</title>
  <link rel="stylesheet" href="style/style.css">
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <a href="index.php" class="brand">
        <div class="brand-mark">SM</div>
        <div class="brand-name">
          <span class="brand-title">Sistema de Mantenimiento</span>
          <small class="brand-sub">Gestión de Inventario FCC</small>
        </div>
      </a>

      <button id="navToggle" class="nav-toggle" aria-label="Abrir menú">
        <span class="hamburger"></span>
      </button>

      <nav id="mainNav" class="main-nav" aria-label="Menú principal">
        <ul>
          <li><a href="index.php">Inicio</a></li>
          <li><a href="#features">Sobre el Sistema</a></li>
          <li><a href="register.php">Registrarse</a></li>
          <li><a href="login.php">Iniciar sesión</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main id="mainContent">
