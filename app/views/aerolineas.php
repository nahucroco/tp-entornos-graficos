<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/datos.php';

$aerolineas = obtenerAerolineas();
$termino = trim($_GET['buscar'] ?? '');

if (!empty($termino)) {
    $aerolineas = array_filter($aerolineas, function($a) use ($termino) {
        return stripos($a['nombreAerolinea'], $termino) !== false 
            || stripos($a['codigoIATA'], $termino) !== false
            || stripos($a['codPais'], $termino) !== false;
    });
}
?>

<!-- BREADCRUMBS -->
<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=vuelos">Vuelos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Aerolíneas</li>
    </ol>
</nav>

<div class="row g-4">
    <!-- FILTROS LATERALES -->
    <aside class="col-lg-3" aria-label="Filtro de aerolíneas">
        <div class="filter-card">
            <h2 class="h6 fw-bold mb-3"><i class="bi bi-funnel text-primary me-2"></i>Filtrar Aerolíneas</h2>
            
            <form method="get" action="index.php">
                <input type="hidden" name="pagina" value="aerolineas">
                <div class="mb-3">
                    <label for="buscar-aero" class="form-label small fw-semibold text-muted">Buscar por nombre o código</label>
                    <input type="text" class="form-control form-control-sm" id="buscar-aero" name="buscar" value="<?= htmlspecialchars($termino) ?>" placeholder="Ej: ARG, Flybondi, LATAM">
                </div>
                <button type="submit" class="btn btn-outline-primary btn-sm w-100 mb-2">Filtrar</button>
                <?php if (!empty($termino)): ?>
                    <a href="index.php?pagina=aerolineas" class="btn btn-link btn-sm text-decoration-none w-100 text-center">Limpiar búsqueda</a>
                <?php endif; ?>
            </form>

            <hr>

            <h3 class="h6 fw-bold mb-2 small text-uppercase text-muted">Rutas Populares</h3>
            <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                <li><a href="index.php?pagina=vuelos&origen=Rosario&destino=Buenos Aires" class="text-decoration-none text-dark"><i class="bi bi-geo-alt me-1 text-primary"></i>Rosario → Buenos Aires</a></li>
                <li><a href="index.php?pagina=vuelos&origen=Buenos Aires&destino=Bariloche" class="text-decoration-none text-dark"><i class="bi bi-geo-alt me-1 text-primary"></i>Buenos Aires → Bariloche</a></li>
                <li><a href="index.php?pagina=vuelos&origen=Rosario&destino=Córdoba" class="text-decoration-none text-dark"><i class="bi bi-geo-alt me-1 text-primary"></i>Rosario → Córdoba</a></li>
                <li><a href="index.php?pagina=vuelos&origen=Buenos Aires&destino=Santiago" class="text-decoration-none text-dark"><i class="bi bi-geo-alt me-1 text-primary"></i>Buenos Aires → Santiago</a></li>
            </ul>
        </div>
    </aside>

    <!-- LISTADO DE AEROLÍNEAS -->
    <section class="col-lg-9" aria-labelledby="titulo-aerolineas">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 id="titulo-aerolineas" class="h4 fw-bold mb-0">
                <i class="bi bi-buildings text-primary me-2"></i>Aerolíneas Registradas
            </h1>
            <span class="badge bg-secondary"><?= count($aerolineas) ?> asociada(s)</span>
        </div>

        <?php if (!empty($aerolineas)): ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($aerolineas as $a): ?>
                    <article class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                        <div class="row align-items-center g-3">
                            <!-- Logo / Código IATA -->
                            <div class="col-md-2 text-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                    <span class="fs-4 fw-bold"><?= htmlspecialchars($a['codigoIATA']) ?></span>
                                </div>
                                <span class="badge bg-light text-dark border mt-2 small"><?= htmlspecialchars($a['codPais']) ?></span>
                            </div>

                            <!-- Información de la Aerolínea -->
                            <div class="col-md-7">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h2 class="h5 fw-bold mb-0"><?= htmlspecialchars($a['nombreAerolinea']) ?></h2>
                                    <span class="text-muted small">· IATA: <?= htmlspecialchars($a['codigoIATA']) ?></span>
                                </div>
                                <p class="small text-muted mb-2">
                                    <?= htmlspecialchars($a['descripcionAerolinea'] ?? 'Aerolínea comercial con cobertura regular.') ?>
                                </p>
                                <div class="d-flex flex-wrap gap-3 small text-muted">
                                    <span><i class="bi bi-airplane-engines me-1 text-primary"></i><?= (int)$a['totalVuelos'] ?> vuelos programados</span>
                                    <?php if ($a['precioMinimo']): ?>
                                        <span><i class="bi bi-tag me-1 text-success"></i>Desde $<?= number_format((float)$a['precioMinimo'], 0, ',', '.') ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="col-md-3 text-md-end">
                                <a href="index.php?pagina=aerolinea_detalle&id=<?= $a['codAerolinea'] ?>" class="btn btn-outline-primary w-100 mb-2 btn-sm">
                                    Ver Perfil y Rutas
                                </a>
                                <a href="index.php?pagina=vuelos&aerolinea=<?= $a['codAerolinea'] ?>" class="btn btn-despegar w-100 btn-sm">
                                    Buscar sus Vuelos
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card p-5 text-center bg-white border-0 shadow-sm rounded-4">
                <i class="bi bi-building-slash display-4 text-muted mb-3"></i>
                <h3 class="h5 fw-bold">No se encontraron aerolíneas</h3>
                <p class="text-muted">No hay aerolíneas registradas que coincidan con tu búsqueda.</p>
                <div>
                    <a href="index.php?pagina=aerolineas" class="btn btn-outline-primary">Ver todas</a>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>
