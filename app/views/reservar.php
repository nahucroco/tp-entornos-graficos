<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/datos.php';

$vueloId = (int)($_GET['vuelo'] ?? 0);
$vuelo = obtenerVueloPorId($vueloId);

if (!$vuelo) {
    echo '<div class="alert alert-danger">Vuelo no encontrado para realizar la reserva.</div>';
    echo '<a href="index.php?pagina=vuelos" class="btn btn-primary">Volver a vuelos</a>';
    return;
}

$promociones = obtenerPromocionesPorAerolinea((int)$vuelo['codAerolinea']);
$usuario = obtenerUsuario();
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=vuelos">Vuelos</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=vuelo_detalle&id=<?= $vuelo['codVuelo'] ?>">Vuelo #<?= $vuelo['codVuelo'] ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Completar Reserva</li>
    </ol>
</nav>

<div class="row g-4">
    <!-- COLUMNA IZQUIERDA: FORMULARIO DE RESERVA -->
    <div class="col-lg-8">
        <?php if (!usuarioLogueado()): ?>
            <div class="card p-4 rounded-4 shadow-sm border-0 bg-white text-center mb-4">
                <i class="bi bi-person-lock display-3 text-warning mb-3"></i>
                <h1 class="h4 fw-bold">Iniciá sesión para continuar con tu reserva</h1>
                <p class="text-muted">
                    Para asegurar tu pasaje y aplicar promociones personalizadas, necesitás estar identificado en el sistema.
                </p>
                <div class="d-flex justify-content-center gap-2 mt-2">
                    <a href="index.php?pagina=login&volver=reservar&vuelo=<?= $vuelo['codVuelo'] ?>" class="btn btn-primary px-4">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión
                    </a>
                    <a href="index.php?demo=pasajero&volver=reservar&vuelo=<?= $vuelo['codVuelo'] ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-lightning-fill text-warning me-1"></i>Demo Pasajero (1 clic)
                    </a>
                    <a href="index.php?pagina=registro" class="btn btn-outline-primary">
                        Registrarse
                    </a>
                </div>
            </div>
        <?php else: ?>
            <form method="post" action="index.php" class="needs-validation" novalidate data-validar>
                <input type="hidden" name="accion" value="crear_reserva">
                <input type="hidden" name="vuelo_id" value="<?= $vuelo['codVuelo'] ?>">
                <input type="hidden" id="precio-base" value="<?= (float)$vuelo['precioVuelo'] ?>">

                <!-- DATOS DEL PASAJERO -->
                <div class="card p-4 rounded-4 shadow-sm border-0 bg-white mb-4">
                    <h2 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-person-lines-fill text-primary"></i>Datos del Pasajero Titular
                    </h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="pasajero-nombre" class="form-label small fw-bold">Nombre completo</label>
                            <input type="text" class="form-control" id="pasajero-nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="pasajero-dni" class="form-label small fw-bold">DNI / Pasaporte</label>
                            <input type="text" class="form-control" id="pasajero-dni" name="dni" placeholder="Ej: 38123456" required>
                        </div>
                        <div class="col-md-6">
                            <label for="pasajero-email" class="form-label small fw-bold">Correo electrónico</label>
                            <input type="email" class="form-control" id="pasajero-email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="pasajero-telefono" class="form-label small fw-bold">Teléfono de contacto</label>
                            <input type="tel" class="form-control" id="pasajero-telefono" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>" placeholder="Ej: 341-555-0123" required>
                        </div>
                    </div>
                </div>

                <!-- SELECCIÓN DE PROMOCIÓN -->
                <div class="card p-4 rounded-4 shadow-sm border-0 bg-white mb-4">
                    <h2 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-tag-fill text-danger"></i>Promociones Disponibles
                    </h2>
                    <label for="select-promocion" class="form-label small text-muted">
                        Seleccioná un descuento autorizado para aplicar sobre la tarifa:
                    </label>
                    <select class="form-select" id="select-promocion" name="promocion_id">
                        <option value="" data-descuento="0" selected>Sin promoción (Tarifa estándar)</option>
                        <?php foreach ($promociones as $promo): ?>
                            <option value="<?= $promo['codPromocion'] ?>" data-descuento="<?= (float)$promo['descuentoPromocion'] ?>">
                                <?= htmlspecialchars($promo['descripcionPromocion']) ?> (<?= (int)$promo['descuentoPromocion'] ?>% Descuento)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- CONDICIONES DE CANCELACIÓN Y CONFIRMACIÓN -->
                <div class="card p-4 rounded-4 shadow-sm border-0 bg-white mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="acepto-terminos" required checked>
                        <label class="form-check-label small" for="acepto-terminos">
                            He leído y acepto los términos y condiciones de Cóndor AeroReserva y la política de cancelación hasta 72 horas antes del vuelo.
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-despegar btn-lg w-100 py-3 shadow">
                    <i class="bi bi-check-circle-fill me-2"></i>Confirmar y Generar Reserva
                </button>
            </form>
        <?php endif; ?>
    </div>

    <!-- COLUMNA DERECHA: RESUMEN DE LA RESERVA -->
    <div class="col-lg-4">
        <aside class="card p-4 rounded-4 shadow-sm border-0 bg-white sticky-top" style="top: 2rem;">
            <h2 class="h6 text-uppercase fw-bold text-muted mb-3">Resumen de tu viaje</h2>

            <!-- Ficha resumen del vuelo -->
            <div class="p-3 bg-light rounded-3 mb-3">
                <span class="badge bg-primary mb-2"><?= htmlspecialchars($vuelo['nombreAerolinea']) ?></span>
                <div class="fw-bold text-dark mb-1">
                    <?= htmlspecialchars($vuelo['origenVuelo']) ?> <i class="bi bi-arrow-right"></i> <?= htmlspecialchars($vuelo['destinoVuelo']) ?>
                </div>
                <div class="small text-muted">
                    <i class="bi bi-calendar-event me-1"></i><?= date('d/m/Y', strtotime($vuelo['fechaSalidaVuelo'])) ?> · <?= date('H:i', strtotime($vuelo['horaSalidaVuelo'])) ?> hs
                </div>
            </div>

            <!-- Desglose de precios -->
            <div class="border-top pt-3 mb-3">
                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Tarifa base (1 adulto)</span>
                    <span>$<?= number_format((float)$vuelo['precioVuelo'], 2, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between small text-danger mb-2">
                    <span>Descuento aplicado:</span>
                    <span id="display-descuento-monto">$0,00</span>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Tasas e impuestos</span>
                    <span class="text-success">Incluidos</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark">Total a pagar:</span>
                    <span class="fs-4 fw-bold text-primary" id="display-precio-total">
                        $<?= number_format((float)$vuelo['precioVuelo'], 2, ',', '.') ?>
                    </span>
                </div>
            </div>

            <div class="p-2 bg-warning bg-opacity-10 border border-warning rounded-2 small text-dark">
                <i class="bi bi-info-circle-fill text-warning me-1"></i>
                La reserva se emitirá como <strong>"pendiente de pago"</strong> y asegurará tu asiento de inmediato.
            </div>
        </aside>
    </div>
</div>
