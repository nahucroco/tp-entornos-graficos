<?php
declare(strict_types=1);
?>

<div class="row justify-content-center py-4">
    <div class="col-md-7 col-lg-5">
        <div class="card shadow-sm border-0 rounded-4 p-4 p-md-5 bg-white">
            <div class="text-center mb-4">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-key-fill fs-2 text-warning"></i>
                </div>
                <h1 class="h4 fw-bold mb-1">Recuperar Contraseña</h1>
                <p class="text-muted small">
                    Ingresá el correo electrónico asociado a tu cuenta para enviarte un enlace de recuperación seguro.
                </p>
            </div>

            <form method="post" action="index.php" class="needs-validation" novalidate data-validar>
                <input type="hidden" name="accion" value="recuperar_clave">

                <div class="mb-4">
                    <label for="recuperar-email" class="form-label small fw-bold">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="recuperar-email" name="email" required placeholder="tu@email.com">
                    </div>
                </div>

                <button type="submit" class="btn btn-despegar w-100 py-2 shadow-sm mb-3">
                    <i class="bi bi-send-fill me-1"></i>Enviar enlace de recuperación
                </button>

                <div class="text-center small">
                    <a href="index.php?pagina=login" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Volver al inicio de sesión
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
