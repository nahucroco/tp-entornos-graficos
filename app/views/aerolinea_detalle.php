<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/datos.php';

$aeroId = (int)($_GET['id'] ?? 0);
$aerolinea = obtenerAerolineaPorId($aeroId);

if (!$aerolinea) {
    echo '<div class="alert alert-danger">La aerolínea seleccionada no existe.</div>';
    echo '<a href="index.php?pagina=aerolineas" class="btn btn-primary">Volver al catálogo</a>';
    return;
}

$vuelos = obtenerVuelos(aerolineaId: $aeroId);
$promociones = obtenerPromocionesPorAerolinea($aeroId);
?>

<!-- BREADCRUMBS -->
<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=aerolineas">Aerolíneas</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($aerolinea['nombreAerolinea']) ?></li>
    </ol>
</nav>

<div class="row g-4">
    <!-- COLUMNA PRINCIPAL -->
    <div class="col-lg-8">
        <!-- INFO GENERAL Y FLOTA -->
        <article class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
            <div class="d-flex flex-wrap align-items-center gap-3 mb-3 pb-3 border-bottom">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <span class="fs-3 fw-bold"><?= htmlspecialchars($aerolinea['codigoIATA']) ?></span>
                </div>
                <div>
                    <h1 class="h4 fw-bold mb-1"><?= htmlspecialchars($aerolinea['nombreAerolinea']) ?></h1>
                    <span class="badge bg-secondary me-1">Código IATA: <?= htmlspecialchars($aerolinea['codigoIATA']) ?></span>
                    <span class="badge bg-light text-dark border">País de Origen: <?= htmlspecialchars($aerolinea['codPais']) ?></span>
                </div>
            </div>

            <p class="text-secondary mb-3">
                <?= htmlspecialchars($aerolinea['descripcionAerolinea']) ?>
            </p>

            <div class="row g-2 small text-muted bg-light p-3 rounded-3">
                <div class="col-sm-6">
                    <strong>Administración Ejecutiva:</strong> <?= htmlspecialchars($aerolinea['nombreCeo'] ?? 'No asignado') ?>
                </div>
                <div class="col-sm-6">
                    <strong>Canal de Contacto:</strong> <?= htmlspecialchars($aerolinea['emailCeo'] ?? 'soporte@condoraero.com') ?>
                </div>
                <div class="col-sm-6">
                    <strong>Flota Comercial:</strong> Boeing 737-800 / Airbus A320
                </div>
                <div class="col-sm-6">
                    <strong>Hub Principal:</strong> Aeroparque (AEP) / Ezeiza (EZE)
                </div>
            </div>
        </article>

        <!-- VUELOS DESTACADOS Y RUTAS POPULARES -->
        <section class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4" aria-labelledby="titulo-vuelos-aero">
            <h2 id="titulo-vuelos-aero" class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-airplane-engines-fill text-primary"></i>Vuelos Programados de <?= htmlspecialchars($aerolinea['nombreAerolinea']) ?>
            </h2>

            <?php if (!empty($vuelos)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Vuelo</th>
                                <th>Ruta (Origen → Destino)</th>
                                <th>Fecha y Hora</th>
                                <th>Tarifa</th>
                                <th>Asientos</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vuelos as $v): ?>
                                <tr>
                                    <td><strong>#<?= $v['codVuelo'] ?></strong></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($v['origenVuelo']) ?> → <?= htmlspecialchars($v['destinoVuelo']) ?></div>
                                    </td>
                                    <td class="small">
                                        <?= date('d/m/Y', strtotime($v['fechaSalidaVuelo'])) ?> · <?= date('H:i', strtotime($v['horaSalidaVuelo'])) ?> hs
                                    </td>
                                    <td class="fw-bold text-primary">$<?= number_format((float)$v['precioVuelo'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= (int)$v['asientosDisponibles'] ?> disp.</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="index.php?pagina=reservar&vuelo=<?= $v['codVuelo'] ?>" class="btn btn-sm btn-despegar">
                                            Reservar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted small mb-0 py-2">No hay vuelos registrados para esta aerolínea actualmente.</p>
            <?php endif; ?>
        </section>

        <!-- PROMOCIONES VIGENTES DE LA AEROLÍNEA -->
        <section class="card shadow-sm border-0 rounded-4 p-4 bg-white" aria-labelledby="titulo-promos-aero">
            <h2 id="titulo-promos-aero" class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-tag-fill text-danger"></i>Promociones Activas de <?= htmlspecialchars($aerolinea['nombreAerolinea']) ?>
            </h2>

            <?php if (!empty($promociones)): ?>
                <div class="row g-3">
                    <?php foreach ($promociones as $p): ?>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <span class="badge bg-danger mb-2"><?= (int)$p['descuentoPromocion'] ?>% DESCUENTO</span>
                                <h3 class="h6 fw-bold mb-1"><?= htmlspecialchars($p['descripcionPromocion']) ?></h3>
                                <p class="small text-muted mb-0">Válida hasta el <?= date('d/m/Y', strtotime($p['fechaFin'] ?? '+30 days')) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted small mb-0">No hay promociones vigentes autorizadas para esta aerolínea en este momento.</p>
            <?php endif; ?>
        </section>
    </div>

    <!-- COLUMNA LATERAL -->
    <aside class="col-lg-4">
        <div class="card p-4 rounded-4 shadow-sm border-0 bg-white mb-3">
            <h2 class="h6 fw-bold mb-3">Atención al Cliente</h2>
            <p class="small text-muted mb-3">
                ¿Tenés consultas sobre equipaje especial, mascotas o reprogramaciones con <?= htmlspecialchars($aerolinea['nombreAerolinea']) ?>?
            </p>
            <a href="index.php?pagina=contacto" class="btn btn-outline-primary w-100 btn-sm">
                <i class="bi bi-chat-dots me-1"></i>Contactar con Soporte
            </a>
        </div>
    </aside>
</div>
