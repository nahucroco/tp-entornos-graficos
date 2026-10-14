<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/paginador.php';

/**
 * Funciones de acceso y manipulación de datos en MySQL/TiDB.
 */

// ==========================================
// VUELOS
// ==========================================

function contarTotalVuelos(
    ?string $origen = null,
    ?string $destino = null,
    ?string $fecha = null,
    ?int $aerolineaId = null,
    ?float $precioMax = null
): int {
    try {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM vuelos v
                INNER JOIN aerolineas a ON v.codAerolinea = a.codAerolinea
                WHERE 1=1";
        $params = [];

        if (!empty($origen)) {
            $sql .= " AND v.origenVuelo LIKE ?";
            $params[] = '%' . $origen . '%';
        }

        if (!empty($destino)) {
            $sql .= " AND v.destinoVuelo LIKE ?";
            $params[] = '%' . $destino . '%';
        }

        if (!empty($fecha)) {
            $sql .= " AND v.fechaSalidaVuelo = ?";
            $params[] = $fecha;
        }

        if (!empty($aerolineaId)) {
            $sql .= " AND v.codAerolinea = ?";
            $params[] = $aerolineaId;
        }

        if (!empty($precioMax) && $precioMax > 0) {
            $sql .= " AND v.precioVuelo <= ?";
            $params[] = $precioMax;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) {
        error_log("Error al contar vuelos: " . $e->getMessage());
        return 0;
    }
}

function obtenerVuelos(
    ?string $origen = null,
    ?string $destino = null,
    ?string $fecha = null,
    ?int $aerolineaId = null,
    ?float $precioMax = null,
    string $orden = 'precio_asc',
    ?int $limite = null,
    int $offset = 0
): array {
    try {
        $db = Database::getConnection();
        $sql = "SELECT v.*, a.nombreAerolinea, a.codigoIATA, a.codPais
                FROM vuelos v
                INNER JOIN aerolineas a ON v.codAerolinea = a.codAerolinea
                WHERE 1=1";
        $params = [];

        if (!empty($origen)) {
            $sql .= " AND v.origenVuelo LIKE ?";
            $params[] = '%' . $origen . '%';
        }

        if (!empty($destino)) {
            $sql .= " AND v.destinoVuelo LIKE ?";
            $params[] = '%' . $destino . '%';
        }

        if (!empty($fecha)) {
            $sql .= " AND v.fechaSalidaVuelo = ?";
            $params[] = $fecha;
        }

        if (!empty($aerolineaId)) {
            $sql .= " AND v.codAerolinea = ?";
            $params[] = $aerolineaId;
        }

        if (!empty($precioMax) && $precioMax > 0) {
            $sql .= " AND v.precioVuelo <= ?";
            $params[] = $precioMax;
        }

        switch ($orden) {
            case 'precio_desc':
                $sql .= " ORDER BY v.precioVuelo DESC";
                break;
            case 'fecha_asc':
                $sql .= " ORDER BY v.fechaSalidaVuelo ASC, v.horaSalidaVuelo ASC";
                break;
            case 'asientos_desc':
                $sql .= " ORDER BY v.asientosDisponibles DESC";
                break;
            case 'precio_asc':
            default:
                $sql .= " ORDER BY v.precioVuelo ASC";
                break;
        }

        if ($limite !== null && $limite > 0) {
            $sql .= " LIMIT " . (int)$limite . " OFFSET " . (int)$offset;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener vuelos: " . $e->getMessage());
        return [];
    }
}

function obtenerVueloPorId(int $codVuelo): ?array
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT v.*, a.nombreAerolinea, a.codigoIATA, a.descripcionAerolinea, a.codPais
                               FROM vuelos v
                               INNER JOIN aerolineas a ON v.codAerolinea = a.codAerolinea
                               WHERE v.codVuelo = ? LIMIT 1");
        $stmt->execute([$codVuelo]);
        $res = $stmt->fetch();
        return $res ?: null;
    } catch (Throwable $e) {
        error_log("Error al obtener vuelo por ID: " . $e->getMessage());
        return null;
    }
}

// ==========================================
// AEROLÍNEAS
// ==========================================

