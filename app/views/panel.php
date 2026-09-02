<?php if (!usuarioLogueado()): header('Location: index.php?pagina=login'); exit; endif; ?>
<h1>Panel de <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></h1>
<?php $rol = $_SESSION['usuario']['rol']; ?>
<p class="lead">Rol activo: <strong><?= strtoupper(htmlspecialchars($rol)) ?></strong></p>
<div class="row g-3">
<?php if ($rol === 'admin'): ?>
    <div class="col-md-4"><div class="card card-body"><h2 class="h5">Aerolíneas</h2><p>Crear, editar y eliminar aerolíneas.</p><button class="btn btn-outline-primary">Gestionar</button></div></div>
    <div class="col-md-4"><div class="card card-body"><h2 class="h5">Promociones</h2><p>Aprobar o denegar promociones.</p><button class="btn btn-outline-primary">Revisar</button></div></div>
<?php elseif ($rol === 'ceo'): ?>
    <div class="col-md-4"><div class="card card-body"><h2 class="h5">Vuelos</h2><p>Administrar vuelos de mi aerolínea.</p><button class="btn btn-outline-primary">Gestionar</button></div></div>
    <div class="col-md-4"><div class="card card-body"><h2 class="h5">Promociones</h2><p>Crear promociones pendientes.</p><button class="btn btn-outline-primary">Crear</button></div></div>
<?php else: ?>
    <div class="col-md-4"><div class="card card-body"><h2 class="h5">Mis reservas</h2><p>Consultar, confirmar o cancelar.</p><a class="btn btn-outline-primary" href="index.php?pagina=reservas">Ver reservas</a></div></div>
    <div class="col-md-4"><div class="card card-body"><h2 class="h5">Mi perfil</h2><p>Actualizar datos personales.</p><button class="btn btn-outline-primary">Editar perfil</button></div></div>
<?php endif; ?>
</div>
