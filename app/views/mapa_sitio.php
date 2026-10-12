<?php
declare(strict_types=1);
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
            <i class="bi bi-diagram-3-fill fs-3 text-primary"></i>
        </div>
        <div>
            <h1 class="h4 fw-bold mb-1">Mapa del Sitio Web (Árbol Funcional)</h1>
            <p class="text-muted small mb-0">
                Estructura jerárquica de contenidos y navegación según la Arquitectura de la Información de Cóndor AeroReserva.
            </p>
        </div>
    </div>

    <div class="row g-4">
        <!-- SECCIÓN 1: PÚBLICO GENERAL / INICIO -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border rounded-3 p-3 bg-light">
                <h2 class="h6 fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-house-door-fill"></i>1. Portada e Información
                </h2>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="index.php" class="text-decoration-none fw-semibold">Inicio / Portada</a></li>
                    <li class="ms-3"><a href="index.php?pagina=vuelos" class="text-muted">Búsqueda rápida de vuelos</a></li>
                    <li class="ms-3"><a href="index.php?pagina=promociones" class="text-muted">Promociones destacadas</a></li>
                    <li class="ms-3"><a href="index.php?pagina=novedades" class="text-muted">Novedades y avisos del aeropuerto</a></li>
                    <li><a href="index.php?pagina=contacto" class="text-decoration-none fw-semibold">Contacto & Soporte</a></li>
                    <li><a href="index.php?pagina=terminos" class="text-decoration-none fw-semibold">Términos, Condiciones y Política 72hs</a></li>
                </ul>
            </div>
        </div>

        <!-- SECCIÓN 2: VUELOS Y RUTAS -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border rounded-3 p-3 bg-light">
                <h2 class="h6 fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-airplane-fill"></i>2. Vuelos y Aerolíneas
                </h2>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="index.php?pagina=vuelos" class="text-decoration-none fw-semibold">Búsqueda de Vuelos</a></li>
                    <li class="ms-3 text-muted">Resultados con filtros por aerolínea y precio</li>
                    <li class="ms-3 text-muted">Ficha técnica y detalle del vuelo</li>
                    <li><a href="index.php?pagina=aerolineas" class="text-decoration-none fw-semibold">Catálogo de Aerolíneas Socias</a></li>
                    <li class="ms-3 text-muted">Información institucional de cada compañía</li>
                    <li class="ms-3 text-muted">Rutas programadas y flota</li>
                    <li><a href="index.php?pagina=promociones" class="text-decoration-none fw-semibold">Catálogo de Promociones</a></li>
                </ul>
            </div>
        </div>

        <!-- SECCIÓN 3: USUARIO Y CUENTA -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border rounded-3 p-3 bg-light">
                <h2 class="h6 fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-person-circle"></i>3. Cuenta y Autenticación
                </h2>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="index.php?pagina=registro" class="text-decoration-none fw-semibold">Registrarse (Pasajero / Solicitud CEO)</a></li>
                    <li><a href="index.php?pagina=login" class="text-decoration-none fw-semibold">Iniciar Sesión</a></li>
                    <li><a href="index.php?pagina=recuperar_clave" class="text-decoration-none fw-semibold">Recuperar Contraseña</a></li>
                    <li><a href="index.php?pagina=perfil" class="text-decoration-none fw-semibold">Mi Perfil (Modificar datos)</a></li>
                </ul>
            </div>
        </div>

        <!-- SECCIÓN 4: PASAJERO (RESERVAS Y COMPRAS) -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border rounded-3 p-3 bg-light">
                <h2 class="h6 fw-bold text-success mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-ticket-perforated-fill"></i>4. Mis Reservas y Compras
                </h2>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="index.php?pagina=reservas" class="text-decoration-none fw-semibold">Reservas Pendientes de Pago</a></li>
                    <li class="ms-3 text-muted">Confirmar compra con tarjeta / pasarela</li>
                    <li class="ms-3 text-muted">Cancelar reserva (Regla de negocio: hasta 72hs antes)</li>
                    <li><a href="index.php?pagina=historial" class="text-decoration-none fw-semibold">Historial de Compras y Reservas</a></li>
                    <li class="ms-3 text-muted">Detalle de reserva y voucher de embarque</li>
                </ul>
            </div>
        </div>

        <!-- SECCIÓN 5: GESTIÓN CEO DE AEROLÍNEA -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border rounded-3 p-3 bg-light">
                <h2 class="h6 fw-bold text-warning mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase-fill"></i>5. Panel CEO de Aerolínea
                </h2>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="index.php?pagina=ceo_panel" class="text-decoration-none fw-semibold">Dashboard Ejecutivo</a></li>
                    <li><a href="index.php?pagina=ceo_vuelos" class="text-decoration-none fw-semibold">Gestión de Vuelos (ABMC)</a></li>
                    <li class="ms-3 text-muted">Alta, modificación y baja de vuelos propios</li>
                    <li class="ms-3 text-muted">Gestión de tarifas y disponibilidad de asientos</li>
                    <li><a href="index.php?pagina=ceo_promociones" class="text-decoration-none fw-semibold">Gestión de Promociones (ABMC)</a></li>
                    <li class="ms-3 text-muted">Crear ofertas pendientes de aprobación</li>
                    <li><a href="index.php?pagina=ceo_reportes" class="text-decoration-none fw-semibold">Reportes de Ventas y Ocupación</a></li>
                </ul>
            </div>
        </div>

        <!-- SECCIÓN 6: ADMINISTRACIÓN GENERAL -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border rounded-3 p-3 bg-light">
                <h2 class="h6 fw-bold text-info mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock-fill"></i>6. Administración General
                </h2>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="index.php?pagina=admin_panel" class="text-decoration-none fw-semibold">Panel de Control Central</a></li>
                    <li><a href="index.php?pagina=admin_aerolineas" class="text-decoration-none fw-semibold">Gestión de Aerolíneas (ABMC)</a></li>
                    <li class="ms-3 text-muted">Alta, edición, baja y asignación de CEO</li>
                    <li><a href="index.php?pagina=admin_promociones" class="text-decoration-none fw-semibold">Bandeja de Promociones</a></li>
                    <li class="ms-3 text-muted">Aprobar o denegar ofertas de los CEOs</li>
                    <li><a href="index.php?pagina=admin_novedades" class="text-decoration-none fw-semibold">Gestión de Novedades (ABMC)</a></li>
                    <li><a href="index.php?pagina=admin_usuarios" class="text-decoration-none fw-semibold">Gestión de Usuarios y Validación</a></li>
                    <li><a href="index.php?pagina=admin_reportes" class="text-decoration-none fw-semibold">Reportes Globales de Ventas y Vuelos</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
