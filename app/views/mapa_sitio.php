<?php
declare(strict_types=1);

$rolActual = obtenerRolUsuario();
$usuarioActual = obtenerUsuario();
$etiquetasRol = [
    'usuario' => 'Pasajero',
    'ceo de aerolínea' => 'CEO de aerolínea',
    'administrador' => 'Administrador',
];
$nombreRol = $etiquetasRol[$rolActual] ?? 'Visitante';
$nombreUsuario = htmlspecialchars((string)($usuarioActual['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Mapa del Sitio Web</li>
    </ol>
</nav>

<div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
        <div class="rounded-circle bg-warning bg-opacity-25 text-dark p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="bi bi-diagram-3-fill fs-3 text-primary" aria-hidden="true"></i>
        </div>
        <div>
            <h1 class="h4 fw-bold mb-1">Mapa del Sitio Web</h1>
            <p class="text-muted small mb-0">Accesos disponibles según tu tipo de usuario en Cóndor AeroReserva.</p>
        </div>
    </div>

    <div class="alert alert-light border d-flex align-items-center gap-2 small" role="status">
        <i class="bi bi-person-badge text-primary fs-5" aria-hidden="true"></i>
        <span>
            <?php if (usuarioLogueado()): ?>
                Estás navegando como <strong><?= htmlspecialchars($nombreRol, ENT_QUOTES, 'UTF-8') ?></strong><?= $nombreUsuario !== '' ? ', ' . $nombreUsuario : '' ?>.
            <?php else: ?>
                Estás viendo el mapa público. Iniciá sesión para acceder a las opciones de tu cuenta.
            <?php endif; ?>
        </span>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <section class="card h-100 border rounded-3 p-3 bg-light" aria-labelledby="mapa-publico">
                <h2 id="mapa-publico" class="h6 fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-house-door-fill" aria-hidden="true"></i>Información pública
                </h2>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="index.php" class="text-decoration-none fw-semibold">Inicio / Portada</a></li>
                    <li class="ms-3"><a href="index.php?pagina=vuelos" class="text-muted">Búsqueda rápida de vuelos</a></li>
                    <li class="ms-3"><a href="index.php?pagina=promociones" class="text-muted">Promociones destacadas</a></li>
                    <li class="ms-3"><a href="index.php?pagina=novedades" class="text-muted">Novedades y avisos del aeropuerto</a></li>
                    <li><a href="index.php?pagina=contacto" class="text-decoration-none fw-semibold">Contacto y soporte</a></li>
                    <li><a href="index.php?pagina=terminos" class="text-decoration-none fw-semibold">Términos, condiciones y política de 72 horas</a></li>
                </ul>
            </section>
        </div>

        <div class="col-md-6 col-lg-4">
            <section class="card h-100 border rounded-3 p-3 bg-light" aria-labelledby="mapa-vuelos">
                <h2 id="mapa-vuelos" class="h6 fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-airplane-fill" aria-hidden="true"></i>Vuelos y aerolíneas
                </h2>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="index.php?pagina=vuelos" class="text-decoration-none fw-semibold">Búsqueda de vuelos</a></li>
                    <li class="ms-3 text-muted">Resultados con filtros por aerolínea y precio</li>
                    <li class="ms-3 text-muted">Ficha técnica y detalle del vuelo</li>
                    <li><a href="index.php?pagina=aerolineas" class="text-decoration-none fw-semibold">Catálogo de aerolíneas socias</a></li>
                    <li class="ms-3 text-muted">Información institucional de cada compañía</li>
                    <li><a href="index.php?pagina=promociones" class="text-decoration-none fw-semibold">Catálogo de promociones</a></li>
                </ul>
            </section>
        </div>

        <div class="col-md-6 col-lg-4">
            <section class="card h-100 border rounded-3 p-3 bg-light" aria-labelledby="mapa-cuenta">
                <h2 id="mapa-cuenta" class="h6 fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-person-circle" aria-hidden="true"></i>Mi cuenta
                </h2>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <?php if (usuarioLogueado()): ?>
                        <li><a href="index.php?pagina=perfil" class="text-decoration-none fw-semibold">Mi perfil y datos personales</a></li>
                        <li><a href="index.php?salir=1" class="text-decoration-none fw-semibold">Cerrar sesión</a></li>
                    <?php else: ?>
                        <li><a href="index.php?pagina=registro" class="text-decoration-none fw-semibold">Crear cuenta de pasajero o solicitar perfil CEO</a></li>
                        <li><a href="index.php?pagina=login" class="text-decoration-none fw-semibold">Iniciar sesión</a></li>
                        <li><a href="index.php?pagina=recuperar_clave" class="text-decoration-none fw-semibold">Recuperar contraseña</a></li>
                    <?php endif; ?>
                </ul>
            </section>
        </div>

        <?php if (esPasajero()): ?>
            <div class="col-md-6 col-lg-4">
                <section class="card h-100 border rounded-3 p-3 bg-light" aria-labelledby="mapa-pasajero">
                    <h2 id="mapa-pasajero" class="h6 fw-bold text-success mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-ticket-perforated-fill" aria-hidden="true"></i>Área de pasajero
                    </h2>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="index.php?pagina=reservas" class="text-decoration-none fw-semibold">Mis reservas pendientes de pago</a></li>
                        <li class="ms-3 text-muted">Confirmar compra y cancelar dentro de las 72 horas</li>
                        <li><a href="index.php?pagina=historial" class="text-decoration-none fw-semibold">Historial de compras y reservas</a></li>
                        <li class="ms-3 text-muted">Detalle de reserva y voucher de embarque</li>
                    </ul>
                </section>
            </div>
        <?php endif; ?>

        <?php if (esCeo()): ?>
            <div class="col-md-6 col-lg-4">
                <section class="card h-100 border rounded-3 p-3 bg-light" aria-labelledby="mapa-ceo">
                    <h2 id="mapa-ceo" class="h6 fw-bold text-warning mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-briefcase-fill" aria-hidden="true"></i>Panel CEO de aerolínea
                    </h2>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="index.php?pagina=ceo_panel" class="text-decoration-none fw-semibold">Dashboard ejecutivo</a></li>
                        <li><a href="index.php?pagina=ceo_vuelos" class="text-decoration-none fw-semibold">Gestión de vuelos</a></li>
                        <li class="ms-3 text-muted">ABMC de vuelos, tarifas y disponibilidad propios</li>
                        <li><a href="index.php?pagina=ceo_promociones" class="text-decoration-none fw-semibold">Gestión de promociones</a></li>
                        <li><a href="index.php?pagina=ceo_reportes" class="text-decoration-none fw-semibold">Reportes de ventas y ocupación</a></li>
                    </ul>
                </section>
            </div>
        <?php endif; ?>

        <?php if (esAdmin()): ?>
            <div class="col-md-6 col-lg-4">
                <section class="card h-100 border rounded-3 p-3 bg-light" aria-labelledby="mapa-admin">
                    <h2 id="mapa-admin" class="h6 fw-bold text-info mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-shield-lock-fill" aria-hidden="true"></i>Administración general
                    </h2>
                    <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                        <li><a href="index.php?pagina=admin_panel" class="text-decoration-none fw-semibold">Panel de control central</a></li>
                        <li><a href="index.php?pagina=admin_aerolineas" class="text-decoration-none fw-semibold">Gestión de aerolíneas</a></li>
                        <li class="ms-3 text-muted">ABMC y asignación de CEO</li>
                        <li><a href="index.php?pagina=admin_promociones" class="text-decoration-none fw-semibold">Bandeja de promociones</a></li>
                        <li><a href="index.php?pagina=admin_novedades" class="text-decoration-none fw-semibold">Gestión de novedades</a></li>
                        <li><a href="index.php?pagina=admin_usuarios" class="text-decoration-none fw-semibold">Gestión de usuarios y validación</a></li>
                        <li><a href="index.php?pagina=admin_reportes" class="text-decoration-none fw-semibold">Reportes globales</a></li>
                    </ul>
                </section>
            </div>
        <?php endif; ?>
    </div>
</div>
