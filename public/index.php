<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../app/helpers/auth.php';

$vuelos = [
    ['codigo' => 101, 'aerolinea' => 'Aerolíneas TP', 'origen' => 'Rosario', 'destino' => 'Buenos Aires', 'fecha' => '15/10/2026', 'hora' => '08:30', 'precio' => 125000, 'asientos' => 18],
    ['codigo' => 102, 'aerolinea' => 'Aerolíneas TP', 'origen' => 'Rosario', 'destino' => 'Córdoba', 'fecha' => '16/10/2026', 'hora' => '14:15', 'precio' => 98000, 'asientos' => 7],
    ['codigo' => 103, 'aerolinea' => 'Vuela Fácil', 'origen' => 'Buenos Aires', 'destino' => 'Mendoza', 'fecha' => '18/10/2026', 'hora' => '10:45', 'precio' => 153000, 'asientos' => 24],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'login') {
        $rol = $_POST['rol'] ?? 'pasajero';
        iniciarSesion(trim($_POST['nombre'] ?? 'Pasajero'), $rol);
        $_SESSION['mensaje'] = 'Sesión iniciada correctamente (modo demostración).';
        header('Location: index.php?pagina=panel');
        exit;
    }

    if ($accion === 'reserva') {
        if (!usuarioLogueado()) {
            $_SESSION['mensaje'] = 'Para reservar primero tenés que iniciar sesión.';
            header('Location: index.php?pagina=login');
            exit;
        }
        $_SESSION['mensaje'] = 'Reserva del vuelo #' . (int) ($_POST['vuelo'] ?? 0) . ' creada con estado pendiente de pago.';
        header('Location: index.php?pagina=reservas');
        exit;
    }
}

if (isset($_GET['salir'])) {
    cerrarSesion();
    header('Location: index.php');
    exit;
}

$pagina = $_GET['pagina'] ?? 'inicio';
$mensaje = $_SESSION['mensaje'] ?? null;
unset($_SESSION['mensaje']);

require __DIR__ . '/../app/views/layouts/header.php';

switch ($pagina) {
    case 'vuelos':
        require __DIR__ . '/../app/views/vuelos.php';
        break;
    case 'login':
        require __DIR__ . '/../app/views/login.php';
        break;
    case 'registro':
        require __DIR__ . '/../app/views/registro.php';
        break;
    case 'panel':
        require __DIR__ . '/../app/views/panel.php';
        break;
    case 'reservas':
        require __DIR__ . '/../app/views/reservas.php';
        break;
    default:
        require __DIR__ . '/../app/views/inicio.php';
}

require __DIR__ . '/../app/views/layouts/footer.php';
