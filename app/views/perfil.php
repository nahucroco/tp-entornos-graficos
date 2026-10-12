<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/datos.php';

if (!usuarioLogueado()) {
    header('Location: index.php?pagina=login&volver=perfil');
    exit;
}

$usuario = obtenerUsuario();
?>

<!-- BREADCRUMBS -->
<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Mi Perfil</li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white text-center mb-3">
            <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="bi bi-person-circle display-4" style="color: var(--color-primario);"></i>
            </div>
            <h1 class="h5 fw-bold mb-1"><?= htmlspecialchars($usuario['nombre']) ?></h1>
            <p class="text-muted small mb-2"><?= htmlspecialchars($usuario['email']) ?></p>
            <div>
                <span class="badge bg-primary text-uppercase px-3 py-1"><?= htmlspecialchars($usuario['rol']) ?></span>
                <span class="badge bg-success ms-1"><i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($usuario['estado']) ?></span>
            </div>
        </div>

        <div class="list-group shadow-sm border-0 rounded-4 overflow-hidden">
            <a href="index.php?pagina=perfil" class="list-group-item list-group-item-action active"><i class="bi bi-person-gear me-2"></i>Datos Personales</a>
            <?php if (esPasajero()): ?>
                <a href="index.php?pagina=reservas" class="list-group-item list-group-item-action"><i class="bi bi-ticket-perforated me-2"></i>Mis Reservas Pendientes</a>
                <a href="index.php?pagina=historial" class="list-group-item list-group-item-action"><i class="bi bi-clock-history me-2"></i>Historial de Compras</a>
            <?php elseif (esCeo()): ?>
                <a href="index.php?pagina=ceo_panel" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i>Panel de Aerolínea</a>
            <?php elseif (esAdmin()): ?>
                <a href="index.php?pagina=admin_panel" class="list-group-item list-group-item-action"><i class="bi bi-shield-lock me-2"></i>Panel de Administración</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
            <h2 class="h5 fw-bold mb-3 border-bottom pb-2">Actualizar Información Personal</h2>
            
            <form method="post" action="index.php" class="needs-validation" novalidate data-validar>
                <input type="hidden" name="accion" value="actualizar_perfil">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="perfil-nombre" class="form-label small fw-bold">Nombre completo</label>
                        <input type="text" class="form-control" id="perfil-nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="perfil-email" class="form-label small fw-bold">Correo Electrónico (No modificable)</label>
                        <input type="email" class="form-control bg-light" id="perfil-email" value="<?= htmlspecialchars($usuario['email']) ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="perfil-telefono" class="form-label small fw-bold">Teléfono de contacto</label>
                        <input type="tel" class="form-control" id="perfil-telefono" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>" placeholder="Ej: 341-555-0100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tipo de Perfil</label>
                        <input type="text" class="form-control bg-light text-capitalize" value="<?= htmlspecialchars($usuario['rol']) ?>" readonly>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-despegar px-4">
                        <i class="bi bi-save me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
