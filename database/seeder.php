<?php
declare(strict_types=1);

/**
 * Seeder oficial de Base de Datos para Cóndor AeroReserva.
 * Pobla de forma segura e idempotente usuarios, aerolíneas, promociones, vuelos, novedades y reservas.
 * Compatible con MySQL 8+ y TiDB Cloud.
 */

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/helpers/Database.php';

echo "========================================================\n";
echo "    INICIANDO SEEDER DE BASE DE DATOS - CÓNDOR AERORESERVA\n";
echo "========================================================\n\n";

try {
    $db = Database::getConnection();
    echo "[OK] Conexión establecida con la base de datos: " . DB_NAME . "\n";
} catch (Throwable $e) {
    die("[ERROR] No se pudo conectar a la base de datos: " . $e->getMessage() . "\n");
}

// -------------------------------------------------------------
// 1. SEED DE USUARIOS
// -------------------------------------------------------------
echo "\n--> 1. Sembrando Usuarios...\n";

$claveAdmin = password_hash('admin123', PASSWORD_BCRYPT);
$claveCeo = password_hash('ceo123', PASSWORD_BCRYPT);
$clavePasajero = password_hash('pasajero123', PASSWORD_BCRYPT);

$usuarios = [
    // Administradores
    [
        'nombre' => 'Administrador General',
        'email' => 'admin@aeroreserva.com',
        'clave' => $claveAdmin,
        'rol' => 'administrador',
        'telefono' => '341-555-0100',
        'estado' => 'activo',
    ],
    [
        'nombre' => 'Soporte Técnico Admin',
        'email' => 'soporte@aeroreserva.com',
        'clave' => $claveAdmin,
        'rol' => 'administrador',
        'telefono' => '341-555-0101',
        'estado' => 'activo',
    ],
    // CEOs
    [
        'nombre' => 'Carlos CEO Aerolíneas',
        'email' => 'ceo.ar@aeroreserva.com',
        'clave' => $claveCeo,
        'rol' => 'ceo de aerolínea',
        'telefono' => '341-555-0200',
        'estado' => 'activo',
    ],
    [
        'nombre' => 'Martín CEO Flybondi',
        'email' => 'ceo.flybondi@aeroreserva.com',
        'clave' => $claveCeo,
        'rol' => 'ceo de aerolínea',
        'telefono' => '341-555-0201',
        'estado' => 'activo',
    ],
    [
        'nombre' => 'Roberto CEO LATAM',
        'email' => 'ceo.latam@aeroreserva.com',
        'clave' => $claveCeo,
        'rol' => 'ceo de aerolínea',
        'telefono' => '341-555-0202',
        'estado' => 'activo',
    ],
    [
        'nombre' => 'Esteban CEO JetSMART',
        'email' => 'ceo.jetsmart@aeroreserva.com',
        'clave' => $claveCeo,
        'rol' => 'ceo de aerolínea',
        'telefono' => '341-555-0203',
        'estado' => 'activo',
    ],
    [
        'nombre' => 'Valeria CEO Avianca (Pendiente)',
        'email' => 'ceo.pendiente@aeroreserva.com',
        'clave' => $claveCeo,
        'rol' => 'ceo de aerolínea',
        'telefono' => '341-555-0204',
        'estado' => 'pendiente', // Para probar aprobación en panel admin
    ],
    // Pasajeros / Clientes
    [
        'nombre' => 'Mariana Pasajera',
        'email' => 'mariana@gmail.com',
        'clave' => $clavePasajero,
        'rol' => 'usuario',
        'telefono' => '341-555-0300',
        'estado' => 'activo',
    ],
    [
        'nombre' => 'Natalia Franco',
        'email' => 'franconatalia222@gmail.com',
        'clave' => $clavePasajero,
        'rol' => 'usuario',
        'telefono' => '341-555-0301',
        'estado' => 'activo',
    ],
    [
        'nombre' => 'Juan Pérez',
        'email' => 'juan.perez@gmail.com',
        'clave' => $clavePasajero,
        'rol' => 'usuario',
        'telefono' => '341-555-0302',
        'estado' => 'activo',
    ],
    [
        'nombre' => 'Lucas Rodríguez',
        'email' => 'lucas.rodriguez@gmail.com',
        'clave' => $clavePasajero,
        'rol' => 'usuario',
        'telefono' => '341-555-0303',
        'estado' => 'activo',
    ],
    [
        'nombre' => 'Usuario Suspendido',
        'email' => 'usuario.suspendido@gmail.com',
        'clave' => $clavePasajero,
        'rol' => 'usuario',
        'telefono' => '341-555-0399',
        'estado' => 'denegado', // Para probar bloqueo por cuenta denegada
    ],
];

