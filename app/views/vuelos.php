<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/datos.php';

// Capturar parámetros de búsqueda y filtros
$filtroOrigen = trim($_GET['origen'] ?? '');
$filtroDestino = trim($_GET['destino'] ?? '');
$filtroFecha = trim($_GET['fecha'] ?? '');
$filtroAerolinea = !empty($_GET['aerolinea']) ? (int)$_GET['aerolinea'] : null;
$filtroPrecioMax = !empty($_GET['precio_max']) ? (float)$_GET['precio_max'] : null;
$orden = $_GET['orden'] ?? 'precio_asc';

// Paginación numérica
$paginaActual = max(1, (int)($_GET['p'] ?? 1));
$porPagina = 4;
$totalVuelos = contarTotalVuelos($filtroOrigen, $filtroDestino, $filtroFecha, $filtroAerolinea, $filtroPrecioMax);
$totalPaginas = (int)ceil($totalVuelos / $porPagina);
$offset = ($paginaActual - 1) * $porPagina;

// Consultar vuelos en la BD con paginación
$vuelos = obtenerVuelos($filtroOrigen, $filtroDestino, $filtroFecha, $filtroAerolinea, $filtroPrecioMax, $orden, $porPagina, $offset);
$aerolineas = obtenerAerolineas();
?>

<!-- BREADCRUMBS -->
<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Búsqueda de Vuelos</li>
    </ol>
</nav>

<!-- BUSCADOR SUPERIOR COMPACTO (ESTILO DESPEGAR) -->
<div class="card shadow-sm border-0 rounded-4 p-3 mb-4 bg-white">
    <form method="get" action="index.php" class="row g-2 align-items-end">
        <input type="hidden" name="pagina" value="vuelos">
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted mb-0" for="filtro-origen">Origen</label>
            <input type="text" class="form-control form-control-sm" id="filtro-origen" name="origen" value="<?= htmlspecialchars($filtroOrigen) ?>" placeholder="Ciudad o código (ROS)">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted mb-0" for="filtro-destino">Destino</label>
            <input type="text" class="form-control form-control-sm" id="filtro-destino" name="destino" value="<?= htmlspecialchars($filtroDestino) ?>" placeholder="Ciudad o código (AEP)">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-0" for="filtro-fecha">Fecha</label>
            <input type="date" class="form-control form-control-sm" id="filtro-fecha" name="fecha" value="<?= htmlspecialchars($filtroFecha) ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-0" for="filtro-orden">Ordenar por</label>
            <select class="form-select form-select-sm" id="filtro-orden" name="orden">
                <option value="precio_asc" <?= $orden === 'precio_asc' ? 'selected' : '' ?>>Menor precio</option>
                <option value="precio_desc" <?= $orden === 'precio_desc' ? 'selected' : '' ?>>Mayor precio</option>
                <option value="fecha_asc" <?= $orden === 'fecha_asc' ? 'selected' : '' ?>>Más temprano</option>
                <option value="asientos_desc" <?= $orden === 'asientos_desc' ? 'selected' : '' ?>>Más asientos</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-despegar btn-sm w-100">
                <i class="bi bi-search me-1"></i>Buscar
            </button>
        </div>
    </form>
</div>

