<?php
declare(strict_types=1);
require_once __DIR__ . '/../../helpers/datos.php';

if (!usuarioLogueado() || !esAdmin()) {
    header('Location: index.php?pagina=login');
    exit;
}

$paginaActual = max(1, (int)($_GET['p'] ?? 1));
$porPagina = 5;
$totalUsuarios = contarTotalUsuarios();
$totalPaginas = (int)ceil($totalUsuarios / $porPagina);
$offset = ($paginaActual - 1) * $porPagina;

$usuarios = obtenerUsuariosPaginados($porPagina, $offset);
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=admin_panel">Administración</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gestión de Usuarios</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">
            <i class="bi bi-people-fill text-primary me-2"></i>Gestión de Cuentas de Usuario
        </h1>
        <p class="text-muted small mb-0">Control de perfiles, aprobación de registros de CEOs de aerolíneas y verificación de estados.</p>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Cód.</th>
                    <th>Nombre Completo</th>
                    <th>Correo Electrónico</th>
                    <th>Teléfono</th>
                    <th>Tipo / Rol</th>
                    <th>Estado de Cuenta</th>
                    <th class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><strong>#<?= $u['codUsuario'] ?></strong></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($u['nombreUsuario']) ?></td>
                        <td><?= htmlspecialchars($u['emailUsuario']) ?></td>
                        <td><?= htmlspecialchars($u['telefonoUsuario'] ?: '-') ?></td>
                        <td>
                            <?php if ($u['tipoUsuario'] === 'administrador'): ?>
                                <span class="badge bg-danger">Administrador</span>
                            <?php elseif ($u['tipoUsuario'] === 'ceo de aerolínea'): ?>
                                <span class="badge bg-warning text-dark">CEO Aerolínea</span>
                            <?php else: ?>
                                <span class="badge bg-primary">Pasajero</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['estadoUsuario'] === 'activo'): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activo</span>
                            <?php elseif ($u['estadoUsuario'] === 'denegado'): ?>
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Denegado</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($u['tipoUsuario'] === 'ceo de aerolínea' && $u['estadoUsuario'] === 'pendiente'): ?>
                                <form method="post" action="index.php" class="d-inline">
                                    <input type="hidden" name="accion" value="cambiar_estado_usuario">
            <?= campoCsrf() ?>
                                    <input type="hidden" name="usuario_id" value="<?= $u['codUsuario'] ?>">
                                    <input type="hidden" name="nuevo_estado" value="activo">
                                    <button type="submit" class="btn btn-sm btn-success" title="Aprobar registro de CEO">
                                        <i class="bi bi-check-lg me-1"></i>Aprobar CEO
                                    </button>
                                </form>
                            <?php elseif ($u['codUsuario'] !== 1): ?>
                                <form method="post" action="index.php" class="d-inline">
                                    <input type="hidden" name="accion" value="cambiar_estado_usuario">
            <?= campoCsrf() ?>
                                    <input type="hidden" name="usuario_id" value="<?= $u['codUsuario'] ?>">
                                    <input type="hidden" name="nuevo_estado" value="<?= $u['estadoUsuario'] === 'activo' ? 'denegado' : 'activo' ?>">
                                    <button type="submit" class="btn btn-sm <?= $u['estadoUsuario'] === 'activo' ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                        <?= $u['estadoUsuario'] === 'activo' ? 'Suspender' : 'Reactivar' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación Numérica -->
    <?= renderizarPaginador($paginaActual, $totalPaginas, 'admin_usuarios') ?>
</div>
