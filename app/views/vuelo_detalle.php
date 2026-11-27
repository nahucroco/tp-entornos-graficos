<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/datos.php';

$vueloId = (int)($_GET['id'] ?? 0);
$vuelo = obtenerVueloPorId($vueloId);

if (!$vuelo) {
    echo '<div class="alert alert-danger">El vuelo solicitado no existe o no se encuentra disponible.</div>';
    echo '<a href="index.php?pagina=vuelos" class="btn btn-primary">Volver al buscador</a>';
    return;
}

$promociones = obtenerPromocionesPorAerolinea((int)$vuelo['codAerolinea']);
$asientos = (int)$vuelo['asientosDisponibles'];
$vueloDisponible = fechaHoraVueloEsFutura($vuelo['fechaSalidaVuelo'], $vuelo['horaSalidaVuelo']);
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=vuelos">Vuelos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detalle del Vuelo #<?= $vuelo['codVuelo'] ?></li>
    </ol>
</nav>

<div class="row g-4">
    <!-- COLUMNA PRINCIPAL: FICHA DEL VUELO -->
    <div class="col-lg-8">
        <article class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <span class="badge bg-primary px-3 py-1 mb-1"><?= htmlspecialchars($vuelo['nombreAerolinea']) ?> (<?= htmlspecialchars($vuelo['codigoIATA']) ?>)</span>
                    <h1 class="h4 fw-bold mb-0">Itinerario: <?= htmlspecialchars($vuelo['origenVuelo']) ?> a <?= htmlspecialchars($vuelo['destinoVuelo']) ?></h1>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-dark border">Vuelo #<?= $vuelo['codVuelo'] ?></span>
                </div>
            </div>

            <!-- ITINERARIO DETALLADO -->
            <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                <div class="row text-center text-md-start align-items-center">
                    <div class="col-md-5">
                        <small class="text-muted text-uppercase fw-bold">Salida</small>
                        <div class="fs-4 fw-bold text-dark"><?= date('H:i', strtotime($vuelo['horaSalidaVuelo'])) ?> hs</div>
                        <div class="fw-semibold"><?= htmlspecialchars($vuelo['origenVuelo']) ?></div>
                        <small class="text-muted"><?= date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])) ?></small>
                    </div>
                    <div class="col-md-2 text-center my-3 my-md-0">
                        <i class="bi bi-airplane-fill text-primary fs-3"></i>
                        <div class="small text-muted">Directo</div>
                    </div>
                    <div class="col-md-5 text-md-end">
                        <small class="text-muted text-uppercase fw-bold">Llegada Estimada</small>
                        <div class="fs-4 fw-bold text-dark"><?= date('H:i', strtotime($vuelo['horaSalidaVuelo'] . ' +1 hour +20 minutes')) ?> hs</div>
                        <div class="fw-semibold"><?= htmlspecialchars($vuelo['destinoVuelo']) ?></div>
                        <small class="text-muted"><?= date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])) ?></small>
                    </div>
                </div>
            </div>

            <!-- CARACTERÍSTICAS Y POLÍTICAS -->
            <h2 class="h5 fw-bold mb-3">Servicios incluidos y Políticas</h2>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-bag-check-fill text-success fs-5"></i>
                        <div>
                            <strong>Equipaje de mano:</strong>
                            <p class="small text-muted mb-0">1 pieza de hasta 10 kg en cabina incluida sin cargo.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-clock-history text-warning fs-5"></i>
                        <div>
                            <strong>Cancelación Flexible:</strong>
                            <p class="small text-muted mb-0">Cancelación permitida hasta 72 horas antes del vuelo (Regla #13).</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-person-check-fill text-primary fs-5"></i>
                        <div>
                            <strong>Check-in Online:</strong>
                            <p class="small text-muted mb-0">Habilitado desde 48 horas antes de la salida.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-airline-seat-recline-normal text-info fs-5"></i>
                        <div>
                            <strong>Disponibilidad:</strong>
                            <p class="small text-muted mb-0"><?= $asientos ?> asientos disponibles en cabina turista.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACERCA DE LA AEROLÍNEA -->
            <div class="p-3 bg-white border rounded-3">
                <h3 class="h6 fw-bold mb-1">Acerca de <?= htmlspecialchars($vuelo['nombreAerolinea']) ?></h3>
                <p class="small text-muted mb-0"><?= htmlspecialchars($vuelo['descripcionAerolinea']) ?></p>
            </div>
        </article>
    </div>

    <!-- COLUMNA DERECHA: CAJA DE TARIFA Y CTA -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white sticky-top" style="top: 2rem;">
            <span class="text-uppercase small fw-bold text-muted">Tarifa regular</span>
            <div class="display-6 fw-bold text-primary mb-1">
                $<?= number_format((float)$vuelo['precioVuelo'], 0, ',', '.') ?>
            </div>
            <p class="small text-muted mb-3">Precio por adulto en ARS. Incluye impuestos y tasas aéreas.</p>

            <section class="border rounded-3 p-3 mb-3 small" aria-labelledby="titulo-promos-vuelo">
                <h2 id="titulo-promos-vuelo" class="h6 fw-bold mb-2">
                    <i class="bi bi-tag-fill me-1 text-danger" aria-hidden="true"></i>Promociones aplicables
                </h2>
                <?php if (!empty($promociones)): ?>
                    <ul class="list-unstyled mb-2">
                        <?php foreach ($promociones as $promo):
                            $precioPromo = (float)$vuelo['precioVuelo'] * (1 - (float)$promo['descuentoPromocion'] / 100); ?>
                            <li class="mb-2">
                                <span class="badge bg-danger me-1"><?= (int)$promo['descuentoPromocion'] ?>% OFF</span>
                                <strong><?= htmlspecialchars($promo['descripcionPromocion']) ?></strong><br>
                                <span class="text-success fw-bold">$<?= number_format($precioPromo, 0, ',', '.') ?> con esta promoción</span>
                                <?php if (!empty($promo['fechaFin'])): ?>
                                    <span class="text-muted">· vigente hasta el <?= date('d/m/Y', strtotime($promo['fechaFin'])) ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="text-muted mb-0">Promoción de <?= htmlspecialchars($vuelo['nombreAerolinea']) ?>. La elegís al confirmar tu reserva.</p>
                <?php else: ?>
                    <p class="text-muted mb-0"><?= htmlspecialchars($vuelo['nombreAerolinea']) ?> no tiene promociones vigentes en este momento.</p>
                <?php endif; ?>
            </section>

            <?php if (!$vueloDisponible): ?>
                <button class="btn btn-secondary btn-lg w-100 mb-2" disabled>Vuelo no disponible</button>
                <p class="small text-muted text-center">La fecha y hora de salida ya pasaron.</p>
            <?php elseif ($asientos > 0): ?>
                <a href="index.php?pagina=reservar&vuelo=<?= $vuelo['codVuelo'] ?>" class="btn btn-despegar btn-lg w-100 mb-2">
                    Continuar reserva
                </a>
            <?php else: ?>
                <button class="btn btn-secondary btn-lg w-100 mb-2 disabled">Vuelo agotado</button>
            <?php endif; ?>

            <a href="index.php?pagina=vuelos" class="btn btn-outline-secondary w-100 btn-sm">
                Volver a la búsqueda
            </a>
        </div>
    </div>
</div>
