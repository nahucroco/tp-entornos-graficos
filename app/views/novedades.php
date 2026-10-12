<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/datos.php';

$novedades = obtenerNovedadesVigentes();
$promociones = obtenerPromocionesAprobadas();
$usuario = obtenerUsuario();
$termino = trim($_GET['buscar'] ?? '');

if (!empty($termino)) {
    $novedades = array_filter($novedades, function($n) use ($termino) {
        return stripos($n['textoNovedad'], $termino) !== false 
            || stripos((string)$n['codNovedad'], $termino) !== false;
    });
}
?>

<!-- BREADCRUMBS -->
<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Novedades</li>
    </ol>
</nav>

<div class="row g-4">
    <!-- COLUMNA PRINCIPAL -->
    <div class="col-lg-8">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
            <div>
                <h1 class="h4 fw-bold mb-0">
                    <i class="bi bi-newspaper text-info me-2"></i>Últimas Novedades de Cóndor
                </h1>
                <p class="text-muted small mb-0">Comunicados oficiales, avisos de aeropuertos y alertas operativas.</p>
            </div>
            
            <form method="get" action="index.php" class="d-flex gap-2">
                <input type="hidden" name="pagina" value="novedades">
                <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Buscar novedad..." value="<?= htmlspecialchars($termino) ?>">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <?php if (!empty($novedades)): ?>
            <div class="row g-3">
                <?php foreach ($novedades as $nov): ?>
                    <div class="col-md-6">
                        <article class="card h-100 shadow-sm border p-3 bg-white">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-info text-dark">
                                    <i class="bi bi-megaphone me-1"></i>Comunicado
                                </span>
                                <span class="badge bg-light text-muted border small">
                                    NOV #<?= $nov['codNovedad'] ?>
                                </span>
                            </div>

                            <p class="fw-semibold text-dark mb-3 flex-grow-1">
                                <?= htmlspecialchars($nov['textoNovedad']) ?>
                            </p>

                            <div class="pt-2 border-top small text-muted">
                                <div><i class="bi bi-calendar3 me-1"></i>Publicado: <?= date('d/m/Y', strtotime($nov['fechaPublicacionNovedad'])) ?></div>
                                <div><i class="bi bi-clock-history me-1"></i>Vigente hasta: <?= date('d/m/Y', strtotime($nov['fechaExpiracionNovedad'])) ?></div>
                                <div class="mt-1"><i class="bi bi-person-badge me-1"></i>Por: <?= htmlspecialchars($nov['publicadoPor'] ?? 'Administración General') ?></div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card p-5 text-center bg-white border-0 shadow-sm rounded-4">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <h2 class="h5 fw-bold">No hay novedades registradas</h2>
                <p class="text-muted">No se encontraron avisos vigentes con ese criterio de búsqueda.</p>
                <div>
                    <a href="index.php?pagina=novedades" class="btn btn-outline-primary">Ver todas</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- COLUMNA LATERAL (SEGUN WIREFRAME): PROMOS Y CUENTA -->
    <aside class="col-lg-4">
        <!-- Promociones Relacionadas -->
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white mb-3">
            <h2 class="h6 fw-bold mb-3"><i class="bi bi-tag-fill text-danger me-2"></i>Promociones Activas</h2>
            <div class="d-flex flex-column gap-2 small">
                <?php foreach (array_slice($promociones, 0, 3) as $pr): ?>
                    <div class="p-2 bg-light rounded">
                        <span class="badge bg-danger mb-1"><?= (int)$pr['descuentoPromocion'] ?>% OFF</span>
                        <p class="mb-1 text-dark fw-semibold"><?= htmlspecialchars($pr['descripcionPromocion']) ?></p>
                        <small class="text-muted"><?= htmlspecialchars($pr['nombreAerolinea']) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Mi Cuenta -->
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white">
            <h2 class="h6 fw-bold mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Centro de Información</h2>
            <p class="small text-muted mb-0">
                Las novedades son publicadas por la administración para mantener informados a todos los pasajeros sobre condiciones climáticas, estado de aeropuertos y nuevas frecuencias.
            </p>
        </div>
    </aside>
</div>