$stmtCheckUser = $db->prepare("SELECT codUsuario FROM usuarios WHERE emailUsuario = ?");
$stmtInsertUser = $db->prepare("INSERT INTO usuarios (nombreUsuario, claveUsuario, tipoUsuario, emailUsuario, telefonoUsuario, estadoUsuario, emailValidado) VALUES (?, ?, ?, ?, ?, ?, 1)");
$stmtUpdateUser = $db->prepare("UPDATE usuarios SET nombreUsuario = ?, claveUsuario = ?, tipoUsuario = ?, telefonoUsuario = ?, estadoUsuario = ?, emailValidado = 1 WHERE emailUsuario = ?");

$userIds = [];
foreach ($usuarios as $u) {
    $stmtCheckUser->execute([$u['email']]);
    $existingId = $stmtCheckUser->fetchColumn();
    if ($existingId) {
        $stmtUpdateUser->execute([$u['nombre'], $u['clave'], $u['rol'], $u['telefono'], $u['estado'], $u['email']]);
        $userIds[$u['email']] = (int)$existingId;
        echo "   [UPDATE] Usuario: {$u['email']} (ID: {$existingId})\n";
    } else {
        $stmtInsertUser->execute([$u['nombre'], $u['clave'], $u['rol'], $u['email'], $u['telefono'], $u['estado']]);
        $newId = (int)$db->lastInsertId();
        $userIds[$u['email']] = $newId;
        echo "   [INSERT] Usuario: {$u['email']} (ID: {$newId})\n";
    }
}

// -------------------------------------------------------------
// 2. SEED DE AEROLÍNEAS
// -------------------------------------------------------------
echo "\n--> 2. Sembrando Aerolíneas...\n";

$aerolineas = [
    [
        'nombre' => 'Aerolíneas Argentinas',
        'iata' => 'ARG',
        'descripcion' => 'Línea de bandera nacional con vuelos de cabotaje e internacionales en toda América.',
        'pais' => 'ARG',
        'ceo_email' => 'ceo.ar@aeroreserva.com',
    ],
    [
        'nombre' => 'Flybondi',
        'iata' => 'FO',
        'descripcion' => 'Primera aerolínea ultra low cost de Argentina con tarifas súper accesibles.',
        'pais' => 'ARG',
        'ceo_email' => 'ceo.flybondi@aeroreserva.com',
    ],
    [
        'nombre' => 'LATAM Airlines',
        'iata' => 'LA',
        'descripcion' => 'Grupo aerocomercial líder en Sudamérica con amplia red de conexiones globales.',
        'pais' => 'CHL',
        'ceo_email' => 'ceo.latam@aeroreserva.com',
    ],
    [
        'nombre' => 'JetSMART',
        'iata' => 'WJ',
        'descripcion' => 'Aerolínea low cost con flota moderna Airbus A320 y rutas punto a punto.',
        'pais' => 'CHL',
        'ceo_email' => 'ceo.jetsmart@aeroreserva.com',
    ],
    [
        'nombre' => 'Copa Airlines',
        'iata' => 'CM',
        'descripcion' => 'Hub de las Américas conectando Argentina con el Caribe y Norteamérica.',
        'pais' => 'PAN',
        'ceo_email' => null,
    ],
    [
        'nombre' => 'Avianca',
        'iata' => 'AV',
        'descripcion' => 'Aerolínea con más de 100 años volando en América Latina.',
        'pais' => 'COL',
        'ceo_email' => null,
    ],
];

