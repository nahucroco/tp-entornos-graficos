<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/datos.php';

if (!usuarioLogueado()) {
    header('Location: index.php?pagina=login&volver=reservas');
    exit;
}

$usuario = obtenerUsuario();
$reservasPendientes = obtenerReservasPorUsuario((int)$usuario['id'], 'pendiente de pago');
$novedades = obtenerNovedadesVigentes();
?>

<!-- BREADCRUMBS -->
<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=reservas">Mis reservas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Reservas pendientes</li>
    </ol>
</nav>

<div class="row g-4">
    <!-- COLUMNA PRINCIPAL: LISTADO DE RESERVAS PENDIENTES -->
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">
                <i class="bi bi-clock-history text-warning me-2"></i>Mis Reservas Pendientes de Pago
            </h1>
            <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                <?= count($reservasPendientes) ?> pendiente(s)
            </span>
        </div>

        <?php if (!empty($reservasPendientes)): ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($reservasPendientes as $res): ?>
                    <?php
                        $fechaVuelo = date('d/m/Y', strtotime($res['fechaSalidaVuelo']));
                        $horaVuelo = date('H:i', strtotime($res['horaSalidaVuelo']));
                        // Calcular si faltan menos de 72hs
                        $tsVuelo = strtotime($res['fechaSalidaVuelo'] . ' ' . $res['horaSalidaVuelo']);
                        $horasParaVuelo = ($tsVuelo - time()) / 3600;
                        $puedeCancelar = $horasParaVuelo >= 72;
                    ?>
                    <article class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-3 pb-2 border-bottom">
                            <div>
                                <span class="badge bg-primary me-2"><?= htmlspecialchars($res['nombreAerolinea']) ?></span>
                                <span class="badge bg-warning text-dark">Pendiente de pago</span>
                            </div>
                            <span class="fw-bold text-muted small">Reserva #<?= $res['codReserva'] ?></span>
                        </div>

                        <div class="row align-items-center mb-3">
                            <div class="col-md-7">
                                <h2 class="h5 fw-bold mb-1">
                                    <?= htmlspecialchars($res['origenVuelo']) ?> <i class="bi bi-arrow-right text-primary"></i> <?= htmlspecialchars($res['destinoVuelo']) ?>
                                </h2>
                                <div class="small text-muted mb-2">
                                    <i class="bi bi-calendar3 me-1"></i><?= $fechaVuelo ?> a las <?= $horaVuelo ?> hs
                                </div>
                                <div class="small text-secondary">
                                    Titular: <strong><?= htmlspecialchars($usuario['nombre']) ?></strong>
                                </div>
                            </div>
                            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                <div class="small text-muted">Total a abonar</div>
                                <div class="fs-4 fw-bold text-primary mb-1">
                                    $<?= number_format((float)$res['precioFinal'], 2, ',', '.') ?>
                                </div>
                                <?php if (!empty($res['descripcionPromocion'])): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success small">
                                        <?= htmlspecialchars($res['descripcionPromocion']) ?> (<?= (int)$res['descuentoPromocion'] ?>% desc)
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Acciones según diagrama de páginas -->
                        <div class="d-flex flex-wrap gap-2 pt-3 border-top justify-content-between align-items-center">
                            <a href="index.php?pagina=reserva_detalle&id=<?= $res['codReserva'] ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-eye me-1"></i>Ver detalle completo
                            </a>

                            <div class="d-flex gap-2">
                                <!-- Botón Cancelar (Valida 72hs) -->
                                <form method="post" action="index.php" onsubmit="return confirm('¿Estás seguro de cancelar la reserva #<?= $res['codReserva'] ?>?');">
                                    <input type="hidden" name="accion" value="cancelar_reserva">
            <?= campoCsrf() ?>
                                    <input type="hidden" name="reserva_id" value="<?= $res['codReserva'] ?>">
                                    <?php if ($puedeCancelar): ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Cancelación disponible hasta 72hs antes">
                                            <i class="bi bi-x-circle me-1"></i>Cancelar Reserva
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-secondary btn-sm disabled" title="Faltan menos de 72hs para el vuelo">
                                            <i class="bi bi-x-circle me-1"></i>No cancelable (< 72hs)
                                        </button>
                                    <?php endif; ?>
                                </form>

                                <!-- Botón Confirmar Pago -->
                                <form method="post" action="index.php">
                                    <input type="hidden" name="accion" value="pagar_reserva">
            <?= campoCsrf() ?>
                                    <input type="hidden" name="reserva_id" value="<?= $res['codReserva'] ?>">
                                    <button type="submit" class="btn btn-despegar btn-sm">
                                        <i class="bi bi-credit-card-2-front me-1"></i>Confirmar Pago
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card p-5 text-center bg-white border-0 shadow-sm rounded-4">
                <i class="bi bi-ticket-perforated display-3 text-muted mb-3"></i>
                <h2 class="h5 fw-bold">No tenés reservas pendientes de pago</h2>
                <p class="text-muted mb-4">Podés buscar vuelos disponibles o consultar tus viajes confirmados en el historial.</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="index.php?pagina=vuelos" class="btn btn-despegar">Buscar nuevos vuelos</a>
                    <a href="index.php?pagina=historial" class="btn btn-outline-primary">Ver historial de compras</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- COLUMNA LATERAL (SEGUN WIREFRAME): NOVEDADES Y CUENTA -->
    <aside class="col-lg-4">
        <!-- Mi cuenta / Resumen de Pasajero -->
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white mb-3">
            <h2 class="h6 fw-bold mb-2"><i class="bi bi-person-check text-primary me-2"></i>Mi Cuenta Cóndor</h2>
            <div class="small text-muted mb-1">Pasajero: <strong><?= htmlspecialchars($usuario['nombre']) ?></strong></div>
            <div class="small text-muted mb-2">Email: <?= htmlspecialchars($usuario['email']) ?></div>
            <div class="p-2 bg-light rounded text-center small">
                <span class="text-success fw-bold"><i class="bi bi-star-fill text-warning me-1"></i>Nivel Plata</span> · 1.250 Puntos Acumulados
            </div>
        </div>

        <!-- Novedades Relacionadas -->
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white">
            <h2 class="h6 fw-bold mb-3"><i class="bi bi-bell-fill text-warning me-2"></i>Avisos Importantes</h2>
            <div class="d-flex flex-column gap-2 small">
                <?php foreach (array_slice($novedades, 0, 2) as $nov): ?>
                    <div class="p-2 bg-light rounded">
                        <span class="text-muted" style="font-size: 0.75rem;"><?= date('d/m/Y', strtotime($nov['fechaPublicacionNovedad'])) ?></span>
                        <p class="mb-0 text-dark"><?= htmlspecialchars($nov['textoNovedad']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>
</div>
