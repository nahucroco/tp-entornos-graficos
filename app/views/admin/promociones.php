<?php
declare(strict_types=1);
require_once __DIR__ . '/../../helpers/datos.php';

if (!usuarioLogueado() || !esAdmin()) {
    header('Location: index.php?pagina=login');
    exit;
}

$promociones = obtenerTodasPromociones();
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=admin_panel">Administración</a></li>
        <li class="breadcrumb-item active" aria-current="page">Bandeja de Promociones</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">
            <i class="bi bi-check2-circle text-warning me-2"></i>Aprobación de Promociones Comerciales
        </h1>
        <p class="text-muted small mb-0">Revisión y autorización de las ofertas creadas por los CEOs de las aerolíneas.</p>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Aerolínea</th>
                    <th>Descripción</th>
                    <th>Descuento</th>
                    <th>Vigencia</th>
                    <th>Estado</th>
                    <th class="text-end">Decisión</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promociones as $pr): ?>
                    <tr>
                        <td><strong>#<?= $pr['codPromocion'] ?></strong></td>
                        <td>
                            <span class="badge bg-primary"><?= htmlspecialchars($pr['nombreAerolinea']) ?></span>
                        </td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($pr['descripcionPromocion']) ?></td>
                        <td><span class="badge bg-danger fs-6">-<?= (int)$pr['descuentoPromocion'] ?>%</span></td>
                        <td class="small text-muted">
                            <?= date('d/m/Y', strtotime($pr['fechaInicio'] ?? date('Y-m-d'))) ?> al <?= date('d/m/Y', strtotime($pr['fechaFin'] ?? '+30 days')) ?>
                        </td>
                        <td>
                            <?php if ($pr['estadoPromocion'] === 'aprobada'): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aprobada</span>
                            <?php elseif ($pr['estadoPromocion'] === 'denegada'): ?>
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Denegada</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($pr['estadoPromocion'] !== 'aprobada'): ?>
                                <form method="post" action="index.php" class="d-inline">
                                    <input type="hidden" name="accion" value="aprobar_promocion">
            <?= campoCsrf() ?>
                                    <input type="hidden" name="promocion_id" value="<?= $pr['codPromocion'] ?>">
                                    <input type="hidden" name="estado" value="aprobada">
                                    <button type="submit" class="btn btn-sm btn-success" title="Aprobar promoción">
                                        <i class="bi bi-check-lg me-1"></i>Aprobar
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($pr['estadoPromocion'] !== 'denegada'): ?>
                                <form method="post" action="index.php" class="d-inline">
                                    <input type="hidden" name="accion" value="aprobar_promocion">
            <?= campoCsrf() ?>
                                    <input type="hidden" name="promocion_id" value="<?= $pr['codPromocion'] ?>">
                                    <input type="hidden" name="estado" value="denegada">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Denegar promoción">
                                        <i class="bi bi-x-lg me-1"></i>Rechazar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