$stmtCheckAero = $db->prepare("SELECT codAerolinea FROM aerolineas WHERE codigoIATA = ?");
$stmtInsertAero = $db->prepare("INSERT INTO aerolineas (nombreAerolinea, codigoIATA, descripcionAerolinea, codPais, codUsuarioCeo) VALUES (?, ?, ?, ?, ?)");
$stmtUpdateAero = $db->prepare("UPDATE aerolineas SET nombreAerolinea = ?, descripcionAerolinea = ?, codPais = ?, codUsuarioCeo = ? WHERE codigoIATA = ?");

$aeroIds = [];
foreach ($aerolineas as $a) {
    $ceoId = !empty($a['ceo_email']) && isset($userIds[$a['ceo_email']]) ? $userIds[$a['ceo_email']] : null;
    $stmtCheckAero->execute([$a['iata']]);
    $existingId = $stmtCheckAero->fetchColumn();

    if ($existingId) {
        $stmtUpdateAero->execute([$a['nombre'], $a['descripcion'], $a['pais'], $ceoId, $a['iata']]);
        $aeroIds[$a['iata']] = (int)$existingId;
        echo "   [UPDATE] Aerolínea: {$a['nombre']} ({$a['iata']}) - CEO ID: " . ($ceoId ?: 'Ninguno') . "\n";
    } else {
        $stmtInsertAero->execute([$a['nombre'], $a['iata'], $a['descripcion'], $a['pais'], $ceoId]);
        $newId = (int)$db->lastInsertId();
        $aeroIds[$a['iata']] = $newId;
        echo "   [INSERT] Aerolínea: {$a['nombre']} ({$a['iata']}) - CEO ID: " . ($ceoId ?: 'Ninguno') . "\n";
    }
}

// -------------------------------------------------------------
// 3. SEED DE PROMOCIONES
// -------------------------------------------------------------
echo "\n--> 3. Sembrando Promociones...\n";

$promociones = [
    [
        'descripcion' => '30% Off en vuelos de primavera',
        'descuento' => 30.00,
        'iata' => 'ARG',
        'estado' => 'aprobada',
        'dias_fin' => 45,
    ],
    [
        'descripcion' => '15% Off primera compra Flybondi',
        'descuento' => 15.00,
        'iata' => 'FO',
        'estado' => 'aprobada',
        'dias_fin' => 30,
    ],
    [
        'descripcion' => '25% Off Verano en Brasil y Chile',
        'descuento' => 25.00,
        'iata' => 'LA',
        'estado' => 'aprobada',
        'dias_fin' => 60,
    ],
    [
        'descripcion' => '20% Off Rutas Patagónicas',
        'descuento' => 20.00,
        'iata' => 'WJ',
        'estado' => 'aprobada',
        'dias_fin' => 40,
    ],
    [
        'descripcion' => '10% Off Escapadas de Fin de Semana',
        'descuento' => 10.00,
        'iata' => 'ARG',
        'estado' => 'aprobada',
        'dias_fin' => 60,
    ],
    [
        'descripcion' => '40% Off Black Friday Anticipado',
        'descuento' => 40.00,
        'iata' => 'FO',
        'estado' => 'pendiente', // Para probar aprobación por admin
        'dias_fin' => 20,
    ],
    [
        'descripcion' => '18% Off Conexiones a Panamá y Caribe',
        'descuento' => 18.00,
        'iata' => 'CM',
        'estado' => 'pendiente', // Para probar rechazo por admin
        'dias_fin' => 30,
    ],
    [
        'descripcion' => '80% Off Liquidación Fuera de Temporada',
        'descuento' => 80.00,
        'iata' => 'FO',
        'estado' => 'denegada', // Historial de rechazo
        'dias_fin' => 15,
    ],
];

$stmtCheckPromo = $db->prepare("SELECT codPromocion FROM promociones WHERE descripcionPromocion = ? AND codAerolinea = ?");
$stmtInsertPromo = $db->prepare("INSERT INTO promociones (descripcionPromocion, descuentoPromocion, codAerolinea, estadoPromocion, fechaInicio, fechaFin) VALUES (?, ?, ?, ?, CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL ? DAY))");
$stmtUpdatePromo = $db->prepare("UPDATE promociones SET descuentoPromocion = ?, estadoPromocion = ?, fechaInicio = CURRENT_DATE, fechaFin = DATE_ADD(CURRENT_DATE, INTERVAL ? DAY) WHERE codPromocion = ?");