function obtenerAerolineas(): array
{
    try {
        $db = Database::getConnection();
        $sql = "SELECT a.*, u.nombreUsuario AS nombreCeo, u.emailUsuario AS emailCeo,
                       (SELECT COUNT(*) FROM vuelos v WHERE v.codAerolinea = a.codAerolinea) AS totalVuelos,
                       (SELECT MIN(precioVuelo) FROM vuelos v WHERE v.codAerolinea = a.codAerolinea) AS precioMinimo
                FROM aerolineas a
                LEFT JOIN usuarios u ON a.codUsuarioCeo = u.codUsuario
                ORDER BY a.nombreAerolinea ASC";
        return $db->query($sql)->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener aerolíneas: " . $e->getMessage());
        return [];
    }
}

function obtenerAerolineaPorId(int $codAerolinea): ?array
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT a.*, u.nombreUsuario AS nombreCeo, u.emailUsuario AS emailCeo
                               FROM aerolineas a
                               LEFT JOIN usuarios u ON a.codUsuarioCeo = u.codUsuario
                               WHERE a.codAerolinea = ? LIMIT 1");
        $stmt->execute([$codAerolinea]);
        $res = $stmt->fetch();
        return $res ?: null;
    } catch (Throwable $e) {
        error_log("Error al obtener aerolínea por ID: " . $e->getMessage());
        return null;
    }
}

// ==========================================
// PROMOCIONES
// ==========================================

function obtenerPromocionesAprobadas(): array
{
    try {
        $db = Database::getConnection();
        $sql = "SELECT p.*, a.nombreAerolinea, a.codigoIATA
                FROM promociones p
                INNER JOIN aerolineas a ON p.codAerolinea = a.codAerolinea
                WHERE p.estadoPromocion = 'aprobada'
                  AND (p.fechaFin IS NULL OR p.fechaFin >= CURRENT_DATE)
                ORDER BY p.descuentoPromocion DESC";
        return $db->query($sql)->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener promociones aprobadas: " . $e->getMessage());
        return [];
    }
}

function obtenerPromocionesPorAerolinea(int $codAerolinea): array
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.*, a.nombreAerolinea
                               FROM promociones p
                               INNER JOIN aerolineas a ON p.codAerolinea = a.codAerolinea
                               WHERE p.codAerolinea = ? AND p.estadoPromocion = 'aprobada'
                                 AND (p.fechaFin IS NULL OR p.fechaFin >= CURRENT_DATE)
                               ORDER BY p.descuentoPromocion DESC");
        $stmt->execute([$codAerolinea]);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener promociones de aerolínea: " . $e->getMessage());
        return [];
    }
}

function obtenerTodasPromociones(): array
{
    try {
        $db = Database::getConnection();
        $sql = "SELECT p.*, a.nombreAerolinea, a.codigoIATA
                FROM promociones p
                INNER JOIN aerolineas a ON p.codAerolinea = a.codAerolinea
                ORDER BY p.codPromocion DESC";
        return $db->query($sql)->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener todas las promociones: " . $e->getMessage());
        return [];
    }
}

// ==========================================
// NOVEDADES
// ==========================================

function obtenerNovedadesVigentes(): array
{
    try {
        $db = Database::getConnection();
        $sql = "SELECT n.*, u.nombreUsuario AS publicadoPor
                FROM novedades n
                LEFT JOIN usuarios u ON n.codUsuarioAdmin = u.codUsuario
                WHERE n.fechaExpiracionNovedad >= CURRENT_DATE
                ORDER BY n.fechaPublicacionNovedad DESC";
        return $db->query($sql)->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener novedades vigentes: " . $e->getMessage());
        return [];
    }
}

function obtenerTodasNovedades(): array
{
    try {
        $db = Database::getConnection();
        $sql = "SELECT n.*, u.nombreUsuario AS publicadoPor
                FROM novedades n
                LEFT JOIN usuarios u ON n.codUsuarioAdmin = u.codUsuario
                ORDER BY n.codNovedad DESC";
        return $db->query($sql)->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener todas las novedades: " . $e->getMessage());
        return [];
    }
}

// ==========================================
// RESERVAS Y COMPRAS
// ==========================================

