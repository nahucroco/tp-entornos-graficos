<?php
declare(strict_types=1);
$volver = htmlspecialchars($_GET['volver'] ?? '');
$vueloId = (int)($_GET['vuelo'] ?? 0);
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

            <!-- Formulario estándar de Login -->
            <form method="post" action="index.php" class="needs-validation mb-4" novalidate data-validar>
                <input type="hidden" name="accion" value="login_credenciales">
                <?php if ($volver): ?>
                    <input type="hidden" name="volver" value="<?= $volver ?>">
                    <input type="hidden" name="vuelo_id" value="<?= $vueloId ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="login-email" class="form-label small fw-bold">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="login-email" name="email" required placeholder="nombre@correo.com">
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
                    </div>
                </div>

                <button type="submit" class="btn btn-despegar w-100 py-2 shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Ingresar a mi cuenta
                </button>
            </form>

            <div class="text-center small text-muted mb-4">
                ¿No tenés una cuenta todavía? <a href="index.php?pagina=registro" class="fw-bold text-decoration-none">Registrate gratis</a>
            </div>

            <!-- CAJA DE DEMOSTRACIÓN RÁPIDA (PARA LA CÁTEDRA) -->
            <div class="p-3 bg-light rounded-3 border">
                <div class="fw-bold small text-dark mb-2 text-center">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Acceso Rápido de Evaluación
                </div>
                <div class="d-grid gap-2">
                    <a href="index.php?demo=pasajero<?= $volver ? "&volver=$volver&vuelo=$vueloId" : '' ?>" class="btn btn-outline-primary btn-sm text-start d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-person me-2"></i>Pasajero (Mariana)</span>
                        <span class="badge bg-primary">usuario</span>
                    </a>
                    <a href="index.php?demo=ceo" class="btn btn-outline-warning text-dark btn-sm text-start d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-briefcase me-2"></i>CEO Aerolínea (Carlos)</span>
                        <span class="badge bg-warning text-dark">ceo</span>
                    </a>
                    <a href="index.php?demo=admin" class="btn btn-outline-danger btn-sm text-start d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-shield-lock me-2"></i>Administrador General</span>
                        <span class="badge bg-danger">admin</span>
                    </a>
                </div>
                <div class="text-center mt-2" style="font-size: 0.75rem;">
                    Claves para login manual: <code>admin123</code> / <code>ceo123</code> / <code>pasajero123</code>
                </div>
            </div>
        </div>
    </div>
</div>