$promoIds = [];
foreach ($promociones as $p) {
    if (!isset($aeroIds[$p['iata']])) continue;
    $aId = $aeroIds[$p['iata']];

    $stmtCheckPromo->execute([$p['descripcion'], $aId]);
    $existingId = $stmtCheckPromo->fetchColumn();

    if ($existingId) {
        $stmtUpdatePromo->execute([$p['descuento'], $p['estado'], $p['dias_fin'], $existingId]);
        $promoIds[$p['descripcion']] = (int)$existingId;
        echo "   [UPDATE] Promo: {$p['descripcion']} ({$p['descuento']}%) - Estado: {$p['estado']}\n";
    } else {
        $stmtInsertPromo->execute([$p['descripcion'], $p['descuento'], $aId, $p['estado'], $p['dias_fin']]);
        $newId = (int)$db->lastInsertId();
        $promoIds[$p['descripcion']] = $newId;
        echo "   [INSERT] Promo: {$p['descripcion']} ({$p['descuento']}%) - Estado: {$p['estado']}\n";
    }
}

// -------------------------------------------------------------
// 4. SEED DE VUELOS (Fechas siempre vigentes y dinámicas)
// -------------------------------------------------------------
echo "\n--> 4. Sembrando Vuelos con Fechas Vigentes...\n";