function contarTotalReservasPorUsuario(int $codUsuario, ?string $estado = null): int
{
    try {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM reservas WHERE codUsuario = ?";
        $params = [$codUsuario];

        if (!empty($estado)) {
            $sql .= " AND estadoReserva = ?";
            $params[] = $estado;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) {
        error_log("Error al contar reservas de usuario: " . $e->getMessage());
        return 0;
    }
}

function obtenerReservasPorUsuario(
    int $codUsuario,
    ?string $estado = null,
    ?int $limite = null,
    int $offset = 0
): array {
    try {
        $db = Database::getConnection();
        $sql = "SELECT r.*, v.origenVuelo, v.destinoVuelo, v.fechaSalidaVuelo, v.horaSalidaVuelo, v.precioVuelo,
                       a.nombreAerolinea, a.codigoIATA, p.descripcionPromocion, p.descuentoPromocion
                FROM reservas r
                INNER JOIN vuelos v ON r.codVuelo = v.codVuelo
                INNER JOIN aerolineas a ON v.codAerolinea = a.codAerolinea
                LEFT JOIN promociones p ON r.codPromocion = p.codPromocion
                WHERE r.codUsuario = ?";
        $params = [$codUsuario];

        if (!empty($estado)) {
            $sql .= " AND r.estadoReserva = ?";
            $params[] = $estado;
        }

        $sql .= " ORDER BY r.fechaReserva DESC";

        if ($limite !== null && $limite > 0) {
            $sql .= " LIMIT " . (int)$limite . " OFFSET " . (int)$offset;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener reservas de usuario: " . $e->getMessage());
        return [];
    }
}

function obtenerReservaPorId(int $codReserva): ?array
{
    try {
        $db = Database::getConnection();
        $sql = "SELECT r.*, u.nombreUsuario, u.emailUsuario, u.telefonoUsuario,
                       v.origenVuelo, v.destinoVuelo, v.fechaSalidaVuelo, v.horaSalidaVuelo, v.precioVuelo, v.asientosDisponibles,
                       a.nombreAerolinea, a.codigoIATA, a.descripcionAerolinea,
                       p.descripcionPromocion, p.descuentoPromocion
                FROM reservas r
                INNER JOIN usuarios u ON r.codUsuario = u.codUsuario
                INNER JOIN vuelos v ON r.codVuelo = v.codVuelo
                INNER JOIN aerolineas a ON v.codAerolinea = a.codAerolinea
                LEFT JOIN promociones p ON r.codPromocion = p.codPromocion
                WHERE r.codReserva = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$codReserva]);
        $res = $stmt->fetch();
        return $res ?: null;
    } catch (Throwable $e) {
        error_log("Error al obtener reserva: " . $e->getMessage());
        return null;
    }
}

/**
 * Crea una reserva en estado 'pendiente de pago' y decrementa el asiento disponible.
 */
function crearReserva(int $codUsuario, int $codVuelo, ?int $codPromocion, float $precioFinal): ?int
{
    try {
        $db = Database::getConnection();
        $db->beginTransaction();

        // 1. Verificar cupos disponibles
        $stmt = $db->prepare("SELECT asientosDisponibles FROM vuelos WHERE codVuelo = ? FOR UPDATE");
        $stmt->execute([$codVuelo]);
        $asientos = (int) $stmt->fetchColumn();

        if ($asientos <= 0) {
            $db->rollBack();
            return null; // Sin cupo
        }

        // 2. Decrementar asiento disponible en vuelo
        $stmt = $db->prepare("UPDATE vuelos SET asientosDisponibles = asientosDisponibles - 1 WHERE codVuelo = ?");
        $stmt->execute([$codVuelo]);

        // 3. Insertar reserva con estado 'pendiente de pago'
        $stmt = $db->prepare("INSERT INTO reservas (codUsuario, codVuelo, codPromocion, fechaReserva, precioFinal, estadoReserva)
                              VALUES (?, ?, ?, NOW(), ?, 'pendiente de pago')");
        $stmt->execute([$codUsuario, $codVuelo, $codPromocion ?: null, $precioFinal]);
        $codReserva = (int) $db->lastInsertId();

        $db->commit();
        return $codReserva;
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Error al crear reserva: " . $e->getMessage());
        return null;
    }
}

/**
 * Confirma el pago de la reserva pasando a 'confirmada' (Compra).
 */
function confirmarPagoReserva(int $codReserva, int $codUsuario): bool
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE reservas SET estadoReserva = 'confirmada'
                              WHERE codReserva = ? AND codUsuario = ? AND estadoReserva = 'pendiente de pago'");
        return $stmt->execute([$codReserva, $codUsuario]) && $stmt->rowCount() > 0;
    } catch (Throwable $e) {
        error_log("Error al confirmar pago: " . $e->getMessage());
        return false;
    }
}

