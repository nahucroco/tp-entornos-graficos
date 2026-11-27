<?php
declare(strict_types=1);
require_once __DIR__ . '/../../helpers/datos.php';

if (!usuarioLogueado() || !esCeo()) {
    header('Location: index.php?pagina=login');
    exit;
}

$usuario = obtenerUsuario();
$metricas = obtenerMetricasCeo((int)$usuario['id']);
$aerolinea = $metricas['aerolinea'];

// Filtros y orden del listado (por defecto: últimos vuelos cargados primero)
$filtros = [
    'buscar' => trim((string)($_GET['buscar'] ?? '')),
    'estado' => in_array($_GET['estado'] ?? '', ['proximos', 'partidos'], true) ? $_GET['estado'] : 'todos',
    'desde'  => (string)($_GET['desde'] ?? ''),
    'hasta'  => (string)($_GET['hasta'] ?? ''),
];
$orden = array_key_exists($_GET['orden'] ?? '', ORDENES_VUELOS_CEO) ? $_GET['orden'] : 'recientes';
$hayFiltros = $filtros['buscar'] !== '' || $filtros['estado'] !== 'todos' || $filtros['desde'] !== '' || $filtros['hasta'] !== '' || $orden !== 'recientes';

$paginaActual = max(1, (int)($_GET['p'] ?? 1));
$porPagina = 5;
$totalVuelosCeo = contarTotalVuelosPorCeo((int)$usuario['id'], $filtros);
$totalPaginas = (int)ceil($totalVuelosCeo / $porPagina);
$offset = ($paginaActual - 1) * $porPagina;

$vuelos = obtenerVuelosPorCeo((int)$usuario['id'], $porPagina, $offset, $filtros, $orden);

$editarId = !empty($_GET['editar']) ? (int)$_GET['editar'] : 0;
$vueloEditar = $editarId ? obtenerVueloPorId($editarId) : null;
?>

<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?pagina=ceo_panel">Panel CEO</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gestión de Vuelos (ABMC)</li>
    </ol>
</nav>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">
            <i class="bi bi-airplane-engines-fill text-primary me-2"></i>Gestión de Vuelos de <?= htmlspecialchars($aerolinea['nombreAerolinea'] ?? 'Mi Aerolínea') ?>
        </h1>
        <p class="text-muted small mb-0">Alta, modificación y baja de itinerarios, tarifas y disponibilidad de asientos.</p>
    </div>
    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formVueloCollapse" aria-expanded="<?= $vueloEditar ? 'true' : 'false' ?>">
        <i class="bi bi-plus-lg me-1"></i><?= $vueloEditar ? 'Editar Vuelo #' . $vueloEditar['codVuelo'] : 'Crear Nuevo Vuelo' ?>
    </button>
</div>

<!-- FORMULARIO DE ALTA / MODIFICACIÓN (COLAPSABLE / MODAL SEGUN WIREFRAME) -->
<div class="collapse mb-4 <?= $vueloEditar ? 'show' : '' ?>" id="formVueloCollapse">
    <div class="card shadow-sm border-0 rounded-4 p-4 bg-white border-primary border-top border-4">
        <h2 class="h5 fw-bold mb-3">
            <?= $vueloEditar ? 'Modificar Vuelo #' . $vueloEditar['codVuelo'] : 'Registrar Nuevo Vuelo Comercial' ?>
        </h2>

        <form method="post" action="index.php" class="needs-validation" novalidate data-validar>
            <input type="hidden" name="accion" value="<?= $vueloEditar ? 'modificar_vuelo' : 'crear_vuelo' ?>">
            <?= campoCsrf() ?>
            <?php if ($vueloEditar): ?>
                <input type="hidden" name="vuelo_id" value="<?= $vueloEditar['codVuelo'] ?>">
            <?php endif; ?>
            <input type="hidden" name="aerolinea_id" value="<?= $aerolinea['codAerolinea'] ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="v-origen" class="form-label small fw-bold">Origen (Aeropuerto / Ciudad) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="v-origen" data-zod="ciudadOrigen" name="origen" value="<?= htmlspecialchars($vueloEditar['origenVuelo'] ?? '') ?>" required placeholder="Ej. Rosario (ROS)">
                </div>
                <div class="col-md-6">
                    <label for="v-destino" class="form-label small fw-bold">Destino (Aeropuerto / Ciudad) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="v-destino" data-zod="ciudadDestino" data-zod-distinto-de="origen" name="destino" value="<?= htmlspecialchars($vueloEditar['destinoVuelo'] ?? '') ?>" required placeholder="Ej. Bariloche (BRC)">
                </div>
                <div class="col-md-3">
                    <label for="v-fecha" class="form-label small fw-bold">Fecha de Salida <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="v-fecha" name="fecha" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($vueloEditar['fechaSalidaVuelo'] ?? date('Y-m-d', strtotime('+3 days'))) ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="v-hora" class="form-label small fw-bold">Hora de Salida <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" id="v-hora" name="hora" value="<?= htmlspecialchars($vueloEditar['horaSalidaVuelo'] ?? '10:00') ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="v-precio" class="form-label small fw-bold">Precio / Tarifa (ARS) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="1000" class="form-control" id="v-precio" data-zod="precioVuelo" name="precio" value="<?= htmlspecialchars((string)($vueloEditar['precioVuelo'] ?? '50000')) ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="v-asientos" class="form-label small fw-bold">Asientos Disponibles <span class="text-danger">*</span></label>
                    <input type="number" min="1" max="500" class="form-control" id="v-asientos" data-zod="asientos" name="asientos" value="<?= htmlspecialchars((string)($vueloEditar['asientosDisponibles'] ?? '40')) ?>" required>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <?php if ($vueloEditar): ?>
                    <a href="index.php?pagina=ceo_vuelos" class="btn btn-outline-secondary btn-sm">Cancelar Edición</a>
                <?php else: ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#formVueloCollapse">Cerrar</button>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-sm px-4">
                    <i class="bi bi-save me-1"></i><?= $vueloEditar ? 'Guardar Cambios' : 'Registrar Vuelo' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- LISTADO DE REGISTROS SEGUN WIREFRAME PAGINA_GESTION -->