$vuelos = [
    // Aerolíneas Argentinas (ARG)
    ['iata' => 'ARG', 'origen' => 'Rosario (ROS)', 'destino' => 'Buenos Aires (AEP)', 'dias' => 2, 'hora' => '08:30:00', 'precio' => 45000.00, 'asientos' => 48],
    ['iata' => 'ARG', 'origen' => 'Buenos Aires (AEP)', 'destino' => 'Rosario (ROS)', 'dias' => 4, 'hora' => '19:15:00', 'precio' => 45000.00, 'asientos' => 50],
    ['iata' => 'ARG', 'origen' => 'Buenos Aires (AEP)', 'destino' => 'Bariloche (BRC)', 'dias' => 3, 'hora' => '10:00:00', 'precio' => 98000.00, 'asientos' => 32],
    ['iata' => 'ARG', 'origen' => 'Bariloche (BRC)', 'destino' => 'Buenos Aires (AEP)', 'dias' => 7, 'hora' => '15:45:00', 'precio' => 98000.00, 'asientos' => 40],
    ['iata' => 'ARG', 'origen' => 'Buenos Aires (AEP)', 'destino' => 'Mendoza (MDZ)', 'dias' => 5, 'hora' => '12:30:00', 'precio' => 75000.00, 'asientos' => 44],
    ['iata' => 'ARG', 'origen' => 'Buenos Aires (AEP)', 'destino' => 'Iguazú (IGR)', 'dias' => 6, 'hora' => '09:20:00', 'precio' => 68000.00, 'asientos' => 35],
    ['iata' => 'ARG', 'origen' => 'Buenos Aires (EZE)', 'destino' => 'Ushuaia (USH)', 'dias' => 8, 'hora' => '07:15:00', 'precio' => 125000.00, 'asientos' => 28],
    ['iata' => 'ARG', 'origen' => 'Rosario (ROS)', 'destino' => 'Bariloche (BRC)', 'dias' => 10, 'hora' => '11:45:00', 'precio' => 110000.00, 'asientos' => 30],

    // Flybondi (FO)
    ['iata' => 'FO', 'origen' => 'Rosario (ROS)', 'destino' => 'Córdoba (COR)', 'dias' => 3, 'hora' => '11:00:00', 'precio' => 38000.00, 'asientos' => 60],
    ['iata' => 'FO', 'origen' => 'Córdoba (COR)', 'destino' => 'Rosario (ROS)', 'dias' => 6, 'hora' => '17:30:00', 'precio' => 38000.00, 'asientos' => 58],
    ['iata' => 'FO', 'origen' => 'Buenos Aires (AEP)', 'destino' => 'Mendoza (MDZ)', 'dias' => 4, 'hora' => '16:45:00', 'precio' => 54000.00, 'asientos' => 52],
    ['iata' => 'FO', 'origen' => 'Buenos Aires (AEP)', 'destino' => 'Salta (SLA)', 'dias' => 5, 'hora' => '08:00:00', 'precio' => 62000.00, 'asientos' => 45],
    ['iata' => 'FO', 'origen' => 'Buenos Aires (AEP)', 'destino' => 'Bariloche (BRC)', 'dias' => 9, 'hora' => '13:10:00', 'precio' => 82000.00, 'asientos' => 50],

    // LATAM Airlines (LA)
    ['iata' => 'LA', 'origen' => 'Buenos Aires (EZE)', 'destino' => 'Santiago de Chile (SCL)', 'dias' => 5, 'hora' => '19:45:00', 'precio' => 145000.00, 'asientos' => 25],
    ['iata' => 'LA', 'origen' => 'Santiago de Chile (SCL)', 'destino' => 'Buenos Aires (EZE)', 'dias' => 10, 'hora' => '14:20:00', 'precio' => 145000.00, 'asientos' => 30],
    ['iata' => 'LA', 'origen' => 'Buenos Aires (EZE)', 'destino' => 'Río de Janeiro (GIG)', 'dias' => 7, 'hora' => '06:30:00', 'precio' => 195000.00, 'asientos' => 32],
    ['iata' => 'LA', 'origen' => 'Buenos Aires (EZE)', 'destino' => 'San Pablo (GRU)', 'dias' => 8, 'hora' => '11:15:00', 'precio' => 180000.00, 'asientos' => 28],
    ['iata' => 'LA', 'origen' => 'Córdoba (COR)', 'destino' => 'Santiago de Chile (SCL)', 'dias' => 12, 'hora' => '16:00:00', 'precio' => 160000.00, 'asientos' => 22],

    // JetSMART (WJ)
    ['iata' => 'WJ', 'origen' => 'Buenos Aires (AEP)', 'destino' => 'Iguazú (IGR)', 'dias' => 3, 'hora' => '14:00:00', 'precio' => 59000.00, 'asientos' => 42],
    ['iata' => 'WJ', 'origen' => 'Buenos Aires (AEP)', 'destino' => 'Salta (SLA)', 'dias' => 7, 'hora' => '10:30:00', 'precio' => 64000.00, 'asientos' => 40],
    ['iata' => 'WJ', 'origen' => 'Córdoba (COR)', 'destino' => 'Bariloche (BRC)', 'dias' => 11, 'hora' => '13:20:00', 'precio' => 89000.00, 'asientos' => 36],
    ['iata' => 'WJ', 'origen' => 'Rosario (ROS)', 'destino' => 'Mendoza (MDZ)', 'dias' => 14, 'hora' => '09:40:00', 'precio' => 72000.00, 'asientos' => 38],

    // Copa Airlines (CM)
    ['iata' => 'CM', 'origen' => 'Buenos Aires (EZE)', 'destino' => 'Panamá (PTY)', 'dias' => 15, 'hora' => '01:30:00', 'precio' => 320000.00, 'asientos' => 20],
    ['iata' => 'CM', 'origen' => 'Rosario (ROS)', 'destino' => 'Panamá (PTY)', 'dias' => 18, 'hora' => '02:45:00', 'precio' => 340000.00, 'asientos' => 18],

    // Vuelo pasado para testing de histórico
    ['iata' => 'ARG', 'origen' => 'Rosario (ROS)', 'destino' => 'Buenos Aires (AEP)', 'dias' => -5, 'hora' => '08:30:00', 'precio' => 42000.00, 'asientos' => 10],
];