/**
 * Cancela una reserva con la regla estricta de negocio:
 * "Un usuario podrá cancelar una reserva hasta 72 horas antes de la salida del vuelo".
 * Al cancelar, se re-incrementa el asiento disponible.
 */
function cancelarReserva(int $codReserva, int $codUsuario): array
{
    try {
        $db = Database::getConnection();
        $db->beginTransaction();

        $stmt = $db->prepare("SELECT r.*, v.fechaSalidaVuelo, v.horaSalidaVuelo, v.codVuelo
                              FROM reservas r
                              INNER JOIN vuelos v ON r.codVuelo = v.codVuelo
                              WHERE r.codReserva = ? AND r.codUsuario = ? FOR UPDATE");
        $stmt->execute([$codReserva, $codUsuario]);
        $reserva = $stmt->fetch();

        if (!$reserva) {
            $db->rollBack();
            return ['ok' => false, 'mensaje' => 'La reserva no existe o no pertenece al usuario.'];
        }

        if ($reserva['estadoReserva'] === 'cancelada') {
            $db->rollBack();
            return ['ok' => false, 'mensaje' => 'La reserva ya fue cancelada con anterioridad.'];
        }

        // Validar 72 horas antes de la salida
        $fechaHoraVuelo = strtotime($reserva['fechaSalidaVuelo'] . ' ' . $reserva['horaSalidaVuelo']);
        $ahora = time();
        $horasRestantes = ($fechaHoraVuelo - $ahora) / 3600;

        if ($horasRestantes < 72) {
            $db->rollBack();
            return [
                'ok' => false,
                'mensaje' => 'No es posible cancelar la reserva: faltan menos de 72 horas para la salida del vuelo (Regla de Negocio #13).'
            ];
        }

        // Actualizar estado a 'cancelada'
        $stmt = $db->prepare("UPDATE reservas SET estadoReserva = 'cancelada' WHERE codReserva = ?");
        $stmt->execute([$codReserva]);

        // Re-incrementar asiento en vuelo
        $stmt = $db->prepare("UPDATE vuelos SET asientosDisponibles = asientosDisponibles + 1 WHERE codVuelo = ?");
        $stmt->execute([$reserva['codVuelo']]);

        $db->commit();
        return ['ok' => true, 'mensaje' => 'Reserva #' . $codReserva . ' cancelada correctamente. El asiento fue liberado.'];
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Error al cancelar reserva: " . $e->getMessage());
        return ['ok' => false, 'mensaje' => 'Ocurrió un error inesperado al procesar la cancelación.'];
    }
}

// ==========================================
// CEO DE AEROLÍNEA
// ==========================================

function obtenerAerolineaDeCeo(int $codUsuarioCeo): ?array
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM aerolineas WHERE codUsuarioCeo = ? LIMIT 1");
        $stmt->execute([$codUsuarioCeo]);
        $res = $stmt->fetch();
        return $res ?: null;
    } catch (Throwable $e) {
        error_log("Error al obtener aerolínea de CEO: " . $e->getMessage());
        return null;
    }
}

function contarTotalVuelosPorCeo(int $codUsuarioCeo): int
{
    try {
        $aero = obtenerAerolineaDeCeo($codUsuarioCeo);
        if (!$aero) {
            return 0;
        }
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM vuelos WHERE codAerolinea = ?");
        $stmt->execute([$aero['codAerolinea']]);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) {
        error_log("Error al contar vuelos de CEO: " . $e->getMessage());
        return 0;
    }
}

