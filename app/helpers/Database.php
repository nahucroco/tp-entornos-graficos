<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

/**
 * Conexión centralizada a MySQL / TiDB utilizando PDO (Patrón Singleton).
 * Soporta conexiones seguras SSL para bases de datos remotas en la nube.
 */
class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            if (defined('DB_SSL') && DB_SSL) {
                $caPath = __DIR__ . '/../ca-bundle.crt';
                if (file_exists($caPath)) {
                    $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
                }
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
            }

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                error_log("Error de conexión a la BD: " . $e->getMessage());
                die("Error de conexión con la base de datos: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
