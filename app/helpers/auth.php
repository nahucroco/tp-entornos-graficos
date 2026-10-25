<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/csrf.php';

/**
 * Helpers de Autenticación y Gestión de Sesión.
 */

function usuarioLogueado(): bool
{
    return isset($_SESSION['usuario']) && is_array($_SESSION['usuario']);
}

function obtenerUsuario(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function obtenerRolUsuario(): string
{
    return $_SESSION['usuario']['rol'] ?? 'invitado';
}

function esAdmin(): bool
{
    return obtenerRolUsuario() === 'administrador';
}

function esCeo(): bool
{
    return obtenerRolUsuario() === 'ceo de aerolínea';
}

function esPasajero(): bool
{
    return obtenerRolUsuario() === 'usuario';
}

/**
 * Inicia la sesión para un usuario autenticado cargando sus datos reales.
 */
function iniciarSesionUsuario(array $usuario): void
{
    $_SESSION['usuario'] = [
        'id'        => (int) ($usuario['codUsuario'] ?? 0),
        'nombre'    => $usuario['nombreUsuario'] ?? 'Usuario',
        'email'     => $usuario['emailUsuario'] ?? '',
        'rol'       => $usuario['tipoUsuario'] ?? 'usuario',
        'telefono'  => $usuario['telefonoUsuario'] ?? '',
        'estado'    => $usuario['estadoUsuario'] ?? 'activo',
    ];
}

/**
 * Cierra la sesión activa.
 */
function cerrarSesion(): void
{
    $_SESSION = [];
    if (ini_get("session.use_cookies") && !headers_sent()) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/**
 * Autentica un usuario por email y contraseña contra la base de datos.
 */
function autenticarUsuario(string $email, string $clave): ?array
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE emailUsuario = ? LIMIT 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($clave, $usuario['claveUsuario'])) {
            return $usuario;
        }
    } catch (Throwable $e) {
        error_log("Error al autenticar: " . $e->getMessage());
    }
    return null;
}
