<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/datos.php';

if (!usuarioLogueado()) {
    header('Location: index.php?pagina=login');
    exit;
}

$reservaId = (int)($_GET['id'] ?? 0);
$reserva = obtenerReservaPorId($reservaId);
$usuario = obtenerUsuario();

if (!$reserva) {
    echo '<div class="alert alert-danger">La reserva solicitada no existe o no se encuentra disponible.</div>';
    echo '<a href="index.php?pagina=reservas" class="btn btn-primary">Volver a mis reservas</a>';
    return;
}

// Verificar que pertenezca al usuario (o admin/ceo)
if ($reserva['codUsuario'] !== $usuario['id'] && !esAdmin()) {
    echo '<div class="alert alert-danger">No tenés permisos para visualizar esta reserva.</div>';
    return;
}

$novedades = obtenerNovedadesVigentes();
?>

<!-- BREADCRUMBS -->
<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=reservas">Mis reservas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detalle de reserva #<?= $reserva['codReserva'] ?></li>
    </ol>
</nav>

<div class="row g-4">
    <!-- COLUMNA PRINCIPAL: DETALLE DE LA RESERVA -->
    <div class="col-lg-8">
        <article class="card shadow-sm border-0 rounded-4 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <span class="badge bg-primary mb-1"><?= htmlspecialchars($reserva['nombreAerolinea']) ?> (<?= htmlspecialchars($reserva['codigoIATA']) ?>)</span>
                    <h1 class="h4 fw-bold mb-0">Comprobante de Reserva #<?= $reserva['codReserva'] ?></h1>
                </div>
                <div>
                    <?php if ($reserva['estadoReserva'] === 'confirmada'): ?>
                        <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>Confirmada (Compra)</span>
                    <?php elseif ($reserva['estadoReserva'] === 'cancelada'): ?>
                        <span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i>Cancelada</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark fs-6"><i class="bi bi-clock me-1"></i>Pendiente de pago</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SECCIÓN 1: DATOS DE LA RESERVA -->
            <div class="mb-4">
                <h2 class="h6 text-uppercase fw-bold text-muted border-bottom pb-2 mb-3">Datos de la Reserva</h2>
                <div class="row g-2 small">
                    <div class="col-sm-6"><strong>Código de Reserva:</strong> #<?= $reserva['codReserva'] ?></div>
                    <div class="col-sm-6"><strong>Fecha de Emisión:</strong> <?= date('d/m/Y H:i', strtotime($reserva['fechaReserva'])) ?></div>
                    <div class="col-sm-6"><strong>Estado Actual:</strong> <span class="text-uppercase fw-bold"><?= htmlspecialchars($reserva['estadoReserva']) ?></span></div>
                    <div class="col-sm-6"><strong>Titular de la Cuenta:</strong> <?= htmlspecialchars($reserva['nombreUsuario']) ?></div>
                </div>
            </div>

            <!-- SECCIÓN 2: DATOS DEL VUELO -->
            <div class="mb-4">
                <h2 class="h6 text-uppercase fw-bold text-muted border-bottom pb-2 mb-3">Datos del Vuelo</h2>
                <div class="card bg-light border-0 p-3 mb-2">
                    <div class="row align-items-center text-center text-md-start g-2">
                        <div class="col-md-5">
                            <span class="small text-muted">Origen</span>
                            <div class="fs-5 fw-bold"><?= htmlspecialchars($reserva['origenVuelo']) ?></div>
                        </div>
                        <div class="col-md-2 text-center">
                            <i class="bi bi-arrow-right fs-4 text-primary"></i>
                        </div>
                        <div class="col-md-5 text-md-end">
                            <span class="small text-muted">Destino</span>
                            <div class="fs-5 fw-bold"><?= htmlspecialchars($reserva['destinoVuelo']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="row g-2 small">
                    <div class="col-sm-6"><strong>Vuelo Nro:</strong> #<?= $reserva['codVuelo'] ?></div>
                    <div class="col-sm-6"><strong>Fecha de Salida:</strong> <?= date('d/m/Y', strtotime($reserva['fechaSalidaVuelo'])) ?> a las <?= date('H:i', strtotime($reserva['horaSalidaVuelo'])) ?> hs</div>
                    <div class="col-sm-6"><strong>Compañía Operadora:</strong> <?= htmlspecialchars($reserva['nombreAerolinea']) ?></div>
                    <div class="col-sm-6"><strong>Asientos Disponibles en Vuelo:</strong> <?= (int)$reserva['asientosDisponibles'] ?></div>
                </div>
            </div>

            <!-- SECCIÓN 3: DATOS DEL PASAJERO -->
            <div class="mb-4">
                <h2 class="h6 text-uppercase fw-bold text-muted border-bottom pb-2 mb-3">Datos del Pasajero Titular</h2>
                <div class="row g-2 small">
                    <div class="col-sm-6"><strong>Nombre y Apellido:</strong> <?= htmlspecialchars($reserva['nombreUsuario']) ?></div>
                    <div class="col-sm-6"><strong>Código de Pasajero:</strong> #<?= $reserva['codUsuario'] ?></div>
                    <div class="col-sm-6"><strong>Correo de Contacto:</strong> <?= htmlspecialchars($reserva['emailUsuario']) ?></div>
                    <div class="col-sm-6"><strong>Teléfono:</strong> <?= htmlspecialchars($reserva['telefonoUsuario'] ?: 'No registrado') ?></div>
                </div>
            </div>

            <!-- SECCIÓN 4: DETALLE DE PAGO Y PROMOCIONES -->
            <div class="mb-4">
                <h2 class="h6 text-uppercase fw-bold text-muted border-bottom pb-2 mb-3">Detalle Financiero y Promociones</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <tbody>
                            <tr>
                                <td>Tarifa estándar del vuelo:</td>
                                <td class="text-end fw-semibold">$<?= number_format((float)$reserva['precioVuelo'], 2, ',', '.') ?></td>
                            </tr>
                            <?php if (!empty($reserva['descripcionPromocion'])): ?>
                                <tr class="text-success">
                                    <td>Promoción aplicada (<?= htmlspecialchars($reserva['descripcionPromocion']) ?>):</td>
                                    <td class="text-end fw-bold">-<?= (int)$reserva['descuentoPromocion'] ?>%</td>
                                </tr>
                            <?php endif; ?>
                            <tr class="table-active">
                                <td class="fw-bold fs-6">Precio Final Abonado / a Pagar:</td>
                                <td class="text-end fw-bold fs-5 text-primary">$<?= number_format((float)$reserva['precioFinal'], 2, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ACCIONES IMPRIMIR / VOLVER -->
            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                <a href="index.php?pagina=reservas" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Volver a reservas
                </a>
                <button type="button" class="btn btn-primary" onclick="window.print();">
                    <i class="bi bi-printer me-1"></i>Imprimir Comprobante
                </button>
            </div>
        </article>
    </div>

    <!-- COLUMNA LATERAL (SEGUN WIREFRAME): NOVEDADES Y CUENTA -->
    <aside class="col-lg-4">
        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white mb-3">
            <h2 class="h6 fw-bold mb-2"><i class="bi bi-shield-check text-success me-2"></i>Estado de la Reserva</h2>
            <p class="small text-muted mb-2">
                <?= $reserva['estadoReserva'] === 'confirmada' 
                    ? 'Esta reserva ya fue abonada y se encuentra confirmada como pasaje electrónico emitido.'
                    : 'Esta reserva se encuentra pendiente de pago. Recordá abonarla para asegurar tu viaje.' ?>
            </p>
            <div class="p-2 bg-light rounded small text-center">
                Código localizador: <strong class="text-primary">AR-<?= str_pad((string)$reserva['codReserva'], 6, '0', STR_PAD_LEFT) ?></strong>
            </div>
        </div>

        <div class="card p-3 rounded-4 shadow-sm border-0 bg-white">
            <h2 class="h6 fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Información Útil</h2>
            <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                <li><i class="bi bi-check2 text-success me-1"></i>Presentate 2 horas antes para vuelos de cabotaje.</li>
                <li><i class="bi bi-check2 text-success me-1"></i>Llevá tu DNI o pasaporte vigente.</li>
                <li><i class="bi bi-check2 text-success me-1"></i>Cancelaciones disponibles hasta 72hs antes.</li>
            </ul>
        </div>
    </aside>
</div>