<div class="row g-4">
    <!-- BARRA LATERAL DE FILTROS (COLUMNA IZQUIERDA) -->
    <aside class="col-lg-3" aria-label="Filtros de búsqueda">
        <div class="filter-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 fw-bold mb-0"><i class="bi bi-funnel me-1 text-primary"></i>Filtros</h2>
                <a href="index.php?pagina=vuelos" class="small text-decoration-none">Limpiar</a>
            </div>

            <form method="get" action="index.php">
                <input type="hidden" name="pagina" value="vuelos">
                <input type="hidden" name="origen" value="<?= htmlspecialchars($filtroOrigen) ?>">
                <input type="hidden" name="destino" value="<?= htmlspecialchars($filtroDestino) ?>">
                <input type="hidden" name="fecha" value="<?= htmlspecialchars($filtroFecha) ?>">
                <input type="hidden" name="orden" value="<?= htmlspecialchars($orden) ?>">

                <!-- Filtro por Aerolínea -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark mb-2">Aerolínea</label>
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="aerolinea" id="aero-todas" value="" <?= empty($filtroAerolinea) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="aero-todas">Todas las aerolíneas</label>
                        </div>
                        <?php foreach ($aerolineas as $aero): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="aerolinea" id="aero-<?= $aero['codAerolinea'] ?>" value="<?= $aero['codAerolinea'] ?>" <?= $filtroAerolinea === (int)$aero['codAerolinea'] ? 'checked' : '' ?>>
                                <label class="form-check-label small d-flex justify-content-between" for="aero-<?= $aero['codAerolinea'] ?>">
                                    <span><?= htmlspecialchars($aero['nombreAerolinea']) ?></span>
                                    <span class="text-muted small">(<?= htmlspecialchars($aero['codigoIATA']) ?>)</span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Filtro por Precio Máximo -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark mb-1" for="precio-max">Precio máximo (ARS)</label>
                    <input type="number" step="5000" min="20000" class="form-control form-control-sm" id="precio-max" name="precio_max" value="<?= $filtroPrecioMax ? (int)$filtroPrecioMax : '' ?>" placeholder="Ej: 80000">
                </div>

                <!-- Escalas simuladas (Informativo Despegar) -->
                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark mb-2">Escalas</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="escala-directo" checked disabled>
                        <label class="form-check-label small" for="escala-directo">Solo vuelos directos</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                    Aplicar filtros
                </button>
            </form>
        </div>
    </aside>

    <!-- LISTADO DE VUELOS (COLUMNA DERECHA) -->
    <section class="col-lg-9" aria-labelledby="titulo-resultados">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 id="titulo-resultados" class="h5 fw-bold mb-0">
                Vuelos disponibles <span class="badge bg-secondary"><?= count($vuelos) ?></span>
            </h1>
            <span class="small text-muted">Precios finales por pasajero en pesos argentinos (ARS)</span>
        </div>

        <?php if (!empty($vuelos)): ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($vuelos as $vuelo): ?>
                    <?php
                        $horaSalida = date('H:i', strtotime($vuelo['horaSalidaVuelo']));
                        // Estimar llegada (+1h 20m para vuelos nacionales)
                        $horaLlegada = date('H:i', strtotime($vuelo['horaSalidaVuelo'] . ' +1 hour +20 minutes'));
                        $asientos = (int)$vuelo['asientosDisponibles'];
                    ?>
                    <article class="flight-card" aria-label="Vuelo de <?= htmlspecialchars($vuelo['origenVuelo']) ?> a <?= htmlspecialchars($vuelo['destinoVuelo']) ?>">
                        <div class="row g-0 align-items-center">
                            <!-- Datos del Vuelo e Itinerario -->
                            <div class="col-md-8 p-3 p-lg-4">
                                <!-- Badge Aerolínea -->
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="airline-badge">
                                        <i class="bi bi-airplane-fill text-primary"></i>
                                        <span><?= htmlspecialchars($vuelo['nombreAerolinea']) ?> (<?= htmlspecialchars($vuelo['codigoIATA']) ?>)</span>
                                    </div>
                                    <span class="badge bg-light text-muted border small">
                                        Vuelo #<?= $vuelo['codVuelo'] ?>
                                    </span>
                                </div>

                                <!-- Itinerario Gráfico Horario / Duración -->
                                <div class="d-flex align-items-center justify-content-between text-center mb-3">
                                    <!-- Salida -->
                                    <div class="text-start">
                                        <div class="flight-time"><?= $horaSalida ?></div>
                                        <div class="fw-semibold text-dark small"><?= htmlspecialchars($vuelo['origenVuelo']) ?></div>
                                        <div class="small text-muted"><?= date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])) ?></div>
                                    </div>

                                    <!-- Línea con avión y duración -->
                                    <div class="flight-route-line px-2">
                                        <span class="small text-muted fw-semibold">1h 20m</span>
                                        <div class="line"></div>
                                        <span class="badge text-bg-success py-0" style="font-size: 0.65rem;">Directo</span>
                                    </div>

                                    <!-- Llegada -->
                                    <div class="text-end">
                                        <div class="flight-time"><?= $horaLlegada ?></div>
                                        <div class="fw-semibold text-dark small"><?= htmlspecialchars($vuelo['destinoVuelo']) ?></div>
                                        <div class="small text-muted"><?= date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])) ?></div>
                                    </div>
                                </div>

                                <!-- Beneficios incluidos y disponibilidad -->
                                <div class="d-flex flex-wrap align-items-center gap-3 pt-2 border-top small">
                                    <span class="text-muted"><i class="bi bi-bag-check me-1 text-success"></i>Bolso de mano incluido</span>
                                    <span class="text-muted"><i class="bi bi-arrow-repeat me-1 text-primary"></i>Cancelación 72hs</span>
                                    <?php if ($asientos <= 10): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>¡Últimos <?= $asientos ?> asientos!</span>
                                    <?php else: ?>
                                        <span class="text-success"><i class="bi bi-check2-circle me-1"></i><?= $asientos ?> asientos disponibles</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Caja de Precio y Botón Reservar (Lado Derecho) -->
                            <div class="col-md-4 price-box text-center text-md-end">
                                <span class="small text-muted">Final por adulto</span>
                                <div class="price-tag mb-1">
                                    $<?= number_format((float)$vuelo['precioVuelo'], 0, ',', '.') ?>
                                </div>
                                <span class="small text-muted mb-3 d-block">Tasas e impuestos incluidos</span>

                                <?php if ($asientos > 0): ?>
                                    <div class="d-flex flex-column gap-2">
                                        <a href="index.php?pagina=reservar&vuelo=<?= $vuelo['codVuelo'] ?>" class="btn btn-despegar w-100">
                                            Reservar ahora
                                        </a>
                                        <a href="index.php?pagina=vuelo_detalle&id=<?= $vuelo['codVuelo'] ?>" class="btn btn-link btn-sm text-decoration-none text-muted">
                                            Ver detalle del vuelo
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100 disabled">Agotado</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Paginación Numérica -->
            <?= renderizarPaginador($paginaActual, $totalPaginas, 'vuelos', array_filter([
                'origen' => $filtroOrigen,
                'destino' => $filtroDestino,
                'fecha' => $filtroFecha,
                'aerolinea' => $filtroAerolinea,
                'precio_max' => $filtroPrecioMax,
                'orden' => $orden
            ])) ?>
        <?php else: ?>
            <div class="card p-5 text-center bg-white border-0 shadow-sm rounded-4">
                <i class="bi bi-search display-3 text-muted mb-3"></i>
                <h3 class="h5 fw-bold">No se encontraron vuelos para tu búsqueda</h3>
                <p class="text-muted mb-3">Probá cambiando las fechas o seleccionando otras ciudades de origen y destino.</p>
                <div>
                    <a href="index.php?pagina=vuelos" class="btn btn-condor-primary">Ver todos los vuelos disponibles</a>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>
