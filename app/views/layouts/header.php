<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AeroReserva | TP Entornos Gráficos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/estilos.css" rel="stylesheet">
</head>
<body>
<a class="visually-hidden-focusable skip-link" href="#contenido">Saltar al contenido principal</a>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary" aria-label="Navegación principal">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">AeroReserva</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?pagina=vuelos">Vuelos</a></li>
                    <?php if (usuarioLogueado()): ?>
                        <li class="nav-item"><a class="nav-link" href="index.php?pagina=reservas">Mis reservas</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php?pagina=panel">Panel</a></li>
                        <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-lg-2" href="index.php?salir=1">Salir</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="index.php?pagina=registro">Registrarse</a></li>
                        <li class="nav-item"><a class="btn btn-light btn-sm text-primary ms-lg-2" href="index.php?pagina=login">Ingresar</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main id="contenido" class="py-4">
    <div class="container">
        <?php if ($mensaje): ?><div class="alert alert-success" role="alert"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