function obtenerVuelosPorCeo(int $codUsuarioCeo, ?int $limite = null, int $offset = 0): array
{
    try {
        $aero = obtenerAerolineaDeCeo($codUsuarioCeo);
        if (!$aero) {
            return [];
        }
        $db = Database::getConnection();
        $sql = "SELECT v.*, a.nombreAerolinea, a.codigoIATA,
                      (SELECT COUNT(*) FROM reservas r WHERE r.codVuelo = v.codVuelo AND r.estadoReserva != 'cancelada') AS reservasRealizadas
                      FROM vuelos v
                      INNER JOIN aerolineas a ON v.codAerolinea = a.codAerolinea
                      WHERE v.codAerolinea = ?
                      ORDER BY v.fechaSalidaVuelo ASC, v.horaSalidaVuelo ASC";

        if ($limite !== null && $limite > 0) {
            $sql .= " LIMIT " . (int)$limite . " OFFSET " . (int)$offset;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute([$aero['codAerolinea']]);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener vuelos de CEO: " . $e->getMessage());
        return [];
    }
}

function obtenerPromocionesPorCeo(int $codUsuarioCeo): array
{
    try {
        $aero = obtenerAerolineaDeCeo($codUsuarioCeo);
        if (!$aero) {
            return [];
        }
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.*, a.nombreAerolinea
                              FROM promociones p
                              INNER JOIN aerolineas a ON p.codAerolinea = a.codAerolinea
                              WHERE p.codAerolinea = ?
                              ORDER BY p.codPromocion DESC");
        $stmt->execute([$aero['codAerolinea']]);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener promociones de CEO: " . $e->getMessage());
        return [];
    }
}

function obtenerMetricasCeo(int $codUsuarioCeo): array
{
    $aero = obtenerAerolineaDeCeo($codUsuarioCeo);
    if (!$aero) {
        return [
            'aerolinea' => null,
            'totalVuelos' => 0,
            'ventasConfirmadas' => 0,
            'recaudacion' => 0.0,
            'asientosOcupados' => 0,
        ];
    }
    try {
        $db = Database::getConnection();
        $aId = $aero['codAerolinea'];

        $totalVuelos = (int) $db->query("SELECT COUNT(*) FROM vuelos WHERE codAerolinea = $aId")->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) AS totalVentas, COALESCE(SUM(r.precioFinal), 0) AS totalMonto
                              FROM reservas r
                              INNER JOIN vuelos v ON r.codVuelo = v.codVuelo
                              WHERE v.codAerolinea = ? AND r.estadoReserva = 'confirmada'");
        $stmt->execute([$aId]);
        $ventas = $stmt->fetch();

        $stmt = $db->prepare("SELECT COUNT(*) FROM reservas r
                              INNER JOIN vuelos v ON r.codVuelo = v.codVuelo
                              WHERE v.codAerolinea = ? AND r.estadoReserva != 'cancelada'");
        $stmt->execute([$aId]);
        $ocupados = (int) $stmt->fetchColumn();

        return [
            'aerolinea' => $aero,
            'totalVuelos' => $totalVuelos,
            'ventasConfirmadas' => (int) ($ventas['totalVentas'] ?? 0),
            'recaudacion' => (float) ($ventas['totalMonto'] ?? 0),
            'asientosOcupados' => $ocupados,
        ];
    } catch (Throwable $e) {
        error_log("Error al calcular métricas CEO: " . $e->getMessage());
        return ['aerolinea' => $aero, 'totalVuelos' => 0, 'ventasConfirmadas' => 0, 'recaudacion' => 0.0, 'asientosOcupados' => 0];
    }
}

// ==========================================
// ADMINISTRADOR
// ==========================================

function obtenerMetricasAdmin(): array
{
    try {
        $db = Database::getConnection();
        $totalAerolineas = (int) $db->query("SELECT COUNT(*) FROM aerolineas")->fetchColumn();
        $totalVuelos = (int) $db->query("SELECT COUNT(*) FROM vuelos")->fetchColumn();
        $totalUsuarios = (int) $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
        $promosPendientes = (int) $db->query("SELECT COUNT(*) FROM promociones WHERE estadoPromocion = 'pendiente'")->fetchColumn();

        $ventas = $db->query("SELECT COUNT(*) AS cant, COALESCE(SUM(precioFinal), 0) AS total FROM reservas WHERE estadoReserva = 'confirmada'")->fetch();

        return [
            'totalAerolineas'   => $totalAerolineas,
            'totalVuelos'       => $totalVuelos,
            'totalUsuarios'     => $totalUsuarios,
            'promosPendientes'  => $promosPendientes,
            'ventasConfirmadas' => (int) ($ventas['cant'] ?? 0),
            'totalRecaudado'    => (float) ($ventas['total'] ?? 0),
        ];
    } catch (Throwable $e) {
        error_log("Error al obtener métricas Admin: " . $e->getMessage());
        return [
            'totalAerolineas'   => 0,
            'totalVuelos'       => 0,
            'totalUsuarios'     => 0,
            'promosPendientes'  => 0,
            'ventasConfirmadas' => 0,
            'totalRecaudado'    => 0.0,
        ];
    }
}

function obtenerVentasPorAerolinea(): array
{
    try {
        $db = Database::getConnection();
        $sql = "SELECT a.codAerolinea, a.nombreAerolinea, a.codigoIATA,
                       COUNT(r.codReserva) AS totalReservas,
                       SUM(CASE WHEN r.estadoReserva = 'confirmada' THEN 1 ELSE 0 END) AS confirmadas,
                       COALESCE(SUM(CASE WHEN r.estadoReserva = 'confirmada' THEN r.precioFinal ELSE 0 END), 0) AS totalRecaudado
                FROM aerolineas a
                LEFT JOIN vuelos v ON a.codAerolinea = v.codAerolinea
                LEFT JOIN reservas r ON v.codVuelo = r.codVuelo
                GROUP BY a.codAerolinea, a.nombreAerolinea, a.codigoIATA
                ORDER BY totalRecaudado DESC";
        return $db->query($sql)->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener ventas por aerolínea: " . $e->getMessage());
        return [];
    }
}

function obtenerTopVuelosReservados(int $limite = 5): array
{
    try {
        $db = Database::getConnection();
        $sql = "SELECT v.codVuelo, v.origenVuelo, v.destinoVuelo, v.fechaSalidaVuelo, v.precioVuelo,
                       a.nombreAerolinea, a.codigoIATA,
                       COUNT(r.codReserva) AS totalReservas,
                       SUM(CASE WHEN r.estadoReserva = 'confirmada' THEN 1 ELSE 0 END) AS confirmadas,
                       COALESCE(SUM(CASE WHEN r.estadoReserva = 'confirmada' THEN r.precioFinal ELSE 0 END), 0) AS totalRecaudado
                FROM vuelos v
                INNER JOIN aerolineas a ON v.codAerolinea = a.codAerolinea
                LEFT JOIN reservas r ON v.codVuelo = r.codVuelo
                GROUP BY v.codVuelo, v.origenVuelo, v.destinoVuelo, v.fechaSalidaVuelo, v.precioVuelo, a.nombreAerolinea, a.codigoIATA
                ORDER BY totalReservas DESC
                LIMIT $limite";
        return $db->query($sql)->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener top vuelos: " . $e->getMessage());
        return [];
    }
}

function obtenerDistribucionUsuarios(): array
{
    try {
        $db = Database::getConnection();
        $sql = "SELECT tipoUsuario, COUNT(*) AS cantidad FROM usuarios GROUP BY tipoUsuario";
        return $db->query($sql)->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener distribución de usuarios: " . $e->getMessage());
        return [];
    }
}

// ==========================================
// MUTACIONES Y OPERACIONES CRUD
// ==========================================

function registrarUsuario(string $nombre, string $email, string $clave, string $telefono = '', string $tipoUsuario = 'usuario'): array
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT codUsuario FROM usuarios WHERE emailUsuario = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['ok' => false, 'mensaje' => 'El correo electrónico ya se encuentra registrado.'];
        }

        $esCeo = $tipoUsuario === 'ceo de aerolínea';
        $estado = $esCeo ? 'pendiente' : 'activo';
        $hash = password_hash($clave, PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO usuarios (nombreUsuario, claveUsuario, tipoUsuario, emailUsuario, telefonoUsuario, estadoUsuario, emailValidado, fechaCreacion)
                              VALUES (?, ?, ?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$nombre, $hash, $tipoUsuario, $email, $telefono, $estado]);
        $id = (int)$db->lastInsertId();

        return [
            'ok' => true,
            'mensaje' => $esCeo
                ? '¡Solicitud enviada con éxito! Un administrador debe aprobar tu cuenta de CEO antes de que puedas ingresar.'
                : '¡Cuenta creada con éxito! Ya podés iniciar sesión y reservar tus pasajes.',
            'usuario' => [
                'codUsuario' => $id,
                'nombreUsuario' => $nombre,
                'emailUsuario' => $email,
                'tipoUsuario' => $tipoUsuario,
                'telefonoUsuario' => $telefono,
                'estadoUsuario' => $estado
            ]
        ];
    } catch (Throwable $e) {
        error_log("Error al registrar usuario: " . $e->getMessage());
        return ['ok' => false, 'mensaje' => 'Error al crear la cuenta: ' . $e->getMessage()];
    }
}

