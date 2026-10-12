<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/datos.php';

if (!usuarioLogueado()) {
    header('Location: index.php?pagina=login&volver=historial');
    exit;
}

$usuario = obtenerUsuario();
$todasReservas = obtenerReservasPorUsuario((int)$usuario['id']);

$comprasConfirmadas = array_filter($todasReservas, fn($r) => $r['estadoReserva'] === 'confirmada');
$pendientes = array_filter($todasReservas, fn($r) => $r['estadoReserva'] === 'pendiente de pago');
$canceladas = array_filter($todasReservas, fn($r) => $r['estadoReserva'] === 'cancelada');
$novedades = obtenerNovedadesVigentes();
?>

<!-- BREADCRUMBS -->
<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=perfil">Mi Perfil</a></li>
        <li class="breadcrumb-item active" aria-current="page">Historial de Compras y Reservas</li>
    </ol>
</nav>

<div class="row g-4">
    <!-- COLUMNA PRINCIPAL -->
    <div class="col-lg-8">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
            <div>
                <h1 class="h4 fw-bold mb-0">Historial de Compras y Reservas</h1>
                <p class="text-muted small mb-0">Registro completo de todos tus viajes, emisiones y reservas.</p>
            </div>
            <!-- Buscador en tiempo real de la tabla -->
            <div style="min-width: 250px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="filtro-tabla-input" placeholder="Filtrar por ciudad o código...">
                </div>
            </div>
        </div>

        <!-- 1. COMPRAS CONFIRMADAS -->
        <section class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4" aria-labelledby="titulo-confirmadas">
            <h2 id="titulo-confirmadas" class="h6 text-uppercase fw-bold text-success border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>Compras Confirmadas (Pasajes Emitidos)
                <span class="badge bg-success ms-auto"><?= count($comprasConfirmadas) ?></span>
            </h2>

            <?php if (!empty($comprasConfirmadas)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Fecha Emisión</th>
                                <th>Trayecto / Vuelo</th>
                                <th>Total Pagado</th>
                                <th>Estado</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comprasConfirmadas as $c): ?>
                                <tr data-fila-busqueda>
                                    <td><strong>#<?= $c['codReserva'] ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($c['fechaReserva'])) ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($c['origenVuelo']) ?> → <?= htmlspecialchars($c['destinoVuelo']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($c['nombreAerolinea']) ?> (Vuelo #<?= $c['codVuelo'] ?>)</small>
                                    </td>
                                    <td class="fw-bold text-primary">$<?= number_format((float)$c['precioFinal'], 2, ',', '.') ?></td>
                                    <td><span class="badge bg-success">Confirmada</span></td>
                                    <td class="text-end">
                                        <a href="index.php?pagina=reserva_detalle&id=<?= $c['codReserva'] ?>" class="btn btn-sm btn-outline-primary" title="Ver detalle e imprimir">
                                            <i class="bi bi-receipt me-1"></i>Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted small mb-0 py-2">No registrás compras confirmadas hasta el momento.</p>
            <?php endif; ?>
        </section>

        <!-- 2. RESERVAS PENDIENTES DE PAGO -->
        <section class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4" aria-labelledby="titulo-pendientes">
            <h2 id="titulo-pendientes" class="h6 text-uppercase fw-bold text-warning border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-clock-history"></i>Reservas Pendientes de Pago
                <span class="badge bg-warning text-dark ms-auto"><?= count($pendientes) ?></span>
            </h2>

            <?php if (!empty($pendientes)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>Trayecto / Vuelo</th>
                                <th>Total a Pagar</th>
                                <th>Estado</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendientes as $p): ?>
                                <tr data-fila-busqueda>
                                    <td><strong>#<?= $p['codReserva'] ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($p['fechaReserva'])) ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($p['origenVuelo']) ?> → <?= htmlspecialchars($p['destinoVuelo']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($p['nombreAerolinea']) ?> (Vuelo #<?= $p['codVuelo'] ?>)</small>
                                    </td>
                                    <td class="fw-bold text-dark">$<?= number_format((float)$p['precioFinal'], 2, ',', '.') ?></td>
                                    <td><span class="badge bg-warning text-dark">Pendiente</span></td>
                                    <td class="text-end">
                                        <form method="post" action="index.php" class="d-inline">
                                            <input type="hidden" name="accion" value="pagar_reserva">
                                            <input type="hidden" name="reserva_id" value="<?= $p['codReserva'] ?>">
                                            <button type="submit" class="btn btn-sm btn-despegar">
                                                Pagar
                                            </button>
                                        </form>
                                        <a href="index.php?pagina=reserva_detalle&id=<?= $p['codReserva'] ?>" class="btn btn-sm btn-outline-secondary ms-1">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted small mb-0 py-2">No tenés reservas pendientes de pago.</p>
            <?php endif; ?>
        </section>

        <!-- 3. RESERVAS CANCELADAS -->
        <section class="card shadow-sm border-0 rounded-4 p-4 bg-white" aria-labelledby="titulo-canceladas">
            <h2 id="titulo-canceladas" class="h6 text-uppercase fw-bold text-muted border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-x-circle"></i>Reservas Canceladas
                <span class="badge bg-secondary ms-auto"><?= count($canceladas) ?></span>
            </h2>

            <?php if (!empty($canceladas)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-muted">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>Trayecto</th>
                                <th>Monto Liberado</th>
                                <th>Estado</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($canceladas as $canc): ?>
                                <tr data-fila-busqueda>
                                    <td>#<?= $canc['codReserva'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($canc['fechaReserva'])) ?></td>
                                    <td><?= htmlspecialchars($canc['origenVuelo']) ?> → <?= htmlspecialchars($canc['destinoVuelo']) ?></td>
                                    <td>$<?= number_format((float)$canc['precioFinal'], 2, ',', '.') ?></td>
                                    <td><span class="badge bg-danger">Cancelada</span></td>
                                    <td class="text-end">
                                        <a href="index.php?pagina=reserva_detalle&id=<?= $canc['codReserva'] ?>" class="btn btn-sm btn-outline-secondary">
                                            Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted small mb-0 py-2">No tenés reservas canceladas.</p>
            <?php endif; ?>
        </section>
    </div>

    <!-- COLUMNA LATERAL: RESUMEN Y NOVEDADES -->
    <aside class="col-lg-4">
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white mb-3">
            <h2 class="h6 fw-bold mb-2"><i class="bi bi-person-badge text-primary me-2"></i>Pasajero Titular</h2>
            <div class="small">Nombre: <strong><?= htmlspecialchars($usuario['nombre']) ?></strong></div>
            <div class="small text-muted mb-3">Email: <?= htmlspecialchars($usuario['email']) ?></div>
            <div class="p-3 bg-light rounded text-center">
                <span class="fs-4 fw-bold text-primary"><?= count($comprasConfirmadas) ?></span>
                <div class="small text-muted">Vuelos confirmados en Cóndor</div>
            </div>
        </div>

        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white">
            <h2 class="h6 fw-bold mb-3"><i class="bi bi-newspaper text-info me-2"></i>Novedades</h2>
            <div class="d-flex flex-column gap-2 small">
                <?php foreach (array_slice($novedades, 0, 2) as $n): ?>
                    <div class="p-2 bg-light rounded">
                        <strong class="d-block text-dark"><?= date('d/m/Y', strtotime($n['fechaPublicacionNovedad'])) ?></strong>
                        <span class="text-muted"><?= htmlspecialchars($n['textoNovedad']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>
</div>
