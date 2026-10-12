<?php
declare(strict_types=1);
require_once __DIR__ . '/../../helpers/datos.php';

if (!usuarioLogueado() || !esAdmin()) {
    header('Location: index.php?pagina=login');
    exit;
}

$usuario = obtenerUsuario();
$metricas = obtenerMetricasAdmin();
$promociones = obtenerTodasPromociones();
$promosPendientes = array_filter($promociones, fn($p) => $p['estadoPromocion'] === 'pendiente');
$aerolineas = obtenerAerolineas();
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Panel de Administración</li>
    </ol>
</nav>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <span class="badge bg-danger text-uppercase px-3 py-1 mb-1">
            <i class="bi bi-shield-lock-fill me-1"></i>Portal de Administración General
        </span>
        <h1 class="h3 fw-bold mb-0">Tablero de Control del Sistema</h1>
        <p class="text-muted small mb-0">Gestión global de aerolíneas, promociones, novedades, usuarios y reportes financieros.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?pagina=admin_promociones" class="btn btn-warning btn-sm text-dark position-relative">
            <i class="bi bi-check2-circle me-1"></i>Revisar Promociones
            <?php if ($metricas['promosPendientes'] > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= $metricas['promosPendientes'] ?>
                </span>
            <?php endif; ?>
        </a>
        <a href="index.php?pagina=admin_aerolineas" class="btn btn-primary btn-sm">
            <i class="bi bi-buildings me-1"></i>Gestionar Aerolíneas
        </a>
    </div>
</div>

<!-- TARJETAS KPIS ADMINISTRADOR -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="panel-kpi-card">
            <div class="panel-kpi-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-buildings"></i>
            </div>
            <div>
                <div class="fs-4 fw-bold text-dark"><?= $metricas['totalAerolineas'] ?></div>
                <div class="small text-muted">Aerolíneas Socias</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="panel-kpi-card">
            <div class="panel-kpi-icon bg-info bg-opacity-10 text-info">
                <i class="bi bi-airplane"></i>
            </div>
            <div>
                <div class="fs-4 fw-bold text-dark"><?= $metricas['totalVuelos'] ?></div>
                <div class="small text-muted">Vuelos en el Sistema</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="panel-kpi-card">
            <div class="panel-kpi-icon bg-warning bg-opacity-10 text-warning">
                <i class="bi bi-tags"></i>
            </div>
            <div>
                <div class="fs-4 fw-bold text-dark"><?= $metricas['promosPendientes'] ?></div>
                <div class="small text-muted">Promos por Aprobar</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="panel-kpi-card">
            <div class="panel-kpi-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div>
                <div class="fs-4 fw-bold text-dark">$<?= number_format((float)$metricas['totalRecaudado'], 0, ',', '.') ?></div>
                <div class="small text-muted">Ventas Globales</div>
            </div>
        </div>
    </div>
</div>

<!-- ACCIONES RÁPIDAS Y BANDEJA DE APROBACIÓN -->
<div class="row g-4">
    <!-- Promociones pendientes de aprobación -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold mb-0">
                    <i class="bi bi-hourglass-split text-warning me-2"></i>Promociones Pendientes de Aprobación
                </h2>
                <a href="index.php?pagina=admin_promociones" class="btn btn-outline-primary btn-sm">Ver todas</a>
            </div>

            <?php if (!empty($promosPendientes)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Aerolínea</th>
                                <th>Descripción</th>
                                <th>Descuento</th>
                                <th class="text-end">Decisión</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($promosPendientes as $p): ?>
                                <tr>
                                    <td>#<?= $p['codPromocion'] ?></td>
                                    <td><strong><?= htmlspecialchars($p['nombreAerolinea']) ?></strong></td>
                                    <td><?= htmlspecialchars($p['descripcionPromocion']) ?></td>
                                    <td><span class="badge bg-danger fs-6">-<?= (int)$p['descuentoPromocion'] ?>%</span></td>
                                    <td class="text-end">
                                        <form method="post" action="index.php" class="d-inline">
                                            <input type="hidden" name="accion" value="aprobar_promocion">
            <?= campoCsrf() ?>
                                            <input type="hidden" name="promocion_id" value="<?= $p['codPromocion'] ?>">
                                            <input type="hidden" name="estado" value="aprobada">
                                            <button type="submit" class="btn btn-sm btn-success" title="Aprobar"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                        <form method="post" action="index.php" class="d-inline">
                                            <input type="hidden" name="accion" value="aprobar_promocion">
            <?= campoCsrf() ?>
                                            <input type="hidden" name="promocion_id" value="<?= $p['codPromocion'] ?>">
                                            <input type="hidden" name="estado" value="denegada">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Denegar"><i class="bi bi-x-lg"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-3 bg-light rounded text-center text-muted">
                    <i class="bi bi-check-all fs-2 text-success"></i>
                    <p class="mb-0">No hay promociones pendientes de revisión en este momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Módulos de Administración -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
            <h2 class="h6 fw-bold mb-3 text-uppercase text-muted">Módulos Administrativos</h2>
            <div class="d-grid gap-2">
                <a href="index.php?pagina=admin_aerolineas" class="btn btn-outline-primary text-start d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-buildings me-2"></i>ABMC Aerolíneas</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="index.php?pagina=admin_promociones" class="btn btn-outline-warning text-dark text-start d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-tags me-2"></i>Bandeja Promociones</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="index.php?pagina=admin_novedades" class="btn btn-outline-info text-dark text-start d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-megaphone me-2"></i>ABMC Novedades</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="index.php?pagina=admin_usuarios" class="btn btn-outline-secondary text-start d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-people me-2"></i>Gestión de Usuarios</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="index.php?pagina=admin_reportes" class="btn btn-outline-success text-start d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-bar-chart-line me-2"></i>Reportes Globales</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