function actualizarPerfilUsuario(int $id, string $nombre, string $email, string $telefono, ?string $nuevaClave = null): array
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT codUsuario FROM usuarios WHERE emailUsuario = ? AND codUsuario != ? LIMIT 1");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            return ['ok' => false, 'mensaje' => 'El correo electrónico ya pertenece a otra cuenta.'];
        }

        if (!empty($nuevaClave)) {
            $hash = password_hash($nuevaClave, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE usuarios SET nombreUsuario = ?, emailUsuario = ?, telefonoUsuario = ?, claveUsuario = ? WHERE codUsuario = ?");
            $stmt->execute([$nombre, $email, $telefono, $hash, $id]);
        } else {
            $stmt = $db->prepare("UPDATE usuarios SET nombreUsuario = ?, emailUsuario = ?, telefonoUsuario = ? WHERE codUsuario = ?");
            $stmt->execute([$nombre, $email, $telefono, $id]);
        }

        return ['ok' => true, 'mensaje' => 'Perfil actualizado correctamente.'];
    } catch (Throwable $e) {
        error_log("Error al actualizar perfil: " . $e->getMessage());
        return ['ok' => false, 'mensaje' => 'No se pudo actualizar el perfil.'];
    }
}

function crearAerolinea(string $nombre, string $iata, string $pais, ?int $ceoId, string $desc): bool
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO aerolineas (nombreAerolinea, codigoIATA, codPais, codUsuarioCeo, descripcionAerolinea) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$nombre, strtoupper($iata), strtoupper($pais), $ceoId ?: null, $desc]);
    } catch (Throwable $e) {
        error_log("Error al crear aerolínea: " . $e->getMessage());
        return false;
    }
}

