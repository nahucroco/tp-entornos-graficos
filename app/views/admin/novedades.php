<?php
declare(strict_types=1);
require_once __DIR__ . '/../../helpers/datos.php';

if (!usuarioLogueado() || !esAdmin()) {
    header('Location: index.php?pagina=login');
    exit;
}

$novedades = obtenerTodasNovedades();
$db = Database::getConnection();

$editarId = !empty($_GET['editar']) ? (int)$_GET['editar'] : 0;
$novedadEditar = null;
if ($editarId) {
    $stmt = $db->prepare("SELECT * FROM novedades WHERE codNovedad = ? LIMIT 1");
    $stmt->execute([$editarId]);
    $novedadEditar = $stmt->fetch();
}
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=admin_panel">Administración</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gestión de Novedades (ABMC)</li>
    </ol>
</nav>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">
            <i class="bi bi-megaphone-fill text-info me-2"></i>Gestión de Novedades (ABMC)
        </h1>
        <p class="text-muted small mb-0">Publicación de comunicados oficiales, avisos de aeropuertos y alertas operativas.</p>
    </div>
    <button class="btn btn-info btn-sm text-dark fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#formNovedadCollapse" aria-expanded="<?= $novedadEditar ? 'true' : 'false' ?>">
        <i class="bi bi-plus-lg me-1"></i><?= $novedadEditar ? 'Modificar Novedad #' . $novedadEditar['codNovedad'] : 'Nueva Novedad' ?>
    </button>
</div>

<!-- FORMULARIO DE ALTA / MODIFICACIÓN DE NOVEDAD -->
<div class="collapse mb-4 <?= $novedadEditar ? 'show' : '' ?>" id="formNovedadCollapse">
    <div class="card shadow-sm border-0 rounded-4 p-4 bg-white border-info border-top border-4">
        <h2 class="h5 fw-bold mb-3">
            <?= $novedadEditar ? 'Editar Novedad #' . $novedadEditar['codNovedad'] : 'Publicar Nuevo Comunicado Oficial' ?>
        </h2>

        <form method="post" action="index.php" class="needs-validation" novalidate data-validar>
            <input type="hidden" name="accion" value="<?= $novedadEditar ? 'modificar_novedad' : 'crear_novedad' ?>">
            <?php if ($novedadEditar): ?>
                <input type="hidden" name="novedad_id" value="<?= $novedadEditar['codNovedad'] ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-12">
                    <label for="nov-texto" class="form-label small fw-bold">Texto del Comunicado / Anuncio <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="nov-texto" name="texto" rows="3" required placeholder="Escribí aquí el mensaje oficial para los pasajeros..."><?= htmlspecialchars($novedadEditar['textoNovedad'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label for="nov-pub" class="form-label small fw-bold">Fecha de Publicación <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="nov-pub" name="fecha_publicacion" value="<?= htmlspecialchars($novedadEditar['fechaPublicacionNovedad'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="nov-exp" class="form-label small fw-bold">Fecha de Expiración / Vencimiento <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="nov-exp" name="fecha_expiracion" value="<?= htmlspecialchars($novedadEditar['fechaExpiracionNovedad'] ?? date('Y-m-d', strtotime('+30 days'))) ?>" required>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <?php if ($novedadEditar): ?>
                    <a href="index.php?pagina=admin_novedades" class="btn btn-outline-secondary btn-sm">Cancelar Edición</a>
                <?php else: ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#formNovedadCollapse">Cerrar</button>
                <?php endif; ?>
                <button type="submit" class="btn btn-info btn-sm text-dark fw-bold px-4">
                    <i class="bi bi-save me-1"></i><?= $novedadEditar ? 'Guardar Cambios' : 'Publicar Anuncio' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- LISTADO DE NOVEDADES -->
<div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 fw-bold mb-0">Novedades Registradas en el Sistema</h2>
        <span class="badge bg-secondary"><?= count($novedades) ?> anuncio(s)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Mensaje / Comunicado</th>
                    <th>Fecha Publicación</th>
                    <th>Fecha Expiración</th>
                    <th>Publicado Por</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($novedades as $nov): ?>
                    <tr>
                        <td><strong>NOV#<?= $nov['codNovedad'] ?></strong></td>
                        <td class="fw-semibold text-dark" style="max-width: 400px;"><?= htmlspecialchars($nov['textoNovedad']) ?></td>
                        <td><?= date('d/m/Y', strtotime($nov['fechaPublicacionNovedad'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($nov['fechaExpiracionNovedad'])) ?></td>
                        <td><small class="text-muted"><?= htmlspecialchars($nov['publicadoPor'] ?? 'Administrador') ?></small></td>
                        <td class="text-end">
                            <a href="index.php?pagina=admin_novedades&editar=<?= $nov['codNovedad'] ?>" class="btn btn-sm btn-outline-primary" title="Modificar">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form method="post" action="index.php" class="d-inline" onsubmit="return confirm('¿Seguro que deseás eliminar esta novedad?');">
                                <input type="hidden" name="accion" value="eliminar_novedad">
                                <input type="hidden" name="novedad_id" value="<?= $nov['codNovedad'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
