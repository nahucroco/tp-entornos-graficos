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
$promociones = obtenerPromocionesPorCeo((int)$usuario['id']);
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Panel CEO de Aerolínea</li>
    </ol>
</nav>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <span class="badge bg-warning text-dark text-uppercase px-3 py-1 mb-1">
            <i class="bi bi-briefcase-fill me-1"></i>Portal Ejecutivo CEO
        </span>
        <h1 class="h3 fw-bold mb-0">
            <?= htmlspecialchars($aerolinea['nombreAerolinea'] ?? 'Mi Aerolínea') ?> (<?= htmlspecialchars($aerolinea['codigoIATA'] ?? 'IATA') ?>)
        </h1>
        <p class="text-muted small mb-0">Bienvenido/a, <?= htmlspecialchars($usuario['nombre']) ?>. Panel de administración exclusiva de tu aerolínea.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?pagina=ceo_vuelos" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Nuevo Vuelo
        </a>
        <a href="index.php?pagina=ceo_promociones" class="btn btn-warning btn-sm text-dark">
            <i class="bi bi-tag me-1"></i>Nueva Promo
        </a>
    </div>
</div>

<!-- TARJETAS KPIS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="panel-kpi-card">
            <div class="panel-kpi-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-airplane-engines"></i>
            </div>
            <div>
                <div class="fs-4 fw-bold text-dark"><?= $metricas['totalVuelos'] ?></div>
                <div class="small text-muted">Vuelos Programados</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="panel-kpi-card">
            <div class="panel-kpi-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div>
                <div class="fs-4 fw-bold text-dark"><?= $metricas['ventasConfirmadas'] ?></div>
                <div class="small text-muted">Ventas Confirmadas</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="panel-kpi-card">
            <div class="panel-kpi-icon bg-info bg-opacity-10 text-info">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <div class="fs-4 fw-bold text-dark">$<?= number_format((float)$metricas['recaudacion'], 0, ',', '.') ?></div>
                <div class="small text-muted">Ingresos Brutos</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="panel-kpi-card">
            <div class="panel-kpi-icon bg-warning bg-opacity-10 text-warning">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <div class="fs-4 fw-bold text-dark"><?= $metricas['asientosOcupados'] ?></div>
                <div class="small text-muted">Asientos Reservados</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Vuelos recientes de la aerolínea -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold mb-0"><i class="bi bi-airplane text-primary me-2"></i>Vuelos Programados</h2>
                <a href="index.php?pagina=ceo_vuelos" class="btn btn-outline-primary btn-sm">Gestionar todos (ABMC)</a>
            </div>

            <?php if (!empty($vuelos)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Ruta</th>
                                <th>Fecha / Hora</th>
                                <th>Tarifa</th>
                                <th>Asientos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($vuelos, 0, 4) as $v): ?>
                                <tr>
                                    <td><strong>#<?= $v['codVuelo'] ?></strong></td>
                                    <td><?= htmlspecialchars($v['origenVuelo']) ?> → <?= htmlspecialchars($v['destinoVuelo']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($v['fechaSalidaVuelo'])) ?> <?= date('H:i', strtotime($v['horaSalidaVuelo'])) ?></td>
                                    <td class="fw-bold text-primary">$<?= number_format((float)$v['precioVuelo'], 0, ',', '.') ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= $v['asientosDisponibles'] ?> disp.</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted small mb-0 py-2">No registrás vuelos para tu aerolínea todavía.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Promociones y estado de aprobación -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 fw-bold mb-0"><i class="bi bi-tag text-warning me-2"></i>Mis Promociones</h2>
                <a href="index.php?pagina=ceo_promociones" class="small text-decoration-none">Ver todas</a>
            </div>

            <?php if (!empty($promociones)): ?>
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($promociones as $p): ?>
                        <div class="p-2 border rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-danger small"><?= (int)$p['descuentoPromocion'] ?>% OFF</span>
                                <?php if ($p['estadoPromocion'] === 'aprobada'): ?>
                                    <span class="badge bg-success">Aprobada</span>
                                <?php elseif ($p['estadoPromocion'] === 'denegada'): ?>
                                    <span class="badge bg-danger">Denegada</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                <?php endif; ?>
                            </div>
                            <div class="small fw-bold text-dark"><?= htmlspecialchars($p['descripcionPromocion']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted small mb-0 py-2">No creaste promociones para tu aerolínea aún.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
