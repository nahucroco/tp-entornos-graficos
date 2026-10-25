<?php
declare(strict_types=1);
require_once __DIR__ . '/../../helpers/datos.php';

if (!usuarioLogueado() || !esAdmin()) {
    header('Location: index.php?pagina=login');
    exit;
}

$metricas = obtenerMetricasAdmin();
$ventasAerolineas = obtenerVentasPorAerolinea();
$topVuelos = obtenerTopVuelosReservados(5);
$distribucionUsuarios = obtenerDistribucionUsuarios();
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=admin_panel">Panel Administrador</a></li>
        <li class="breadcrumb-item active" aria-current="page">Reportes y Estadísticas</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h4 fw-bold mb-1">
            <i class="bi bi-bar-chart-line text-danger me-2"></i>Reportes Globales de la Plataforma
        </h1>
        <p class="text-muted small mb-0">Métricas consolidadas de facturación, volumen de reservas y actividad del sistema.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print();">
            <i class="bi bi-printer me-1"></i>Imprimir Reporte
        </button>
        <a href="index.php?pagina=admin_panel" class="btn btn-primary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Volver al Panel
        </a>
    </div>
</div>

<!-- RESUMEN EJECUTIVO -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small text-muted text-uppercase fw-bold">Recaudación Total</span>
                <span class="badge bg-success-subtle text-success"><i class="bi bi-currency-dollar"></i></span>
            </div>
            <div class="fs-4 fw-bold text-success">$<?= number_format((float)$metricas['totalRecaudado'], 2, ',', '.') ?></div>
            <span class="small text-muted mt-auto pt-1"><?= $metricas['ventasConfirmadas'] ?> pasajes cobrados</span>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small text-muted text-uppercase fw-bold">Vuelos Activos</span>
                <span class="badge bg-primary-subtle text-primary"><i class="bi bi-airplane"></i></span>
            </div>
            <div class="fs-4 fw-bold text-primary"><?= $metricas['totalVuelos'] ?></div>
            <span class="small text-muted mt-auto pt-1">Rutas en cartelera</span>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small text-muted text-uppercase fw-bold">Aerolíneas</span>
                <span class="badge bg-warning-subtle text-warning"><i class="bi bi-buildings"></i></span>
            </div>
            <div class="fs-4 fw-bold text-dark"><?= $metricas['totalAerolineas'] ?></div>
            <span class="small text-muted mt-auto pt-1">Compañías registradas</span>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small text-muted text-uppercase fw-bold">Usuarios Totales</span>
                <span class="badge bg-info-subtle text-info"><i class="bi bi-people"></i></span>
            </div>
            <div class="fs-4 fw-bold text-info"><?= $metricas['totalUsuarios'] ?></div>
            <span class="small text-muted mt-auto pt-1">Cuentas activas en base</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- FACTURACIÓN POR AEROLÍNEA -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h2 class="h6 fw-bold mb-0">
                    <i class="bi bi-pie-chart text-primary me-2"></i>Ventas y Facturación por Aerolínea
                </h2>
                <span class="badge bg-light text-muted border"><?= count($ventasAerolineas) ?> aerolíneas</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <th scope="col"ead class="table-light">
                            <tr>
                                <th scope="col" class="ps-3">Aerolínea</th>
                                <th scope="col" class="text-center">Reservas</th>
                                <th scope="col" class="text-center">Confirmadas</th>
                                <th scope="col" class="text-end pe-3">Facturación Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ventasAerolineas)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No hay datos de ventas registrados.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ventasAerolineas as $va): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold"><?= htmlspecialchars($va['nombreAerolinea']) ?></div>
                                            <span class="badge bg-light text-secondary border"><?= htmlspecialchars($va['codigoIATA'] ?? 'N/A') ?></span>
                                        </td>
                                        <td class="text-center fw-semibold"><?= (int)$va['totalReservas'] ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success px-2 py-1">
                                                <?= (int)$va['confirmadas'] ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-3 fw-bold text-success">
                                            $<?= number_format((float)$va['totalRecaudado'], 2, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- DISTRIBUCIÓN DE USUARIOS -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h2 class="h6 fw-bold mb-0">
                    <i class="bi bi-people text-info me-2"></i>Distribución de Cuentas por Rol
                </h2>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php 
                    $iconos = [
                        'administrador' => ['bi bi-shield-lock text-danger', 'Administradores Generales'],
                        'ceo'           => ['bi bi-briefcase text-primary', 'CEOs de Aerolínea'],
                        'pasajero'      => ['bi bi-person text-success', 'Pasajeros Registrados']
                    ];
                    foreach ($distribucionUsuarios as $du): 
                        $rol = $du['tipoUsuario'];
                        $info = $iconos[$rol] ?? ['bi bi-person', ucfirst($rol)];
                    ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <div>
                                <i class="<?= $info[0] ?> me-2"></i>
                                <strong><?= $info[1] ?></strong>
                            </div>
                            <span class="badge bg-dark rounded-pill"><?= $du['cantidad'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 bg-light p-3">
            <h3 class="h6 fw-bold mb-2"><i class="bi bi-shield-check text-success me-2"></i>Estado de Auditoría</h3>
            <p class="small text-muted mb-2">
                Todos los datos financieros corresponden a reservas con comprobante de pago emitido bajo estado <code>confirmada</code>. Las reservas canceladas liberan cupo de asientos automáticamente conforme a la regla de negocio de 72 hs previas al despegue.
            </p>
            <div class="small fw-semibold text-secondary">
                <i class="bi bi-clock me-1"></i>Generado el <?= date('d/m/Y H:i') ?>
            </div>
        </div>
    </div>
</div>

<!-- TOP 5 VUELOS CON MAYOR DEMANDA -->
<div class="card border-0 shadow-sm rounded-4 mt-4">
    <div class="card-header bg-white py-3 border-0">
        <h2 class="h6 fw-bold mb-0">
            <i class="bi bi-star-fill text-warning me-2"></i>Top Vuelos con Mayor Demanda de Pasajes
        </h2>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <th scope="col"ead class="table-light">
                    <tr>
                        <th scope="col" class="ps-3">Vuelo</th>
                        <th scope="col">Ruta</th>
                        <th scope="col">Aerolínea</th>
                        <th scope="col">Fecha Salida</th>
                        <th scope="col" class="text-center">Reservas Totales</th>
                        <th scope="col" class="text-center">Confirmadas</th>
                        <th scope="col" class="text-end pe-3">Monto Facturado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($topVuelos)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay reservas registradas en ningún vuelo.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($topVuelos as $tv): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-primary">
                                    <?= htmlspecialchars($tv['codigoIATA'] ?? 'VU') ?>-<?= (int)$tv['codVuelo'] ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($tv['origenVuelo']) ?></strong>
                                    <i class="bi bi-arrow-right text-muted mx-1"></i>
                                    <strong><?= htmlspecialchars($tv['destinoVuelo']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($tv['nombreAerolinea']) ?></td>
                                <td><?= date('d/m/Y', strtotime($tv['fechaSalidaVuelo'])) ?></td>
                                <td class="text-center fw-bold"><?= (int)$tv['totalReservas'] ?></td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success px-2 py-1">
                                        <?= (int)$tv['confirmadas'] ?>
                                    </span>
                                </td>
                                <td class="text-end pe-3 fw-bold text-success">
                                    $<?= number_format((float)$tv['totalRecaudado'], 2, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
