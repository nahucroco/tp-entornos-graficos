<?php
declare(strict_types=1);

/**
 * Validación de datos del lado del servidor.
 * Son las mismas reglas que aplica Zod en el navegador (public/assets/js/esquemas-zod.js):
 * la validación del navegador mejora la experiencia, pero se puede saltear,
 * así que el servidor vuelve a validar todo antes de guardar.
 *
 * Cada función devuelve el mensaje de error en español, o null si el valor es válido.
 */

const REGEX_NOMBRE_PERSONA = "/^[\\p{L}][\\p{L} .'-]*$/u";
const REGEX_CIUDAD = "/^[\\p{L}0-9][\\p{L}0-9 .,'()-]*$/u";

function errNombrePersona(string $valor, string $etiqueta = 'El nombre'): ?string
{
    $largo = mb_strlen($valor);
    if ($largo < 2) return "{$etiqueta} debe tener al menos 2 caracteres.";
    if ($largo > 100) return "{$etiqueta} no puede superar los 100 caracteres.";
    if (!preg_match(REGEX_NOMBRE_PERSONA, $valor)) return "{$etiqueta} solo puede contener letras, espacios, apóstrofos y guiones.";
    return null;
}

/** DNI argentino (7 u 8 dígitos, con o sin puntos) o pasaporte (6 a 9 letras y números). */
function errDocumento(string $valor): ?string
{
    $sinPuntos = str_replace('.', '', $valor);
    if (preg_match('/^\d{7,8}$/', $sinPuntos)) return null;
    if (preg_match('/^(?=.*[A-Za-z])[A-Za-z0-9]{6,9}$/', $valor)) return null;
    return 'Ingresá un DNI de 7 u 8 dígitos o un pasaporte de 6 a 9 letras y números.';
}

function normalizarDocumento(string $valor): string
{
    return strtoupper(str_replace('.', '', trim($valor)));
}

function errEmail(string $valor): ?string
{
    if ($valor === '') return 'Ingresá tu correo electrónico.';
    if (mb_strlen($valor) > 100 || !filter_var($valor, FILTER_VALIDATE_EMAIL)) {
        return 'Ingresá un correo electrónico válido (por ejemplo, nombre@correo.com).';
    }
    return null;
}

function errTelefono(string $valor, bool $obligatorio = true): ?string
{
    if ($valor === '') return $obligatorio ? 'Ingresá un teléfono de contacto.' : null;
    $digitos = preg_replace('/\D/', '', $valor);
    if (mb_strlen($valor) > 20 || !preg_match('/^\+?[0-9 ()-]+$/', $valor) || strlen($digitos) < 8 || strlen($digitos) > 15) {
        return 'Ingresá un teléfono válido: entre 8 y 15 dígitos; se permiten espacios, guiones, paréntesis y el signo +.';
    }
    return null;
}

function errClave(string $valor): ?string
{
    if (strlen($valor) < 6) return 'La contraseña debe tener al menos 6 caracteres.';
    if (strlen($valor) > 72) return 'La contraseña no puede superar los 72 caracteres.';
    return null;
}

function errTexto(string $valor, string $etiqueta, int $min, int $max): ?string
{
    $largo = mb_strlen($valor);
    if ($largo < $min) return $min <= 1 ? "Completá {$etiqueta}." : "{$etiqueta} debe tener al menos {$min} caracteres.";
    if ($largo > $max) return "{$etiqueta} no puede superar los {$max} caracteres.";
    return null;
}

function errCiudad(string $valor, string $etiqueta): ?string
{
    if (($e = errTexto($valor, $etiqueta, 2, 50)) !== null) return $e;
    if (!preg_match(REGEX_CIUDAD, $valor)) return "{$etiqueta} contiene caracteres no permitidos.";
    return null;
}

function errNumero(string $valor, string $etiqueta, float $min, float $max, bool $entero = false): ?string
{
    if ($valor === '' || !is_numeric($valor)) return "{$etiqueta} debe ser un número.";
    $num = (float)$valor;
    if ($entero && floor($num) != $num) return "{$etiqueta} debe ser un número entero.";
    if ($num < $min || $num > $max) return "{$etiqueta} debe estar entre " . (0 + $min) . " y " . (0 + $max) . ".";
    return null;
}

function errFecha(string $valor, string $etiqueta, bool $obligatoria = true): ?string
{
    if ($valor === '') return $obligatoria ? "Completá {$etiqueta}." : null;
    $dt = DateTime::createFromFormat('!Y-m-d', $valor);
    if (!$dt || $dt->format('Y-m-d') !== $valor) return ucfirst($etiqueta) . " no es una fecha válida.";
    return null;
}

function errHora(string $valor): ?string
{
    return preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $valor) ? null : 'Ingresá una hora válida (HH:MM).';
}

function errCodigoLetras(string $valor, string $etiqueta, int $min, int $max): ?string
{
    if (!preg_match('/^[A-Za-z]{' . $min . ',' . $max . '}$/', $valor)) {
        return $min === $max
            ? "{$etiqueta} debe tener exactamente {$min} letras."
            : "{$etiqueta} debe tener entre {$min} y {$max} letras.";
    }
    return null;
}

/** Devuelve el primer error de la lista (o null si no hay ninguno). */
function primerError(?string ...$errores): ?string
{
    foreach ($errores as $error) {
        if ($error !== null) return $error;
    }
    return null;
}

/** Si hay error, lo muestra como mensaje y redirige; si no, sigue. */
function rechazarSiHayError(?string $error, string $destino): void
{
    if ($error === null) return;
    $_SESSION['mensaje'] = $error;
    $_SESSION['tipo_mensaje'] = 'warning';
    header('Location: ' . $destino);
    exit;
}