<div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 fw-bold mb-0">Listado de Vuelos Registrados</h2>
        <span class="badge bg-secondary"><?= $totalVuelosCeo ?> vuelo(s)</span>
    </div>

    <!-- Filtros y orden del listado -->
    <form method="get" action="index.php" class="row g-2 align-items-end mb-3" aria-label="Filtrar y ordenar vuelos">
        <input type="hidden" name="pagina" value="ceo_vuelos">
        <div class="col-12 col-md-4 col-lg-3">
            <label for="filtro-buscar" class="form-label small fw-bold mb-1">Buscar</label>
            <input type="search" class="form-control form-control-sm" id="filtro-buscar" name="buscar"
                   value="<?= htmlspecialchars($filtros['buscar']) ?>" placeholder="Origen, destino o código">
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <label for="filtro-estado" class="form-label small fw-bold mb-1">Vuelos</label>
            <select class="form-select form-select-sm" id="filtro-estado" name="estado">
                <option value="todos" <?= $filtros['estado'] === 'todos' ? 'selected' : '' ?>>Todos</option>
                <option value="proximos" <?= $filtros['estado'] === 'proximos' ? 'selected' : '' ?>>Próximos</option>
                <option value="partidos" <?= $filtros['estado'] === 'partidos' ? 'selected' : '' ?>>Ya partieron</option>
            </select>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <label for="filtro-desde" class="form-label small fw-bold mb-1">Salida desde</label>
            <input type="date" class="form-control form-control-sm" id="filtro-desde" name="desde" value="<?= htmlspecialchars($filtros['desde']) ?>">
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <label for="filtro-hasta" class="form-label small fw-bold mb-1">Salida hasta</label>
            <input type="date" class="form-control form-control-sm" id="filtro-hasta" name="hasta" value="<?= htmlspecialchars($filtros['hasta']) ?>">
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <label for="filtro-orden" class="form-label small fw-bold mb-1">Ordenar por</label>
            <select class="form-select form-select-sm" id="filtro-orden" name="orden">
                <?php foreach (ORDENES_VUELOS_CEO as $clave => [$etiqueta]): ?>
                    <option value="<?= $clave ?>" <?= $orden === $clave ? 'selected' : '' ?>><?= $etiqueta ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-4 col-lg-1 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-fill">Aplicar</button>
            <?php if ($hayFiltros): ?>
                <a href="index.php?pagina=ceo_vuelos" class="btn btn-sm btn-outline-secondary" title="Quitar filtros" aria-label="Quitar filtros"><i class="bi bi-x-lg" aria-hidden="true"></i></a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (!empty($vuelos)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Código</th>
                        <th scope="col">Origen → Destino</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Hora</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Disponibilidad</th>
                        <th scope="col">Reservas</th>
                        <th scope="col" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vuelos as $v): ?>
                        <tr>
                            <td><strong>#<?= $v['codVuelo'] ?></strong></td>
                            <td class="fw-semibold"><?= htmlspecialchars($v['origenVuelo']) ?> → <?= htmlspecialchars($v['destinoVuelo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($v['fechaSalidaVuelo'])) ?></td>
                            <td><?= date('H:i', strtotime($v['horaSalidaVuelo'])) ?> hs</td>
                            <td class="fw-bold text-primary">$<?= number_format((float)$v['precioVuelo'], 2, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= $v['asientosDisponibles'] ?> asientos</span>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info fw-bold"><?= (int)$v['reservasRealizadas'] ?></span>
                            </td>
                            <td class="text-end">
                                <a href="index.php?pagina=ceo_vuelos&editar=<?= $v['codVuelo'] ?>" class="btn btn-sm btn-outline-primary" title="Modificar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form method="post" action="index.php" class="d-inline" onsubmit="return confirm('¿Seguro que deseás eliminar el vuelo #<?= $v['codVuelo'] ?>?');">
                                    <input type="hidden" name="accion" value="eliminar_vuelo">
            <?= campoCsrf() ?>
                                    <input type="hidden" name="vuelo_id" value="<?= $v['codVuelo'] ?>">
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

        <!-- Paginación Numérica -->
        <?= renderizarPaginador($paginaActual, $totalPaginas, 'ceo_vuelos', array_filter($filtros + ['orden' => $orden], fn($v) => $v !== '' && $v !== 'todos' && $v !== 'recientes')) ?>
    <?php elseif ($hayFiltros): ?>
        <div class="p-4 text-center text-muted">
            <i class="bi bi-search display-6 mb-2"></i>
            <p class="mb-2">No hay vuelos que coincidan con los filtros elegidos.</p>
            <a href="index.php?pagina=ceo_vuelos" class="btn btn-sm btn-outline-primary">Quitar filtros</a>
        </div>
    <?php else: ?>
        <div class="p-4 text-center text-muted">
            <i class="bi bi-airplane display-4 mb-2"></i>
            <p class="mb-0">Tu aerolínea aún no tiene vuelos programados. Usá el botón "Crear Nuevo Vuelo" para dar de alta el primero.</p>
        </div>
    <?php endif; ?>
</div>
