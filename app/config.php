<?php
declare(strict_types=1);

/**
 * Carga de variables de entorno desde .env (si existe) sin librerías externas.
 */
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if (!array_key_exists($key, $_ENV)) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

/**
 * Definición de constantes de conexión a MySQL / TiDB Cloud.
 */
define('DB_HOST', getenv('DB_HOST') ?: 'gateway01.us-east-1.prod.aws.tidbcloud.com');
define('DB_PORT', getenv('DB_PORT') ?: '4000');
define('DB_NAME', getenv('DB_NAME') ?: 'tp_entornos_graficos');
define('DB_USER', getenv('DB_USER') ?: '2j2i1kPjD1e3mQ9.root');
define('DB_PASS', (getenv('DB_PASS') !== false && getenv('DB_PASS') !== '') ? getenv('DB_PASS') : 'v9U765yA2346J1sL');
define('DB_SSL',  filter_var(getenv('DB_SSL') !== false ? getenv('DB_SSL') : 'true', FILTER_VALIDATE_BOOLEAN));
