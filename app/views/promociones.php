<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/datos.php';

$promociones = obtenerPromocionesAprobadas();
$novedades = obtenerNovedadesVigentes();
$usuario = obtenerUsuario();
$termino = trim($_GET['buscar'] ?? '');

if (!empty($termino)) {
    $promociones = array_filter($promociones, function($p) use ($termino) {
        return stripos($p['descripcionPromocion'], $termino) !== false 
            || stripos($p['nombreAerolinea'], $termino) !== false
            || stripos((string)$p['codPromocion'], $termino) !== false;
    });
}
?>

<!-- BREADCRUMBS -->
<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Promociones</li>
    </ol>
</nav>

<div class="row g-4">
    <!-- COLUMNA PRINCIPAL: CATÁLOGO DE PROMOCIONES -->
    <div class="col-lg-8">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
            <div>
                <h1 class="h4 fw-bold mb-0">
                    <i class="bi bi-tag-fill text-danger me-2"></i>Últimas Promociones de Cóndor
                </h1>
                <p class="text-muted small mb-0">Descuentos comerciales aprobados por la administración para viajar barato.</p>
            </div>
            
            <form method="get" action="index.php" class="d-flex gap-2">
                <input type="hidden" name="pagina" value="promociones">
                <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Buscar promoción..." value="<?= htmlspecialchars($termino) ?>">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <?php if (!empty($promociones)): ?>
            <div class="row g-3">
                <?php foreach ($promociones as $promo): ?>
                    <div class="col-md-6">
                        <article class="card promo-card h-100 shadow-sm border p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-danger fs-6 fw-bold">
                                    -<?= (int)$promo['descuentoPromocion'] ?>% OFF
                                </span>
                                <span class="badge bg-light text-dark border small">
                                    PROMO #<?= $promo['codPromocion'] ?>
                                </span>
                            </div>

                            <h2 class="h6 fw-bold mb-2 text-dark">
                                <?= htmlspecialchars($promo['descripcionPromocion']) ?>
                            </h2>

                            <p class="small text-muted mb-3 flex-grow-1">
                                Operada por <strong><?= htmlspecialchars($promo['nombreAerolinea']) ?></strong> (<?= htmlspecialchars($promo['codigoIATA']) ?>). Descuento directo aplicable sobre la tarifa de vuelo al momento de reservar.
                            </p>

                            <div class="pt-2 border-top small text-muted mb-3">
                                <i class="bi bi-calendar3 me-1"></i>Vigencia: hasta <?= date('d/m/Y', strtotime($promo['fechaFin'] ?? '+30 days')) ?>
                            </div>

                            <a href="index.php?pagina=vuelos&aerolinea=<?= $promo['codAerolinea'] ?>" class="btn btn-despegar btn-sm w-100">
                                Ver vuelos y aplicar descuento
                            </a>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card p-5 text-center bg-white border-0 shadow-sm rounded-4">
                <i class="bi bi-tag display-4 text-muted mb-3"></i>
                <h2 class="h5 fw-bold">No hay promociones que coincidan</h2>
                <p class="text-muted">Podés revisar el listado completo de vuelos o consultar las novedades del sistema.</p>
                <div>
                    <a href="index.php?pagina=promociones" class="btn btn-outline-primary">Ver todas las promociones</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- COLUMNA LATERAL (SEGUN WIREFRAME): NOVEDADES Y CUENTA -->
    <aside class="col-lg-4">
        <!-- Mi cuenta -->
        <?php if (usuarioLogueado()): ?>
            <div class="card p-3 rounded-4 shadow-sm border-0 bg-white mb-3">
                <h2 class="h6 fw-bold mb-2"><i class="bi bi-person-check text-primary me-2"></i>Mi Cuenta</h2>
                <div class="small">Pasajero: <strong><?= htmlspecialchars($usuario['nombre']) ?></strong></div>
                <div class="small text-muted mb-2">Email: <?= htmlspecialchars($usuario['email']) ?></div>
                <div class="p-2 bg-light rounded text-center small">
                    <span class="text-success fw-bold"><i class="bi bi-star-fill text-warning me-1"></i>Nivel Plata</span> · Acceso a ofertas exclusivas
                </div>
            </div>
        <?php else: ?>
            <div class="card p-3 rounded-4 shadow-sm border-0 bg-white mb-3 text-center">
                <i class="bi bi-person-circle fs-2 text-primary mb-2"></i>
                <h2 class="h6 fw-bold mb-1">¿Querés más descuentos?</h2>
                <p class="small text-muted mb-3">Registrate gratis para recibir promociones exclusivas en tu correo.</p>
                <a href="index.php?pagina=registro" class="btn btn-outline-primary btn-sm">Crear cuenta</a>
            </div>
        <?php endif; ?>

        <!-- Novedades Relacionadas -->
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white">
            <h2 class="h6 fw-bold mb-3"><i class="bi bi-newspaper text-info me-2"></i>Novedades</h2>
            <div class="d-flex flex-column gap-2 small">
                <?php foreach (array_slice($novedades, 0, 3) as $nov): ?>
                    <div class="p-2 bg-light rounded">
                        <span class="text-muted" style="font-size: 0.75rem;"><?= date('d/m/Y', strtotime($nov['fechaPublicacionNovedad'])) ?></span>
                        <p class="mb-0 text-dark"><?= htmlspecialchars($nov['textoNovedad']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>
</div>
