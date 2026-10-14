<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/datos.php';

$promociones = obtenerPromocionesAprobadas();
$novedades = obtenerNovedadesVigentes();
$aerolineas = obtenerAerolineas();
?>

<!-- HERO BANNER TIPO DESPEGAR -->
<section class="hero-despegar text-center text-md-start">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <span class="badge bg-warning text-dark fw-bold px-3 py-2 text-uppercase mb-3 shadow-sm">
                <i class="bi bi-airplane-fill me-1"></i>Vuelos al mejor precio de Argentina
            </span>
            <h1 class="display-5 fw-bold mb-3">Encontrá, compará y reservá tus vuelos</h1>
            <p class="lead mb-0 text-white-50">
                Viajá con las mejores aerolíneas comerciales, accedé a descuentos exclusivos y gestioná tus reservas en un solo lugar.
            </p>
        </div>
        <div class="col-lg-4 d-none d-lg-block text-end">
            <img src="/assets/img/logo.png" alt="Logo Cóndor Aero" width="300" height="240" class="d-inline-block align-text-top">
        </div>
    </div>
</section>

<!-- WIDGET DE BÚSQUEDA FLOTANTE (ESTILO DESPEGAR) -->
<section class="search-box-card" aria-label="Buscador de vuelos">
    <div class="d-flex align-items-center gap-3 mb-3 border-bottom pb-2">
        <span class="fw-bold text-primary"><i class="bi bi-airplane me-2"></i>Vuelos</span>
        <div class="form-check form-check-inline mb-0">
            <input class="form-check-input" type="radio" name="tipoViaje" id="idaVuelta" value="ida_vuelta" checked>
            <label class="form-check-label small fw-semibold" for="idaVuelta">Ida y vuelta</label>
        </div>
        <div class="form-check form-check-inline mb-0">
            <input class="form-check-input" type="radio" name="tipoViaje" id="soloIda" value="solo_ida">
            <label class="form-check-label small fw-semibold" for="soloIda">Solo ida</label>
        </div>
    </div>

    <form method="get" action="index.php" class="needs-validation" novalidate>
        <input type="hidden" name="pagina" value="vuelos">
        <div class="row g-3 align-items-end">
            <!-- Origen -->
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted mb-1" for="hero-origen">
                    <i class="bi bi-geo-alt me-1 text-primary"></i>Origen
                </label>
                <div class="input-group">
                    <input type="text" class="form-control" id="hero-origen" name="origen" placeholder="Ej. Rosario (ROS)" list="lista-origenes" required>
                    <datalist id="lista-origenes">
                        <option value="Rosario (ROS)">
                        <option value="Buenos Aires (AEP)">
                        <option value="Buenos Aires (EZE)">
                        <option value="Córdoba (COR)">
                        <option value="Mendoza (MDZ)">
                        <option value="Bariloche (BRC)">
                    </datalist>
                </div>
            </div>

            <!-- Destino -->
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted mb-1" for="hero-destino">
                    <i class="bi bi-geo-alt-fill me-1 text-danger"></i>Destino
                </label>
                <div class="input-group">
                    <input type="text" class="form-control" id="hero-destino" name="destino" placeholder="Ej. Bariloche (BRC)" list="lista-destinos" required>
                    <datalist id="lista-destinos">
                        <option value="Buenos Aires (AEP)">
                        <option value="Bariloche (BRC)">
                        <option value="Córdoba (COR)">
                        <option value="Iguazú (IGR)">
                        <option value="Mendoza (MDZ)">
                        <option value="Santiago de Chile (SCL)">
                        <option value="Ushuaia (USH)">
                    </datalist>
                </div>
            </div>

            <!-- Fecha Salida -->
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted mb-1" for="hero-fecha">
                    <i class="bi bi-calendar-event me-1 text-primary"></i>Fecha ida
                </label>
                <input type="date" class="form-control" id="hero-fecha" name="fecha" value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
            </div>

            <!-- Pasajeros -->
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted mb-1" for="hero-pasajeros">
                    <i class="bi bi-people me-1 text-primary"></i>Pasajeros
                </label>
                <select class="form-select" id="hero-pasajeros" name="pasajeros">
                    <option value="1" selected>1 adulto</option>
                    <option value="2">2 adultos</option>
                    <option value="3">3 adultos</option>
                    <option value="4">Familia (4+)</option>
                </select>
            </div>

            <!-- Botón Buscar -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-despegar w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-search"></i>
                    <span>Buscar</span>
                </button>
            </div>
        </div>
    </form>
</section>