$stmtCheckVuelo = $db->prepare("SELECT codVuelo FROM vuelos WHERE codAerolinea = ? AND origenVuelo = ? AND destinoVuelo = ? AND fechaSalidaVuelo = DATE_ADD(CURRENT_DATE, INTERVAL ? DAY)");
$stmtInsertVuelo = $db->prepare("INSERT INTO vuelos (codAerolinea, origenVuelo, destinoVuelo, fechaSalidaVuelo, horaSalidaVuelo, precioVuelo, asientosDisponibles) VALUES (?, ?, ?, DATE_ADD(CURRENT_DATE, INTERVAL ? DAY), ?, ?, ?)");
$stmtUpdateVuelo = $db->prepare("UPDATE vuelos SET horaSalidaVuelo = ?, precioVuelo = ?, asientosDisponibles = ? WHERE codVuelo = ?");

$vueloIds = [];
$vueloIndex = 1;
foreach ($vuelos as $v) {
    if (!isset($aeroIds[$v['iata']])) continue;
    $aId = $aeroIds[$v['iata']];

    $stmtCheckVuelo->execute([$aId, $v['origen'], $v['destino'], $v['dias']]);
    $existingId = $stmtCheckVuelo->fetchColumn();

    if ($existingId) {
        $stmtUpdateVuelo->execute([$v['hora'], $v['precio'], $v['asientos'], $existingId]);
        $vueloIds[] = (int)$existingId;
        echo "   [UPDATE] Vuelo #{$existingId}: {$v['iata']} {$v['origen']} -> {$v['destino']} ({$v['hora']}, \${$v['precio']})\n";
    } else {
        $stmtInsertVuelo->execute([$aId, $v['origen'], $v['destino'], $v['dias'], $v['hora'], $v['precio'], $v['asientos']]);
        $newId = (int)$db->lastInsertId();
        $vueloIds[] = $newId;
        echo "   [INSERT] Vuelo #{$newId}: {$v['iata']} {$v['origen']} -> {$v['destino']} ({$v['hora']}, \${$v['precio']})\n";
    }
}

// -------------------------------------------------------------
// 5. SEED DE NOVEDADES
// -------------------------------------------------------------
echo "\n--> 5. Sembrando Novedades Institucionales...\n";

$adminId = $userIds['admin@aeroreserva.com'] ?? 1;
$novedades = [
    [
        'texto' => 'Cóndor AeroReserva incorpora nuevas rutas directas hacia Bariloche y Ushuaia para la temporada alta de verano.',
        'dias_exp' => 60,
    ],
    [
        'texto' => 'Alianza estratégica con JetSMART para sumar más frecuencias diarias entre Rosario y Buenos Aires.',
        'dias_exp' => 45,
    ],
    [
        'texto' => 'Beneficio exclusivo: 1 equipaje de mano sin costo adicional para todas las reservas emitidas online.',
        'dias_exp' => 90,
    ],
    [
        'texto' => 'Nuevos métodos de pago y financiación en hasta 6 cuotas con tarjetas bancarias seleccionadas.',
        'dias_exp' => 30,
    ],
];

$stmtCheckNov = $db->prepare("SELECT codNovedad FROM novedades WHERE textoNovedad = ?");
$stmtInsertNov = $db->prepare("INSERT INTO novedades (textoNovedad, fechaPublicacionNovedad, fechaExpiracionNovedad, codUsuarioAdmin) VALUES (?, CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL ? DAY), ?)");
$stmtUpdateNov = $db->prepare("UPDATE novedades SET fechaPublicacionNovedad = CURRENT_DATE, fechaExpiracionNovedad = DATE_ADD(CURRENT_DATE, INTERVAL ? DAY), codUsuarioAdmin = ? WHERE codNovedad = ?");

foreach ($novedades as $n) {
    $stmtCheckNov->execute([$n['texto']]);
    $existingId = $stmtCheckNov->fetchColumn();

    if ($existingId) {
        $stmtUpdateNov->execute([$n['dias_exp'], $adminId, $existingId]);
        echo "   [UPDATE] Novedad #{$existingId}: \"{$n['texto']}\"\n";
    } else {
        $stmtInsertNov->execute([$n['texto'], $n['dias_exp'], $adminId]);
        $newId = (int)$db->lastInsertId();
        echo "   [INSERT] Novedad #{$newId}: \"{$n['texto']}\"\n";
    }
}

// -------------------------------------------------------------
// 6. SEED DE RESERVAS (Estados variados)
// -------------------------------------------------------------
echo "\n--> 6. Sembrando Reservas de Prueba...\n";