function modificarAerolinea(int $id, string $nombre, string $iata, string $pais, ?int $ceoId, string $desc): bool
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE aerolineas SET nombreAerolinea = ?, codigoIATA = ?, codPais = ?, codUsuarioCeo = ?, descripcionAerolinea = ? WHERE codAerolinea = ?");
        return $stmt->execute([$nombre, strtoupper($iata), strtoupper($pais), $ceoId ?: null, $desc, $id]);
    } catch (Throwable $e) {
        error_log("Error al modificar aerolínea: " . $e->getMessage());
        return false;
    }
}

function eliminarAerolinea(int $id): array
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM vuelos WHERE codAerolinea = ?");
        $stmt->execute([$id]);
        if ((int)$stmt->fetchColumn() > 0) {
            return ['ok' => false, 'mensaje' => 'No se puede eliminar la aerolínea porque posee vuelos programados.'];
        }

        $stmt = $db->prepare("DELETE FROM aerolineas WHERE codAerolinea = ?");
        $stmt->execute([$id]);
        return ['ok' => true, 'mensaje' => 'Aerolínea eliminada correctamente.'];
    } catch (Throwable $e) {
        error_log("Error al eliminar aerolínea: " . $e->getMessage());
        return ['ok' => false, 'mensaje' => 'Error al eliminar aerolínea: ' . $e->getMessage()];
    }
}

function crearVuelo(int $aerolineaId, string $origen, string $destino, string $fecha, string $hora, float $precio, int $asientos): bool
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO vuelos (codAerolinea, origenVuelo, destinoVuelo, fechaSalidaVuelo, horaSalidaVuelo, precioVuelo, asientosDisponibles)
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$aerolineaId, $origen, $destino, $fecha, $hora, $precio, $asientos]);
    } catch (Throwable $e) {
        error_log("Error al crear vuelo: " . $e->getMessage());
        return false;
    }
}

function modificarVuelo(int $id, string $origen, string $destino, string $fecha, string $hora, float $precio, int $asientos): bool
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE vuelos SET origenVuelo = ?, destinoVuelo = ?, fechaSalidaVuelo = ?, horaSalidaVuelo = ?, precioVuelo = ?, asientosDisponibles = ? WHERE codVuelo = ?");
        return $stmt->execute([$origen, $destino, $fecha, $hora, $precio, $asientos, $id]);
    } catch (Throwable $e) {
        error_log("Error al modificar vuelo: " . $e->getMessage());
        return false;
    }
}

