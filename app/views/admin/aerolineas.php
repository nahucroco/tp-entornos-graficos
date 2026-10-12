<?php
declare(strict_types=1);
require_once __DIR__ . '/../../helpers/datos.php';

if (!usuarioLogueado() || !esAdmin()) {
    header('Location: index.php?pagina=login');
    exit;
}

$aerolineas = obtenerAerolineas();
$db = Database::getConnection();
// Obtener usuarios con rol CEO o candidatos para asignar
$ceos = $db->query("SELECT codUsuario, nombreUsuario, emailUsuario FROM usuarios WHERE tipoUsuario = 'ceo de aerolínea'")->fetchAll();

$editarId = !empty($_GET['editar']) ? (int)$_GET['editar'] : 0;
$aeroEditar = $editarId ? obtenerAerolineaPorId($editarId) : null;
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=admin_panel">Administración</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gestión de Aerolíneas (ABMC)</li>
    </ol>
</nav>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">
            <i class="bi bi-buildings-fill text-primary me-2"></i>Gestión de Aerolíneas (ABMC)
        </h1>
        <p class="text-muted small mb-0">Alta, modificación y baja de compañías aéreas asociadas y asignación de CEOs responsables.</p>
    </div>
    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formAeroCollapse" aria-expanded="<?= $aeroEditar ? 'true' : 'false' ?>">
        <i class="bi bi-plus-lg me-1"></i><?= $aeroEditar ? 'Editar ' . htmlspecialchars($aeroEditar['nombreAerolinea']) : 'Nueva Aerolínea' ?>
    </button>
</div>

<!-- FORMULARIO DE ALTA / MODIFICACIÓN DE AEROLÍNEA -->
<div class="collapse mb-4 <?= $aeroEditar ? 'show' : '' ?>" id="formAeroCollapse">
    <div class="card shadow-sm border-0 rounded-4 p-4 bg-white border-primary border-top border-4">
        <h2 class="h5 fw-bold mb-3">
            <?= $aeroEditar ? 'Modificar Aerolínea #' . $aeroEditar['codAerolinea'] : 'Registrar Nueva Aerolínea' ?>
        </h2>

        <form method="post" action="index.php" class="needs-validation" novalidate data-validar>
            <input type="hidden" name="accion" value="<?= $aeroEditar ? 'modificar_aerolinea' : 'crear_aerolinea' ?>">
            <?php if ($aeroEditar): ?>
                <input type="hidden" name="aerolinea_id" value="<?= $aeroEditar['codAerolinea'] ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="a-nombre" class="form-label small fw-bold">Nombre de la Aerolínea <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="a-nombre" name="nombre" value="<?= htmlspecialchars($aeroEditar['nombreAerolinea'] ?? '') ?>" required placeholder="Ej: Austral Líneas Aéreas">
                </div>
                <div class="col-md-3">
                    <label for="a-iata" class="form-label small fw-bold">Código IATA (3 letras) <span class="text-danger">*</span></label>
                    <input type="text" maxlength="3" class="form-control text-uppercase" id="a-iata" name="codigo_iata" value="<?= htmlspecialchars($aeroEditar['codigoIATA'] ?? '') ?>" required placeholder="Ej: AUS">
                </div>
                <div class="col-md-3">
                    <label for="a-pais" class="form-label small fw-bold">Código País (ISO 3) <span class="text-danger">*</span></label>
                    <input type="text" maxlength="3" class="form-control text-uppercase" id="a-pais" name="cod_pais" value="<?= htmlspecialchars($aeroEditar['codPais'] ?? 'ARG') ?>" required placeholder="ARG">
                </div>
                <div class="col-md-6">
                    <label for="a-ceo" class="form-label small fw-bold">Asignar CEO de Aerolínea</label>
                    <select class="form-select" id="a-ceo" name="ceo_id">
                        <option value="">Sin asignar / Pendiente</option>
                        <?php foreach ($ceos as $c): ?>
                            <option value="<?= $c['codUsuario'] ?>" <?= ($aeroEditar && (int)$aeroEditar['codUsuarioCeo'] === (int)$c['codUsuario']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nombreUsuario']) ?> (<?= htmlspecialchars($c['emailUsuario']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="a-desc" class="form-label small fw-bold">Descripción Institucional</label>
                    <input type="text" class="form-control" id="a-desc" name="descripcion" value="<?= htmlspecialchars($aeroEditar['descripcionAerolinea'] ?? '') ?>" placeholder="Breve descripción comercial">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <?php if ($aeroEditar): ?>
                    <a href="index.php?pagina=admin_aerolineas" class="btn btn-outline-secondary btn-sm">Cancelar Edición</a>
                <?php else: ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#formAeroCollapse">Cerrar</button>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-sm px-4">
                    <i class="bi bi-save me-1"></i><?= $aeroEditar ? 'Guardar Cambios' : 'Registrar Aerolínea' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TABLA DE AEROLÍNEAS -->
<div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 fw-bold mb-0">Aerolíneas en el Sistema</h2>
        <span class="badge bg-secondary"><?= count($aerolineas) ?> registrada(s)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Cód.</th>
                    <th>Nombre</th>
                    <th>IATA</th>
                    <th>País</th>
                    <th>CEO Responsable</th>
                    <th>Vuelos</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($aerolineas as $a): ?>
                    <tr>
                        <td><strong>#<?= $a['codAerolinea'] ?></strong></td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($a['nombreAerolinea']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($a['codigoIATA']) ?></span></td>
                        <td><?= htmlspecialchars($a['codPais']) ?></td>
                        <td class="small">
                            <?= !empty($a['nombreCeo']) ? '<i class="bi bi-person-check text-success me-1"></i>' . htmlspecialchars($a['nombreCeo']) : '<span class="text-muted">Sin asignar</span>' ?>
                        </td>
                        <td><span class="badge bg-info bg-opacity-10 text-info fw-bold"><?= (int)$a['totalVuelos'] ?></span></td>
                        <td class="text-end">
                            <a href="index.php?pagina=admin_aerolineas&editar=<?= $a['codAerolinea'] ?>" class="btn btn-sm btn-outline-primary" title="Modificar">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form method="post" action="index.php" class="d-inline" onsubmit="return confirm('¿Seguro que deseás eliminar la aerolínea <?= htmlspecialchars($a['nombreAerolinea']) ?>?');">
                                <input type="hidden" name="accion" value="eliminar_aerolinea">
                                <input type="hidden" name="aerolinea_id" value="<?= $a['codAerolinea'] ?>">
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
