<?php
declare(strict_types=1);
require_once __DIR__ . '/../../helpers/datos.php';

if (!usuarioLogueado() || !esCeo()) {
    header('Location: index.php?pagina=login');
    exit;
}

$usuario = obtenerUsuario();
$metricas = obtenerMetricasCeo((int)$usuario['id']);
$aerolinea = $metricas['aerolinea'];
$vuelos = obtenerVuelosPorCeo((int)$usuario['id']);
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=ceo_panel">Panel CEO</a></li>
        <li class="breadcrumb-item active" aria-current="page">Reportes de Aerolínea</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">
            <i class="bi bi-graph-up text-primary me-2"></i>Reportes de Desempeño: <?= htmlspecialchars($aerolinea['nombreAerolinea'] ?? 'Mi Aerolínea') ?>
        </h1>
        <p class="text-muted small mb-0">Métricas de ocupación de aeronaves e ingresos por pasajes confirmados.</p>
    </div>
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print();">
        <i class="bi bi-printer me-1"></i>Imprimir Reporte
    </button>
</div>

<!-- TARJETAS DE RESUMEN EJECUTIVO -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white">
            <span class="small text-muted text-uppercase fw-bold">Facturación Confirmada</span>
            <div class="fs-3 fw-bold text-success">$<?= number_format((float)$metricas['recaudacion'], 2, ',', '.') ?></div>
            <span class="small text-muted"><?= $metricas['ventasConfirmadas'] ?> pasajes emitidos</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white">
            <span class="small text-muted text-uppercase fw-bold">Asientos Comercializados</span>
            <div class="fs-3 fw-bold text-primary"><?= $metricas['asientosOcupados'] ?></div>
            <span class="small text-muted">En <?= $metricas['totalVuelos'] ?> vuelos activos</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white">
            <span class="small text-muted text-uppercase fw-bold">Ticket Promedio</span>
            <?php $prom = $metricas['ventasConfirmadas'] > 0 ? $metricas['recaudacion'] / $metricas['ventasConfirmadas'] : 0; ?>
            <div class="fs-3 fw-bold text-dark">$<?= number_format((float)$prom, 2, ',', '.') ?></div>
            <span class="small text-muted">Por pasajero confirmado</span>
        </div>
    </div>
</div>

<!-- REPORTE DE OCUPACIÓN POR VUELO -->
<div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
    <h2 class="h5 fw-bold mb-3"><i class="bi bi-pie-chart-fill text-warning me-2"></i>Índice de Ocupación por Vuelo</h2>

    <?php if (!empty($vuelos)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Vuelo</th>
                        <th>Ruta</th>
                        <th>Fecha</th>
                        <th>Tarifa</th>
                        <th>Asientos Disp.</th>
                        <th>Reservados</th>
                        <th style="min-width: 150px;">Ocupación (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vuelos as $v): ?>
                        <?php 
                            $disp = (int)$v['asientosDisponibles'];
                            $ocup = (int)$v['reservasRealizadas'];
                            $capacidadTotal = max(1, $disp + $ocup);
                            $pct = round(($ocup / $capacidadTotal) * 100);
                        ?>
                        <tr>
                            <td><strong>#<?= $v['codVuelo'] ?></strong></td>
                            <td class="fw-semibold"><?= htmlspecialchars($v['origenVuelo']) ?> → <?= htmlspecialchars($v['destinoVuelo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($v['fechaSalidaVuelo'])) ?></td>
                            <td class="fw-bold text-dark">$<?= number_format((float)$v['precioVuelo'], 0, ',', '.') ?></td>
                            <td><?= $disp ?></td>
                            <td><strong class="text-primary"><?= $ocup ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 10px;">
                                        <div class="progress-bar <?= $pct > 80 ? 'bg-danger' : ($pct > 50 ? 'bg-warning' : 'bg-success') ?>" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="small fw-bold text-muted"><?= $pct ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted small mb-0 py-2">No hay datos de vuelos disponibles para generar reportes.</p>
    <?php endif; ?>
</div>
