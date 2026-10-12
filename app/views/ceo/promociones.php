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
$promociones = obtenerPromocionesPorCeo((int)$usuario['id']);
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=ceo_panel">Panel CEO</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gestión de Promociones</li>
    </ol>
</nav>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">
            <i class="bi bi-tag-fill text-warning me-2"></i>Promociones de <?= htmlspecialchars($aerolinea['nombreAerolinea'] ?? 'Mi Aerolínea') ?>
        </h1>
        <p class="text-muted small mb-0">Creación de ofertas comerciales. Toda promoción queda en estado <em>pendiente</em> hasta la autorización del Administrador.</p>
    </div>
    <button class="btn btn-warning btn-sm text-dark fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#formPromoCollapse">
        <i class="bi bi-plus-lg me-1"></i>Crear Nueva Promoción
    </button>
</div>

<!-- FORMULARIO DE ALTA DE PROMOCIÓN -->
<div class="collapse mb-4" id="formPromoCollapse">
    <div class="card shadow-sm border-0 rounded-4 p-4 bg-white border-warning border-top border-4">
        <h2 class="h5 fw-bold mb-3">Registrar Nueva Oferta Promocional</h2>

        <form method="post" action="index.php" class="needs-validation" novalidate data-validar>
            <input type="hidden" name="accion" value="crear_promocion_ceo">
            <?= campoCsrf() ?>
            <input type="hidden" name="aerolinea_id" value="<?= $aerolinea['codAerolinea'] ?>">

            <div class="row g-3">
                <div class="col-md-8">
                    <label for="p-desc" class="form-label small fw-bold">Descripción de la Promoción <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="p-desc" name="descripcion" required placeholder="Ej: 25% OFF en vuelos de primavera">
                </div>
                <div class="col-md-4">
                    <label for="p-desc-porc" class="form-label small fw-bold">Porcentaje de Descuento (%) <span class="text-danger">*</span></label>
                    <input type="number" step="1" min="1" max="90" class="form-control" id="p-desc-porc" name="descuento" required placeholder="Ej: 20">
                </div>
                <div class="col-md-6">
                    <label for="p-inicio" class="form-label small fw-bold">Fecha de Inicio</label>
                    <input type="date" class="form-control" id="p-inicio" name="fecha_inicio" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-6">
                    <label for="p-fin" class="form-label small fw-bold">Fecha de Vencimiento / Fin</label>
                    <input type="date" class="form-control" id="p-fin" name="fecha_fin" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#formPromoCollapse">Cancelar</button>
                <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold px-4">
                    <i class="bi bi-send me-1"></i>Enviar para Aprobación
                </button>
            </div>
        </form>
    </div>
</div>

<!-- LISTADO DE PROMOCIONES DE LA AEROLÍNEA -->
<div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 fw-bold mb-0">Historial de Promociones</h2>
        <span class="badge bg-secondary"><?= count($promociones) ?> registrada(s)</span>
    </div>

    <?php if (!empty($promociones)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Descuento</th>
                        <th>Período de Vigencia</th>
                        <th>Estado de Aprobación</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($promociones as $pr): ?>
                        <tr>
                            <td><strong>PRO#<?= $pr['codPromocion'] ?></strong></td>
                            <td class="fw-semibold"><?= htmlspecialchars($pr['descripcionPromocion']) ?></td>
                            <td><span class="badge bg-danger fs-6">-<?= (int)$pr['descuentoPromocion'] ?>%</span></td>
                            <td class="small text-muted">
                                <?= date('d/m/Y', strtotime($pr['fechaInicio'] ?? date('Y-m-d'))) ?> al <?= date('d/m/Y', strtotime($pr['fechaFin'] ?? '+30 days')) ?>
                            </td>
                            <td>
                                <?php if ($pr['estadoPromocion'] === 'aprobada'): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aprobada (Activa)</span>
                                <?php elseif ($pr['estadoPromocion'] === 'denegada'): ?>
                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Denegada</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Pendiente de Admin</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <form method="post" action="index.php" class="d-inline" onsubmit="return confirm('¿Seguro que deseás eliminar esta promoción?');">
                                    <input type="hidden" name="accion" value="eliminar_promocion_ceo">
            <?= campoCsrf() ?>
                                    <input type="hidden" name="promocion_id" value="<?= $pr['codPromocion'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted small mb-0 py-3 text-center">No registrás promociones creadas para tu aerolínea todavía.</p>
    <?php endif; ?>
</div>