$marianaId = $userIds['mariana@gmail.com'] ?? null;
$nataliaId = $userIds['franconatalia222@gmail.com'] ?? null;
$juanId    = $userIds['juan.perez@gmail.com'] ?? null;
$lucasId   = $userIds['lucas.rodriguez@gmail.com'] ?? null;

$promoPrimaveraId = $promoIds['30% Off en vuelos de primavera'] ?? null;
$promoFlybondiId  = $promoIds['15% Off primera compra Flybondi'] ?? null;

if (!empty($vueloIds) && $marianaId) {
    $vuelo1 = $vueloIds[0];
    $vuelo2 = $vueloIds[2] ?? $vuelo1;
    $vuelo3 = $vueloIds[8] ?? $vuelo1;
    $vuelo4 = $vueloIds[10] ?? $vuelo1;

    $reservas = [
        [
            'usuario_id' => $marianaId,
            'vuelo_id' => $vuelo1,
            'promo_id' => $promoPrimaveraId,
            'precio' => 31500.00, // 45000 - 30%
            'estado' => 'confirmada',
        ],
        [
            'usuario_id' => $marianaId,
            'vuelo_id' => $vuelo2,
            'promo_id' => null,
            'precio' => 98000.00,
            'estado' => 'pendiente de pago', // Para que Mariana pueda pagar en "Mis Reservas"
        ],
        [
            'usuario_id' => $juanId ?: $marianaId,
            'vuelo_id' => $vuelo3,
            'promo_id' => $promoFlybondiId,
            'precio' => 32300.00, // 38000 - 15%
            'estado' => 'confirmada',
        ],
        [
            'usuario_id' => $juanId ?: $marianaId,
            'vuelo_id' => $vuelo4,
            'promo_id' => null,
            'precio' => 54000.00,
            'estado' => 'cancelada', // Para historial
        ],
        [
            'usuario_id' => $nataliaId ?: $marianaId,
            'vuelo_id' => $vuelo1,
            'promo_id' => null,
            'precio' => 45000.00,
            'estado' => 'confirmada',
        ],
        [
            'usuario_id' => $lucasId ?: $marianaId,
            'vuelo_id' => $vuelo2,
            'promo_id' => null,
            'precio' => 98000.00,
            'estado' => 'pendiente de pago',
        ],
    ];

    $stmtCheckRes = $db->prepare("SELECT codReserva FROM reservas WHERE codUsuario = ? AND codVuelo = ? AND estadoReserva = ?");
    $stmtInsertRes = $db->prepare("INSERT INTO reservas (codUsuario, codVuelo, codPromocion, fechaReserva, precioFinal, estadoReserva) VALUES (?, ?, ?, NOW(), ?, ?)");

    foreach ($reservas as $r) {
        $stmtCheckRes->execute([$r['usuario_id'], $r['vuelo_id'], $r['estado']]);
        $existingId = $stmtCheckRes->fetchColumn();
        if (!$existingId) {
            $stmtInsertRes->execute([$r['usuario_id'], $r['vuelo_id'], $r['promo_id'], $r['precio'], $r['estado']]);
            $resId = (int)$db->lastInsertId();
            echo "   [INSERT] Reserva #{$resId}: Usuario ID {$r['usuario_id']} en Vuelo #{$r['vuelo_id']} (\${$r['precio']}, {$r['estado']})\n";
        } else {
            echo "   [EXISTE] Reserva #{$existingId} ya sembrada.\n";
        }
    }
}

// -------------------------------------------------------------
// RESUMEN FINAL
// -------------------------------------------------------------
echo "\n========================================================\n";
echo "    RESUMEN DEL SEEDING:\n";
echo "========================================================\n";
foreach (['usuarios', 'aerolineas', 'promociones', 'vuelos', 'novedades', 'reservas'] as $tabla) {
    $count = $db->query("SELECT COUNT(*) FROM {$tabla}")->fetchColumn();
    echo "  * {$tabla}: {$count} registros\n";
}
echo "========================================================\n";
echo "[OK] ¡SEEDING COMPLETADO EXITOSAMENTE!\n";