<!-- SECCIÓN: PROMOCIONES Y OFERTAS EXCLUSIVAS -->
<section class="mb-5" aria-labelledby="titulo-promociones">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 id="titulo-promociones" class="h4 fw-bold mb-0">
                <i class="bi bi-lightning-charge-fill text-warning me-2"></i>Promociones Destacadas
            </h2>
            <p class="text-muted small mb-0">Ofertas autorizadas por la administración para viajar con descuento.</p>
        </div>
        <a href="index.php?pagina=promociones" class="btn btn-outline-primary btn-sm fw-semibold">
            Ver todas <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="row g-3">
        <?php if (!empty($promociones)): ?>
            <?php foreach (array_slice($promociones, 0, 3) as $promo): ?>
                <div class="col-md-4">
                    <article class="card promo-card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-danger fs-6 fw-bold">
                                    <?= (int)$promo['descuentoPromocion'] ?>% OFF
                                </span>
                                <span class="badge bg-light text-dark border">
                                    <?= htmlspecialchars($promo['nombreAerolinea']) ?>
                                </span>
                            </div>
                            <h3 class="h6 fw-bold mb-2"><?= htmlspecialchars($promo['descripcionPromocion']) ?></h3>
                            <p class="small text-muted mb-3 flex-grow-1">
                                Promoción válida hasta el <?= date('d/m/Y', strtotime($promo['fechaFin'] ?? '+30 days')) ?> o hasta agotar cupos disponibles.
                            </p>
                            <a href="index.php?pagina=vuelos&aerolinea=<?= $promo['codAerolinea'] ?>" class="btn btn-sm btn-outline-primary mt-auto">
                                Ver vuelos con descuento
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-light border text-center text-muted py-4">
                    No hay promociones vigentes en este momento.
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- SECCIÓN: AEROLÍNEAS SOCIAS (CATÁLOGO RÁPIDO) -->
<section class="mb-5" aria-labelledby="titulo-aerolineas">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 id="titulo-aerolineas" class="h4 fw-bold mb-0">
                <i class="bi bi-buildings-fill text-primary me-2"></i>Nuestras Aerolíneas
            </h2>
            <p class="text-muted small mb-0">Operamos con las compañías aéreas líderes de la región.</p>
        </div>
        <a href="index.php?pagina=aerolineas" class="btn btn-outline-primary btn-sm fw-semibold">
            Ver catálogo <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="row g-3">
        <?php foreach ($aerolineas as $aero): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border p-3">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <strong class="fs-6"><?= htmlspecialchars($aero['codigoIATA']) ?></strong>
                        </div>
                        <div>
                            <h3 class="h6 fw-bold mb-0"><?= htmlspecialchars($aero['nombreAerolinea']) ?></h3>
                            <small class="text-muted">País: <?= htmlspecialchars($aero['codPais']) ?></small>
                        </div>
                    </div>
                    <p class="small text-muted mb-3 flex-grow-1">
                        <?= htmlspecialchars($aero['descripcionAerolinea']) ?>
                    </p>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="small text-muted"><?= (int)$aero['totalVuelos'] ?> vuelos activos</span>
                        <a href="index.php?pagina=aerolinea_detalle&id=<?= $aero['codAerolinea'] ?>" class="btn btn-sm btn-link text-decoration-none fw-semibold">
                            Ver rutas <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- SECCIÓN: NOVEDADES Y COMUNICADOS OFICIALES -->
<section class="mb-5" aria-labelledby="titulo-novedades">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 id="titulo-novedades" class="h4 fw-bold mb-0">
                <i class="bi bi-megaphone-fill text-info me-2"></i>Avisos y Novedades del Sistema
            </h2>
            <p class="text-muted small mb-0">Información aeroportuaria, reprogramaciones y comunicados oficiales.</p>
        </div>
        <a href="index.php?pagina=novedades" class="btn btn-outline-primary btn-sm fw-semibold">
            Todas las novedades <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="row g-3">
        <?php foreach ($novedades as $nov): ?>
            <div class="col-md-6">
                <article class="card h-100 border shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2 text-muted small">
                            <i class="bi bi-calendar3"></i>
                            <span>Publicado: <?= date('d/m/Y', strtotime($nov['fechaPublicacionNovedad'])) ?></span>
                            <span class="badge bg-secondary ms-auto">Oficial</span>
                        </div>
                        <p class="mb-0 fw-medium">
                            <?= htmlspecialchars($nov['textoNovedad']) ?>
                        </p>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- SECCIÓN: BENEFICIOS DE CONDOR (ESTILO DESPEGAR) -->
<section class="bg-white rounded-4 p-4 shadow-sm border mb-4">
    <div class="row text-center g-4">
        <div class="col-md-4">
            <div class="text-primary mb-2 display-6"><i class="bi bi-shield-lock-fill"></i></div>
            <h3 class="h6 fw-bold">Compra 100% Protegida</h3>
            <p class="text-muted small mb-0">Tus datos y reservas están aseguradas con protocolos de seguridad bancaria.</p>
        </div>
        <div class="col-md-4">
            <div class="text-warning mb-2 display-6"><i class="bi bi-clock-history"></i></div>
            <h3 class="h6 fw-bold">Cancelación Flexible (72 hs)</h3>
            <p class="text-muted small mb-0">Podés cancelar cualquier reserva sin costo hasta 72 horas antes del despegue.</p>
        </div>
        <div class="col-md-4">
            <div class="text-success mb-2 display-6"><i class="bi bi-headset"></i></div>
            <h3 class="h6 fw-bold">Soporte Inmediato</h3>
            <p class="text-muted small mb-0">Asistencia las 24 horas y atención personalizada para todos los pasajeros.</p>
        </div>
    </div>
</section>
