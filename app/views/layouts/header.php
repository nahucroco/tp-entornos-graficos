<?php
declare(strict_types=1);
$usuario = obtenerUsuario();
$rol = obtenerRolUsuario();
$paginaActual = $_GET['pagina'] ?? 'inicio';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cóndor AeroReserva | Vuelos y Promociones</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Estilos personalizados inspirados en Despegar -->
    <link href="assets/css/estilos.css" rel="stylesheet">
</head>
<body>
<a class="skip-link" href="#contenido">Saltar al contenido principal</a>

<header role="banner">
    <!-- Barra superior de utilidad -->
    <div class="bg-dark text-white-50 py-1 small border-bottom border-secondary border-opacity-25">
        <div class="container">
            <div>
                <span class="text-light me-2"><i class="bi bi-shield-check text-success"></i> UTN FRRo · Entornos Gráficos 2026</span>
                <span class="d-none d-md-inline text-muted">| Conexión activa: Cloud TiDB / MySQL</span>
            </div>
        </div>
    </div>

    <!-- Navegación principal -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-condor" aria-label="Navegación principal del sitio">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold fs-4 text-white" href="index.php">
                <span class="bg-white text-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                    <i class="bi bi-send-fill fs-5" style="color: var(--color-primario);"></i>
                </span>
                <span>Cóndor<span class="fw-light ms-1 text-info">Aero</span></span>
                <span class="badge brand-badge text-uppercase d-none d-sm-inline">Despegar Style</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú de navegación">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= $paginaActual === 'inicio' ? 'active' : '' ?>" href="index.php">
                            <i class="bi bi-house-door me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $paginaActual === 'vuelos' ? 'active' : '' ?>" href="index.php?pagina=vuelos">
                            <i class="bi bi-airplane me-1"></i>Vuelos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $paginaActual === 'aerolineas' ? 'active' : '' ?>" href="index.php?pagina=aerolineas">
                            <i class="bi bi-buildings me-1"></i>Aerolíneas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $paginaActual === 'promociones' ? 'active' : '' ?>" href="index.php?pagina=promociones">
                            <i class="bi bi-tag me-1"></i>Promociones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $paginaActual === 'novedades' ? 'active' : '' ?>" href="index.php?pagina=novedades">
                            <i class="bi bi-newspaper me-1"></i>Novedades
                        </a>
                    </li>

                    <?php if (esPasajero()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $paginaActual === 'reservas' ? 'active' : '' ?>" href="index.php?pagina=reservas">
                                <i class="bi bi-ticket-perforated me-1"></i>Mis Reservas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $paginaActual === 'historial' ? 'active' : '' ?>" href="index.php?pagina=historial">
                                <i class="bi bi-clock-history me-1"></i>Historial
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (esCeo()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-warning fw-semibold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-briefcase me-1"></i>Gestión CEO
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?pagina=ceo_panel"><i class="bi bi-speedometer2 me-2"></i>Panel CEO</a></li>
                                <li><a class="dropdown-item" href="index.php?pagina=ceo_vuelos"><i class="bi bi-airplane-engines me-2"></i>Mis Vuelos (ABMC)</a></li>
                                <li><a class="dropdown-item" href="index.php?pagina=ceo_promociones"><i class="bi bi-tags me-2"></i>Mis Promociones (ABMC)</a></li>
                                <li><a class="dropdown-item" href="index.php?pagina=ceo_reportes"><i class="bi bi-bar-chart-line me-2"></i>Reportes de Aerolínea</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (esAdmin()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-info fw-semibold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-gear-wide-connected me-1"></i>Administración
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?pagina=admin_panel"><i class="bi bi-speedometer2 me-2"></i>Panel Admin</a></li>
                                <li><a class="dropdown-item" href="index.php?pagina=admin_aerolineas"><i class="bi bi-buildings me-2"></i>Aerolíneas (ABMC)</a></li>
                                <li><a class="dropdown-item" href="index.php?pagina=admin_promociones"><i class="bi bi-check2-circle me-2"></i>Aprobar Promociones</a></li>
                                <li><a class="dropdown-item" href="index.php?pagina=admin_novedades"><i class="bi bi-megaphone me-2"></i>Novedades (ABMC)</a></li>
                                <li><a class="dropdown-item" href="index.php?pagina=admin_usuarios"><i class="bi bi-people me-2"></i>Gestión de Usuarios</a></li>
                                <li><a class="dropdown-item" href="index.php?pagina=admin_reportes"><i class="bi bi-graph-up-arrow me-2"></i>Reportes Globales</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                    <?php if (usuarioLogueado()): ?>
                        <li class="nav-item dropdown">
                            <a class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-6"></i>
                                <span><?= htmlspecialchars($usuario['nombre']) ?></span>
                                <span class="badge bg-light text-dark text-capitalize ms-1"><?= htmlspecialchars($usuario['rol']) ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li class="dropdown-header">Conectado como <?= htmlspecialchars($usuario['email']) ?></li>
                                <li><a class="dropdown-item" href="index.php?pagina=perfil"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
                                <?php if (esPasajero()): ?>
                                    <li><a class="dropdown-item" href="index.php?pagina=reservas"><i class="bi bi-ticket-detailed me-2"></i>Mis Reservas</a></li>
                                    <li><a class="dropdown-item" href="index.php?pagina=historial"><i class="bi bi-receipt me-2"></i>Historial de Compras</a></li>
                                <?php elseif (esCeo()): ?>
                                    <li><a class="dropdown-item" href="index.php?pagina=ceo_panel"><i class="bi bi-speedometer2 me-2"></i>Panel Ejecutivo</a></li>
                                <?php elseif (esAdmin()): ?>
                                    <li><a class="dropdown-item" href="index.php?pagina=admin_panel"><i class="bi bi-shield-lock me-2"></i>Panel Administrador</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="index.php?salir=1"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $paginaActual === 'registro' ? 'active' : '' ?>" href="index.php?pagina=registro">
                                <i class="bi bi-person-plus me-1"></i>Registrarse
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light btn-sm fw-bold px-3 py-1 text-primary shadow-sm" href="index.php?pagina=login">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Ingresar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main id="contenido" class="py-4" role="main">
    <div class="container">
        <!-- Notificaciones y Alertas Flash -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <?php 
                $tipoAlert = $_SESSION['tipo_mensaje'] ?? 'success';
                $icono = match($tipoAlert) {
                    'danger' => 'bi-exclamation-octagon-fill',
                    'warning' => 'bi-exclamation-triangle-fill',
                    'info' => 'bi-info-circle-fill',
                    default => 'bi-check-circle-fill',
                };
            ?>
            <div class="alert alert-<?= htmlspecialchars($tipoAlert) ?> alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                <i class="bi <?= $icono ?> fs-5"></i>
                <div><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar notificación"></button>
            </div>
            <?php 
                unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); 
            ?>
        <?php endif; ?>