function eliminarVuelo(int $id): array
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM reservas WHERE codVuelo = ? AND estadoReserva != 'cancelada'");
        $stmt->execute([$id]);
        if ((int)$stmt->fetchColumn() > 0) {
            return ['ok' => false, 'mensaje' => 'No es posible eliminar el vuelo porque tiene reservas activas o pendientes asociadas.'];
        }

        $stmt = $db->prepare("DELETE FROM vuelos WHERE codVuelo = ?");
        $stmt->execute([$id]);
        return ['ok' => true, 'mensaje' => 'Vuelo eliminado correctamente del sistema.'];
    } catch (Throwable $e) {
        error_log("Error al eliminar vuelo: " . $e->getMessage());
        return ['ok' => false, 'mensaje' => 'Error al eliminar vuelo: ' . $e->getMessage()];
    }
}

function crearPromocion(int $aerolineaId, string $desc, float $descuento, ?string $inicio, ?string $fin): bool
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO promociones (codAerolinea, descripcionPromocion, descuentoPromocion, estadoPromocion, fechaInicio, fechaFin)
                              VALUES (?, ?, ?, 'pendiente', ?, ?)");
        return $stmt->execute([$aerolineaId, $desc, $descuento, $inicio ?: null, $fin ?: null]);
    } catch (Throwable $e) {
        error_log("Error al crear promoción: " . $e->getMessage());
        return false;
    }
}

function eliminarPromocion(int $id): bool
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM promociones WHERE codPromocion = ?");
        return $stmt->execute([$id]);
    } catch (Throwable $e) {
        error_log("Error al eliminar promoción: " . $e->getMessage());
        return false;
    }
}

function cambiarEstadoPromocion(int $id, string $nuevoEstado): bool
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE promociones SET estadoPromocion = ? WHERE codPromocion = ?");
        return $stmt->execute([$nuevoEstado, $id]);
    } catch (Throwable $e) {
        error_log("Error al cambiar estado de promoción: " . $e->getMessage());
        return false;
    }
}

function crearNovedad(int $adminId, string $texto, string $fechaPub, string $fechaExp): bool
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO novedades (codUsuarioAdmin, textoNovedad, fechaPublicacionNovedad, fechaExpiracionNovedad)
                              VALUES (?, ?, ?, ?)");
        return $stmt->execute([$adminId, $texto, $fechaPub, $fechaExp]);
    } catch (Throwable $e) {
        error_log("Error al crear novedad: " . $e->getMessage());
        return false;
    }
}

function modificarNovedad(int $id, string $texto, string $fechaPub, string $fechaExp): bool
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE novedades SET textoNovedad = ?, fechaPublicacionNovedad = ?, fechaExpiracionNovedad = ? WHERE codNovedad = ?");
        return $stmt->execute([$texto, $fechaPub, $fechaExp, $id]);
    } catch (Throwable $e) {
        error_log("Error al modificar novedad: " . $e->getMessage());
        return false;
    }
}

function eliminarNovedad(int $id): bool
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM novedades WHERE codNovedad = ?");
        return $stmt->execute([$id]);
    } catch (Throwable $e) {
        error_log("Error al eliminar novedad: " . $e->getMessage());
        return false;
    }
}

function cambiarEstadoUsuario(int $id, string $nuevoEstado, ?string $tipoUsuario = null): bool
{
    try {
        $db = Database::getConnection();
        if ($tipoUsuario) {
            $stmt = $db->prepare("UPDATE usuarios SET estadoUsuario = ?, tipoUsuario = ? WHERE codUsuario = ?");
            return $stmt->execute([$nuevoEstado, $tipoUsuario, $id]);
        }
        $stmt = $db->prepare("UPDATE usuarios SET estadoUsuario = ? WHERE codUsuario = ?");
        return $stmt->execute([$nuevoEstado, $id]);
    } catch (Throwable $e) {
        error_log("Error al cambiar estado de usuario: " . $e->getMessage());
        return false;
    }
}

function contarTotalUsuarios(): int
{
    try {
        $db = Database::getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    } catch (Throwable $e) {
        error_log("Error al contar usuarios: " . $e->getMessage());
        return 0;
    }
}

function obtenerUsuariosPaginados(int $limite = 10, int $offset = 0): array
{
    try {
        $db = Database::getConnection();
        $sql = "SELECT * FROM usuarios ORDER BY codUsuario ASC LIMIT " . (int)$limite . " OFFSET " . (int)$offset;
        return $db->query($sql)->fetchAll();
    } catch (Throwable $e) {
        error_log("Error al obtener usuarios paginados: " . $e->getMessage());
        return [];
    }
}


