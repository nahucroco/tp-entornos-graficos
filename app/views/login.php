<?php
declare(strict_types=1);
$volver = (string)($_GET['volver'] ?? '');
$vueloId = (int)($_GET['vuelo'] ?? 0);

// Datos de la validación de correo del registro (se muestran una sola vez)
$enlaceValidacionDemo = (string)($_SESSION['enlace_validacion_demo'] ?? '');
$emailSinValidar = (string)($_SESSION['email_sin_validar'] ?? '');
unset($_SESSION['enlace_validacion_demo'], $_SESSION['email_sin_validar']);
?>

<div class="row justify-content-center py-4">
    <div class="col-md-8 col-lg-5">
        <div class="card shadow-sm border-0 rounded-4 p-4 p-md-5 bg-white">
            <!-- Encabezado estilo Despegar Pasaporte -->
            <div class="text-center mb-4">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-person-fill fs-2" style="color: var(--color-primario);"></i>
                </div>
                <h1 class="h4 fw-bold mb-1">Iniciá sesión en Cóndor</h1>
                <p class="text-muted small">Ingresá a tu cuenta para gestionar tus vuelos y reservas.</p>
            </div>

            <?php if ($enlaceValidacionDemo !== ''): ?>
                <div class="alert alert-info small" role="status">
                    <p class="fw-bold mb-1"><i class="bi bi-envelope-paper me-1" aria-hidden="true"></i>Modo demostración</p>
                    <p class="mb-2">El servidor de correo no está configurado, por lo que el mensaje quedó registrado en el log del sistema en lugar de enviarse. Podés validar tu registro desde acá:</p>
                    <a href="<?= htmlspecialchars($enlaceValidacionDemo) ?>" class="btn btn-sm btn-primary">Validar mi registro</a>
                </div>
            <?php endif; ?>

            <!-- Formulario estándar de Login -->
            <form method="post" action="index.php" class="needs-validation" novalidate data-validar>
                <input type="hidden" name="accion" value="login_credenciales">
            <?= campoCsrf() ?>
                <?php if ($volver): ?>
                    <input type="hidden" name="volver" value="<?= htmlspecialchars($volver) ?>">
                    <input type="hidden" name="vuelo_id" value="<?= $vueloId ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="login-email" class="form-label small fw-bold">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="login-email" data-zod="email" name="email" required placeholder="nombre@correo.com">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="login-clave" class="form-label small fw-bold mb-0">Contraseña</label>
                        <a href="index.php?pagina=recuperar_clave" class="small text-decoration-none">¿Olvidaste tu clave?</a>
                    </div>
                    <div class="input-group mt-1">
                        <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="login-clave" name="clave" required placeholder="••••••••">
                        <button class="btn btn-outline-secondary" type="button" data-toggle-password="login-clave" aria-label="Mostrar contraseña" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-despegar w-100 py-2 shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Ingresar a mi cuenta
                </button>
            </form>

            <details class="small mt-3 mb-3"<?= $emailSinValidar !== '' ? ' open' : '' ?>>
                <summary class="text-primary">¿No te llegó el correo para validar tu registro?</summary>
                <form method="post" action="index.php" class="needs-validation mt-2" novalidate data-validar>
                    <input type="hidden" name="accion" value="reenviar_validacion">
                    <?= campoCsrf() ?>
                    <label for="reenviar-email" class="form-label small fw-bold">Correo electrónico con el que te registraste</label>
                    <div class="input-group">
                        <input type="email" class="form-control form-control-sm" id="reenviar-email" data-zod="email" name="email" required
                               value="<?= htmlspecialchars($emailSinValidar) ?>" placeholder="nombre@correo.com">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Reenviar enlace</button>
                    </div>
                </form>
            </details>

            <div class="text-center small text-muted">
                ¿No tenés una cuenta todavía? <a href="index.php?pagina=registro" class="fw-bold text-decoration-none">Registrate gratis</a>
            </div>
        </div>
    </div>
</div>
