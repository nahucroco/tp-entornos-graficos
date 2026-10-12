<?php
declare(strict_types=1);

/**
 * Helper de protección CSRF (Cross-Site Request Forgery).
 */

function obtenerTokenCsrf(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string)$_SESSION['csrf_token'];
}

function validarTokenCsrf(?string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

function campoCsrf(): string
{
    $token = htmlspecialchars(obtenerTokenCsrf(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}
