<?php
function usuarioLogueado(): bool
{
    return isset($_SESSION['usuario']);
}

function iniciarSesion(string $nombre, string $rol): void
{
    $rolesPermitidos = ['pasajero', 'ceo', 'admin'];
    $_SESSION['usuario'] = [
        'nombre' => $nombre !== '' ? $nombre : 'Pasajero',
        'rol' => in_array($rol, $rolesPermitidos, true) ? $rol : 'pasajero',
    ];
}

function cerrarSesion(): void
{
    $_SESSION = [];
    session_destroy();
}
