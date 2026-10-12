<?php
declare(strict_types=1);
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Contacto y Soporte</li>
    </ol>
</nav>

<div class="row g-4">
    <!-- FORMULARIO DE CONTACTO -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
            <h1 class="h4 fw-bold mb-2">Canal de Contacto y Atención al Cliente</h1>
            <p class="text-muted small mb-4">
                Comunicate con nuestro equipo de atención las 24 horas por consultas de vuelos, reservas o reclamos.
            </p>

            <form method="post" action="index.php" class="needs-validation" novalidate data-validar>
                <input type="hidden" name="accion" value="contacto">
            <?= campoCsrf() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="contacto-nombre" class="form-label small fw-bold">Nombre completo</label>
                        <input type="text" class="form-control" id="contacto-nombre" name="nombre" required placeholder="Tu nombre">
                    </div>
                    <div class="col-md-6">
                        <label for="contacto-email" class="form-label small fw-bold">Correo electrónico</label>
                        <input type="email" class="form-control" id="contacto-email" name="email" required placeholder="tu@email.com">
                    </div>
                    <div class="col-12">
                        <label for="contacto-motivo" class="form-label small fw-bold">Motivo de contacto</label>
                        <select class="form-select" id="contacto-motivo" name="motivo" required>
                            <option value="">Seleccioná un motivo...</option>
                            <option value="consulta">Consulta sobre vuelos y tarifas</option>
                            <option value="reserva">Dudas sobre reserva o confirmación</option>
                            <option value="cancelacion">Cancelación / Devolución</option>
                            <option value="aerolinea">Consultas institucionales de aerolíneas</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="contacto-mensaje" class="form-label small fw-bold">Mensaje o consulta</label>
                        <textarea class="form-control" id="contacto-mensaje" name="mensaje" rows="4" required placeholder="Escribí tu consulta detalladamente..."></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-despegar mt-4 px-4">
                    <i class="bi bi-send-fill me-1"></i>Enviar mensaje
                </button>
            </form>
        </div>
    </div>

    <!-- PREGUNTAS FRECUENTES (FAQ) -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-3">
            <h2 class="h5 fw-bold mb-3"><i class="bi bi-question-circle-fill text-primary me-2"></i>Preguntas Frecuentes</h2>

            <div class="accordion accordion-flush" id="faqAccordion">
                <div class="accordion-item">
                    <h3 class="accordion-header" id="faq1">
                        <button class="accordion-button collapsed px-0 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                            ¿Cómo cancelo una reserva?
                        </button>
                    </h3>
                    <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body px-0 small text-muted">
                            Podés cancelar tu reserva desde la sección "Mis Reservas" hasta 72 horas antes de la salida del vuelo programado. El asiento se libera automáticamente.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header" id="faq2">
                        <button class="accordion-button collapsed px-0 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                            ¿Cuándo se considera una compra?
                        </button>
                    </h3>
                    <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body px-0 small text-muted">
                            Una reserva nace como "pendiente de pago". Una vez confirmado el pago, pasa al estado "confirmada" y se emite el pasaje electrónico.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header" id="faq3">
                        <button class="accordion-button collapsed px-0 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                            ¿Cómo aplica un CEO de aerolínea?
                        </button>
                    </h3>
                    <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body px-0 small text-muted">
                            Los representantes de aerolíneas se registran con el perfil "CEO de Aerolínea" y su cuenta es autorizada por el Administrador General antes de operar.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-3 rounded-4 bg-light border-0">
            <h4 class="h6 fw-bold mb-1"><i class="bi bi-telephone-fill text-success me-2"></i>Línea Telefónica</h4>
            <p class="small text-muted mb-0">0810-555-CONDOR (2663) · Lunes a Viernes de 08:00 a 20:00 hs.</p>
        </div>
    </div>
</div>
