<h1 class="mb-4">Buscar vuelos</h1>
<form class="card card-body shadow-sm mb-4" method="get" action="index.php">
    <input type="hidden" name="pagina" value="vuelos">
    <div class="row g-3 align-items-end">
        <div class="col-md-4"><label class="form-label" for="origen">Origen</label><input class="form-control" id="origen" name="origen" placeholder="Ej. Rosario"></div>
        <div class="col-md-4"><label class="form-label" for="destino">Destino</label><input class="form-control" id="destino" name="destino" placeholder="Ej. Córdoba"></div>
        <div class="col-md-3"><label class="form-label" for="fecha">Fecha</label><input class="form-control" id="fecha" name="fecha" type="date"></div>
        <div class="col-md-1"><button class="btn btn-primary w-100" type="submit">Ir</button></div>
    </div>
</form>
<p class="text-muted">Mostrando <?= count($vuelos) ?> vuelos de ejemplo.</p>
<div class="row g-4">
<?php foreach ($vuelos as $vuelo): ?>
    <div class="col-md-6 col-lg-4"><article class="card h-100 shadow-sm"><div class="card-body d-flex flex-column">
        <span class="badge text-bg-light align-self-start mb-2"><?= htmlspecialchars($vuelo['aerolinea']) ?></span>
        <h2 class="h5"><?= htmlspecialchars($vuelo['origen']) ?> <span aria-hidden="true">→</span> <?= htmlspecialchars($vuelo['destino']) ?></h2>
        <p><?= $vuelo['fecha'] ?> · <?= $vuelo['hora'] ?><br><small><?= $vuelo['asientos'] ?> asientos disponibles</small></p>
        <p class="fs-4 fw-bold mt-auto">$<?= number_format($vuelo['precio'], 0, ',', '.') ?></p>
        <form method="post"><input type="hidden" name="accion" value="reserva"><input type="hidden" name="vuelo" value="<?= $vuelo['codigo'] ?>"><button class="btn btn-outline-primary w-100">Reservar</button></form>
    </div></article></div>
<?php endforeach; ?>
</div>
