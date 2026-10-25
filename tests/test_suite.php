<?php
declare(strict_types=1);

/**
 * Suite de Pruebas Automatizadas para Cóndor AeroReserva (TPI Entornos Gráficos 2026).
 * Incluye pruebas unitarias de helpers, pruebas de integración con MySQL/TiDB Cloud,
 * pruebas de reglas de negocio y pruebas HTTP End-to-End con manejo de cookies y CSRF.
 */

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/helpers/Database.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/datos.php';
require_once __DIR__ . '/../app/helpers/paginador.php';

class TestRunner
{
    private int $passed = 0;
    private int $failed = 0;
    private array $failures = [];
    private string $baseUrl;

    public function __construct(string $baseUrl = 'http://127.0.0.1:8000')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function assert(bool $condition, string $testName, string $details = ''): void
    {
        if ($condition) {
            $this->passed++;
            echo "  [PASS] {$testName}\n";
        } else {
            $this->failed++;
            $msg = "  [FAIL] {$testName}" . ($details ? " -> {$details}" : "");
            $this->failures[] = $msg;
            echo "{$msg}\n";
        }
    }

    public function assertEqual($expected, $actual, string $testName): void
    {
        $condition = ($expected === $actual);
        $details = $condition ? '' : "Esperado: " . var_export($expected, true) . ", Obtenido: " . var_export($actual, true);
        $this->assert($condition, $testName, $details);
    }

    public function assertContains(string $needle, string $haystack, string $testName): void
    {
        $condition = str_contains($haystack, $needle);
        $details = $condition ? '' : "No se encontró el texto '{$needle}' en la respuesta";
        $this->assert($condition, $testName, $details);
    }

    public function httpGet(string $path, ?string $cookieJar = null): array
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        if ($cookieJar) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        curl_close($ch);

        return [
            'code' => $httpCode,
            'headers' => $headers,
            'body' => $body,
        ];
    }

    public function httpPost(string $path, array $data, ?string $cookieJar = null): array
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        if ($cookieJar) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        curl_close($ch);

        return [
            'code' => $httpCode,
            'headers' => $headers,
            'body' => $body,
        ];
    }

    public function extractCsrfToken(string $html): ?string
    {
        if (preg_match('/name=["\']csrf_token["\']\s+value=["\']([a-f0-9]{64})["\']/i', $html, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function printSummary(): void
    {
        echo "\n========================================================\n";
        echo "              RESUMEN DE RESULTADOS DE TESTS            \n";
        echo "========================================================\n";
        echo "  Total de pruebas ejecutadas: " . ($this->passed + $this->failed) . "\n";
        echo "  [PASS] Pruebas exitosas:    {$this->passed}\n";
        echo "  [FAIL] Pruebas fallidas:    {$this->failed}\n";

        if ($this->failed > 0) {
            echo "\nDetalle de fallos:\n";
            foreach ($this->failures as $f) {
                echo "  - {$f}\n";
            }
            echo "========================================================\n";
            exit(1);
        } else {
            echo "========================================================\n";
            echo "[ÉXITO TOTAL] ¡Todas las pruebas pasaron satisfactoriamente!\n";
        }
    }
}

$runner = new TestRunner();

echo "========================================================\n";
echo "    INICIANDO TEST SUITE - CÓNDOR AERORESERVA (2026)    \n";
echo "========================================================\n\n";

// -------------------------------------------------------------
// SECCIÓN 1: PRUEBAS DE CONEXIÓN Y BASE DE DATOS
// -------------------------------------------------------------
echo "\n--> [SECCIÓN 1] Conexión y Modelo de Datos (MySQL / TiDB Cloud)...\n";
try {
    $db = Database::getConnection();
    $runner->assert($db instanceof PDO, "Database::getConnection() retorna una instancia válida de PDO");

    $db2 = Database::getConnection();
    $runner->assert($db === $db2, "Patrón Singleton en Database asegura la misma instancia");

    $res = $db->query("SELECT 1")->fetchColumn();
    $runner->assertEqual("1", (string)$res, "Consulta SELECT 1 de prueba responde correctamente");
} catch (Throwable $e) {
    $runner->assert(false, "Excepción en conexión a base de datos", $e->getMessage());
}

// -------------------------------------------------------------
// SECCIÓN 2: PRUEBAS DE SEGURIDAD Y CSRF
// -------------------------------------------------------------
echo "\n--> [SECCIÓN 2] Seguridad Criptográfica y Helpers CSRF...\n";

$token = obtenerTokenCsrf();
$runner->assert(!empty($token) && strlen($token) === 64, "obtenerTokenCsrf() genera token hexadecimal de 64 caracteres (32 bytes)");

$valido = validarTokenCsrf($token);
$runner->assert($valido === true, "validarTokenCsrf() valida positivamente el token de sesión legítimo");

$invalido = validarTokenCsrf('token_falso_1234567890abcdef1234567890abcdef1234567890abcdef1234567890');
$runner->assert($invalido === false, "validarTokenCsrf() rechaza token adulterado o falsificado");

$vacio = validarTokenCsrf(null);
$runner->assert($vacio === false, "validarTokenCsrf() rechaza token nulo o vacío");

$markup = campoCsrf();
$runner->assertContains('name="csrf_token"', $markup, "campoCsrf() genera input oculto con name='csrf_token'");
$runner->assertContains($token, $markup, "campoCsrf() contiene el valor exacto del token en el HTML");

// -------------------------------------------------------------
// SECCIÓN 3: PRUEBAS DE AUTENTICACIÓN Y SESIONES
// -------------------------------------------------------------
echo "\n--> [SECCIÓN 3] Autenticación y Control de Sesión...\n";

// 3.1 Login exitoso Administrador
$userAdmin = autenticarUsuario('admin@aeroreserva.com', 'admin123');
$runner->assert($userAdmin !== null, "autenticarUsuario() valida credenciales correctas de Administrador");
if ($userAdmin) {
    $runner->assertEqual('administrador', $userAdmin['tipoUsuario'], "Tipo de usuario autenticado es 'administrador'");
    $runner->assertEqual('activo', $userAdmin['estadoUsuario'], "Estado del Administrador es 'activo'");
}

// 3.2 Login exitoso CEO
$userCeo = autenticarUsuario('ceo.ar@aeroreserva.com', 'ceo123');
$runner->assert($userCeo !== null, "autenticarUsuario() valida credenciales de CEO");
if ($userCeo) {
    $runner->assertEqual('ceo de aerolínea', $userCeo['tipoUsuario'], "Tipo de usuario CEO es 'ceo de aerolínea'");
}

// 3.3 Login exitoso Pasajero
$userPasajero = autenticarUsuario('mariana@gmail.com', 'pasajero123');
$runner->assert($userPasajero !== null, "autenticarUsuario() valida credenciales de Pasajero");
if ($userPasajero) {
    $runner->assertEqual('usuario', $userPasajero['tipoUsuario'], "Tipo de usuario pasajero es 'usuario'");
}

// 3.4 Contraseña incorrecta
$userInvalido = autenticarUsuario('admin@aeroreserva.com', 'clave_totalmente_erronea');
$runner->assert($userInvalido === null, "autenticarUsuario() rechaza contraseña incorrecta y retorna null");

// 3.5 Usuario inexistente
$userInexistente = autenticarUsuario('noexiste_jamas@dominio.com', 'cualquiera');
$runner->assert($userInexistente === null, "autenticarUsuario() rechaza correo inexistente y retorna null");

// 3.6 Manejo de sesión y roles
if ($userAdmin) {
    iniciarSesionUsuario($userAdmin);
    $runner->assert(usuarioLogueado() === true, "usuarioLogueado() devuelve true tras iniciar sesión");
    $runner->assert(esAdmin() === true, "esAdmin() devuelve true para administrador");
    $runner->assert(esCeo() === false, "esCeo() devuelve false para administrador");
    $runner->assert(esPasajero() === false, "esPasajero() devuelve false para administrador");
}

if ($userCeo) {
    iniciarSesionUsuario($userCeo);
    $runner->assert(esCeo() === true, "esCeo() devuelve true para CEO");
    $runner->assert(esAdmin() === false, "esAdmin() devuelve false para CEO");
}

if ($userPasajero) {
    iniciarSesionUsuario($userPasajero);
    $runner->assert(esPasajero() === true, "esPasajero() devuelve true para Pasajero");
    $runner->assert(esAdmin() === false, "esAdmin() devuelve false para Pasajero");
}

cerrarSesion();
$runner->assert(usuarioLogueado() === false, "usuarioLogueado() devuelve false tras cerrarSesion()");

// -------------------------------------------------------------
// SECCIÓN 4: PRUEBAS DE CONSULTA Y NEGOCIO (app/helpers/datos.php)
// -------------------------------------------------------------
echo "\n--> [SECCIÓN 4] Consultas y Reglas de Negocio en datos.php...\n";

// 4.1 Vuelos y Filtros
$totalVuelos = contarTotalVuelos();
$runner->assert($totalVuelos > 0, "contarTotalVuelos() retorna cantidad mayor a 0 (Total: {$totalVuelos})");

$vuelosRosario = contarTotalVuelos('Rosario');
$runner->assert($vuelosRosario > 0, "contarTotalVuelos('Rosario') filtra vuelos con origen Rosario ({$vuelosRosario} vuelos)");

$vuelosPaginados = obtenerVuelos(null, null, null, null, null, 'precio_asc', 4, 0);
$runner->assert(count($vuelosPaginados) <= 4, "obtenerVuelos() respeta el límite de paginación (4 por página)");

// Verificar orden de menor a mayor precio
if (count($vuelosPaginados) >= 2) {
    $precioAscOk = true;
    for ($i = 0; $i < count($vuelosPaginados) - 1; $i++) {
        if ((float)$vuelosPaginados[$i]['precioVuelo'] > (float)$vuelosPaginados[$i + 1]['precioVuelo']) {
            $precioAscOk = false;
            break;
        }
    }
    $runner->assert($precioAscOk, "obtenerVuelos() con orden 'precio_asc' ordena correctamente los precios");
}

// 4.2 Aerolíneas
$aerolineas = obtenerAerolineas();
$runner->assert(count($aerolineas) >= 4, "obtenerAerolineas() retorna lista completa de aerolíneas (Total: " . count($aerolineas) . ")");
$primerAero = $aerolineas[0];
$runner->assert(isset($primerAero['totalVuelos']), "Aerolínea incluye cálculo de subconsulta 'totalVuelos'");
$runner->assert(isset($primerAero['nombreCeo']), "Aerolínea incluye LEFT JOIN con nombre de CEO");

// 4.3 Promociones
$promosAprobadas = obtenerPromocionesAprobadas();
$runner->assert(!empty($promosAprobadas), "obtenerPromocionesAprobadas() retorna promociones activas (Total: " . count($promosAprobadas) . ")");
foreach ($promosAprobadas as $p) {
    if ($p['estadoPromocion'] !== 'aprobada') {
        $runner->assert(false, "Promoción aprobada no debe tener estado distinto a 'aprobada'");
        break;
    }
}
$runner->assert(true, "Todas las promociones retornadas por obtenerPromocionesAprobadas() tienen estado 'aprobada'");

$todasPromos = obtenerTodasPromociones();
$runner->assert(count($todasPromos) >= count($promosAprobadas), "obtenerTodasPromociones() incluye promociones en cualquier estado (Total: " . count($todasPromos) . ")");

// 4.4 Novedades
$novedadesVigentes = obtenerNovedadesVigentes();
$runner->assert(!empty($novedadesVigentes), "obtenerNovedadesVigentes() retorna novedades vigentes (Total: " . count($novedadesVigentes) . ")");

// 4.5 Transacción de Reserva y Decremento de Cupos
$vueloTest = $vuelosPaginados[0] ?? null;
if ($vueloTest) {
    $vId = (int)$vueloTest['codVuelo'];
    $asientosAntes = (int)$db->query("SELECT asientosDisponibles FROM vuelos WHERE codVuelo = {$vId}")->fetchColumn();

    // Crear reserva de prueba para Mariana
    $marianaId = (int)$db->query("SELECT codUsuario FROM usuarios WHERE emailUsuario = 'mariana@gmail.com'")->fetchColumn();
    $resId = crearReserva($marianaId, $vId, null, 45000.00);

    $runner->assert($resId !== null && $resId > 0, "crearReserva() crea la reserva exitosamente dentro de una transacción (ID: {$resId})");

    $asientosDespues = (int)$db->query("SELECT asientosDisponibles FROM vuelos WHERE codVuelo = {$vId}")->fetchColumn();
    $runner->assertEqual($asientosAntes - 1, $asientosDespues, "crearReserva() decrementa exactamente 1 asiento disponible en el vuelo");

    // Confirmar pago
    $pagoOk = confirmarPagoReserva($resId, $marianaId);
    $runner->assert($pagoOk === true, "confirmarPagoReserva() transiciona el estado a 'confirmada'");

    $estadoFinal = $db->query("SELECT estadoReserva FROM reservas WHERE codReserva = {$resId}")->fetchColumn();
    $runner->assertEqual('confirmada', $estadoFinal, "Estado de la reserva en base de datos quedó en 'confirmada'");
}

// 4.6 Regla de Negocio #13: Cancelación de Reservas y Regla de las 72 Horas
// Creamos un vuelo lejano (a 10 días) para probar cancelación exitosa
$db->query("INSERT INTO vuelos (codAerolinea, origenVuelo, destinoVuelo, fechaSalidaVuelo, horaSalidaVuelo, precioVuelo, asientosDisponibles)
            VALUES (1, 'Test ROS', 'Test BRC', DATE_ADD(CURRENT_DATE, INTERVAL 10 DAY), '12:00:00', 50000.00, 20)");
$vueloLejanoId = (int)$db->lastInsertId();
$asientosLejanoAntes = 20;

$resLejanaId = crearReserva($marianaId, $vueloLejanoId, null, 50000.00);
$runner->assert($resLejanaId !== null, "crearReserva() en vuelo futuro a 10 días creada con éxito");

// Cancelar reserva futura (> 72hs)
$cancelRes = cancelarReserva($resLejanaId, $marianaId);
$runner->assert($cancelRes['ok'] === true, "cancelarReserva() permite cancelar con más de 72 horas de anticipación");

$asientosLejanoDespues = (int)$db->query("SELECT asientosDisponibles FROM vuelos WHERE codVuelo = {$vueloLejanoId}")->fetchColumn();
$runner->assertEqual($asientosLejanoAntes, $asientosLejanoDespues, "cancelarReserva() re-incrementa el asiento disponible en el vuelo");

// Ahora probamos vuelo inminente (< 72hs, e.g. mañana)
$db->query("INSERT INTO vuelos (codAerolinea, origenVuelo, destinoVuelo, fechaSalidaVuelo, horaSalidaVuelo, precioVuelo, asientosDisponibles)
            VALUES (1, 'Test ROS Inminente', 'Test AEP', DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY), '12:00:00', 40000.00, 15)");
$vueloInminenteId = (int)$db->lastInsertId();
$resInminenteId = crearReserva($marianaId, $vueloInminenteId, null, 40000.00);

$cancelInminente = cancelarReserva($resInminenteId, $marianaId);
$runner->assert($cancelInminente['ok'] === false, "cancelarReserva() BLOQUEA la cancelación si faltan menos de 72 horas (Regla #13)");
$runner->assertContains("72 horas", $cancelInminente['mensaje'], "Mensaje de rechazo explicita la restricción de 72 horas");

// Limpiar registros de prueba temporales
$db->query("DELETE FROM reservas WHERE codReserva IN ({$resLejanaId}, {$resInminenteId})");
$db->query("DELETE FROM vuelos WHERE codVuelo IN ({$vueloLejanoId}, {$vueloInminenteId})");

// 4.7 Métricas Admin y CEO
$metricasAdmin = obtenerMetricasAdmin();
$runner->assert($metricasAdmin['totalAerolineas'] >= 4, "obtenerMetricasAdmin() reporta total de aerolíneas correcto");
$runner->assert($metricasAdmin['totalVuelos'] >= 20, "obtenerMetricasAdmin() reporta catálogo amplio de vuelos");
$runner->assert($metricasAdmin['ventasConfirmadas'] > 0, "obtenerMetricasAdmin() contabiliza reservas confirmadas");

// 4.8 Paginador Helper
$htmlPaginador = renderizarPaginador(3, 10, 'vuelos', ['origen' => 'Rosario']);
$runner->assertContains('pagina=vuelos', $htmlPaginador, "renderizarPaginador() genera enlaces con el parámetro 'pagina=vuelos'");
$runner->assertContains('p=1', $htmlPaginador, "renderizarPaginador() incluye enlace a la primera página");
$runner->assertContains('p=2', $htmlPaginador, "renderizarPaginador() incluye enlace a página anterior");
$runner->assertContains('<li class="page-item active" aria-current="page"><span class="page-link fw-bold">3</span></li>', $htmlPaginador, "renderizarPaginador() marca la página actual (3) como activa");
$runner->assertContains('&laquo; Anterior', $htmlPaginador, "renderizarPaginador() incluye botón Anterior");
$runner->assertContains('Siguiente &raquo;', $htmlPaginador, "renderizarPaginador() incluye botón Siguiente");

// -------------------------------------------------------------
// SECCIÓN 5: PRUEBAS HTTP END-TO-END (Web Server en 127.0.0.1:8000)
// -------------------------------------------------------------
echo "\n--> [SECCIÓN 5] Pruebas HTTP End-to-End contra el Servidor Web...\n";

// 5.1 Rutas Públicas (HTTP 200 OK)
$rutasPublicas = [
    'Home' => 'index.php',
    'Vuelos' => 'index.php?pagina=vuelos',
    'Aerolineas' => 'index.php?pagina=aerolineas',
    'Promociones' => 'index.php?pagina=promociones',
    'Novedades' => 'index.php?pagina=novedades',
    'Contacto' => 'index.php?pagina=contacto',
    'Login' => 'index.php?pagina=login',
    'Registro' => 'index.php?pagina=registro',
    'Recuperar Clave' => 'index.php?pagina=recuperar_clave',
    'Terminos' => 'index.php?pagina=terminos',
    'Mapa del Sitio' => 'index.php?pagina=mapa_sitio',
];

foreach ($rutasPublicas as $nombre => $ruta) {
    $resp = $runner->httpGet($ruta);
    $runner->assertEqual(200, $resp['code'], "Ruta pública '{$nombre}' ({$ruta}) responde HTTP 200 OK");
}

// 5.2 Protección de Rutas Privadas (Redirección 302 hacia login sin sesión)
$rutasProtegidas = [
    'Perfil' => 'index.php?pagina=perfil',
    'Mis Reservas' => 'index.php?pagina=reservas',
    'Historial' => 'index.php?pagina=historial',
    'Panel Admin' => 'index.php?pagina=admin_panel',
    'Admin Usuarios' => 'index.php?pagina=admin_usuarios',
    'Admin Aerolíneas' => 'index.php?pagina=admin_aerolineas',
    'Admin Promociones' => 'index.php?pagina=admin_promociones',
    'Admin Novedades' => 'index.php?pagina=admin_novedades',
    'Admin Reportes' => 'index.php?pagina=admin_reportes',
    'Panel CEO' => 'index.php?pagina=ceo_panel',
    'CEO Vuelos' => 'index.php?pagina=ceo_vuelos',
    'CEO Promociones' => 'index.php?pagina=ceo_promociones',
    'CEO Reportes' => 'index.php?pagina=ceo_reportes',
];

foreach ($rutasProtegidas as $nombre => $ruta) {
    $resp = $runner->httpGet($ruta);
    $runner->assertEqual(302, $resp['code'], "Ruta protegida '{$nombre}' sin sesión responde HTTP 302 (Redirección)");
    $runner->assertContains('Location: index.php?pagina=login', $resp['headers'], "Ruta protegida '{$nombre}' redirige al Login");
}

// -------------------------------------------------------------
// SECCIÓN 6: FLUJOS COMPLETOS DE SESIÓN Y CONTROL DE ACCESO (E2E)
// -------------------------------------------------------------
echo "\n--> [SECCIÓN 6] Flujos Completos de Autenticación y RBAC...\n";

$cookieJarAdmin = tempnam(sys_get_temp_dir(), 'condor_admin_');
$cookieJarCeo   = tempnam(sys_get_temp_dir(), 'condor_ceo_');
$cookieJarUser  = tempnam(sys_get_temp_dir(), 'condor_user_');

// 6.1 Flujo de Administrador
// Obtener página de login para capturar token CSRF y cookie de sesión
$loginGet = $runner->httpGet('index.php?pagina=login', $cookieJarAdmin);
$csrfToken = $runner->extractCsrfToken($loginGet['body']);
$runner->assert(!empty($csrfToken), "Token CSRF extraído exitosamente del formulario de login de Cóndor");

// Login POST de Administrador
$loginPostAdmin = $runner->httpPost('index.php', [
    'accion' => 'login_credenciales',
    'csrf_token' => $csrfToken,
    'email' => 'admin@aeroreserva.com',
    'clave' => 'admin123',
], $cookieJarAdmin);

$runner->assertEqual(302, $loginPostAdmin['code'], "Login de Administrador responde 302 Redirección");
$runner->assertContains('Location: index.php?pagina=admin_panel', $loginPostAdmin['headers'], "Login de Admin redirige a 'admin_panel'");

// Acceso al panel de Admin con la cookie de sesión establecida
$adminPanelGet = $runner->httpGet('index.php?pagina=admin_panel', $cookieJarAdmin);
$runner->assertEqual(200, $adminPanelGet['code'], "Acceso autenticado a admin_panel responde HTTP 200 OK");
$runner->assertContains('Panel de Administración', $adminPanelGet['body'], "Contenido de admin_panel muestra el título de administración");

// Acceso a submódulos de Admin
$adminUsuariosGet = $runner->httpGet('index.php?pagina=admin_usuarios', $cookieJarAdmin);
$runner->assertEqual(200, $adminUsuariosGet['code'], "Admin accede a gestión de usuarios (HTTP 200 OK)");

$adminAeroGet = $runner->httpGet('index.php?pagina=admin_aerolineas', $cookieJarAdmin);
$runner->assertEqual(200, $adminAeroGet['code'], "Admin accede a gestión de aerolíneas (HTTP 200 OK)");

// 6.2 Flujo de CEO de Aerolínea
$loginGetCeo = $runner->httpGet('index.php?pagina=login', $cookieJarCeo);
$csrfCeo = $runner->extractCsrfToken($loginGetCeo['body']);

$loginPostCeo = $runner->httpPost('index.php', [
    'accion' => 'login_credenciales',
    'csrf_token' => $csrfCeo,
    'email' => 'ceo.ar@aeroreserva.com',
    'clave' => 'ceo123',
], $cookieJarCeo);

$runner->assertEqual(302, $loginPostCeo['code'], "Login de CEO responde 302 Redirección");
$runner->assertContains('Location: index.php?pagina=ceo_panel', $loginPostCeo['headers'], "Login de CEO redirige a 'ceo_panel'");

$ceoPanelGet = $runner->httpGet('index.php?pagina=ceo_panel', $cookieJarCeo);
$runner->assertEqual(200, $ceoPanelGet['code'], "Acceso autenticado a ceo_panel responde HTTP 200 OK");

$ceoVuelosGet = $runner->httpGet('index.php?pagina=ceo_vuelos', $cookieJarCeo);
$runner->assertEqual(200, $ceoVuelosGet['code'], "CEO accede a gestión de vuelos propios (HTTP 200 OK)");

// Intento de CEO accediendo a admin_panel -> Debe ser bloqueado
$ceoAccessAdmin = $runner->httpGet('index.php?pagina=admin_panel', $cookieJarCeo);
$runner->assertEqual(302, $ceoAccessAdmin['code'], "CEO intentando acceder a admin_panel es bloqueado y redirigido (HTTP 302)");

// 6.3 Flujo de Pasajero
$loginGetUser = $runner->httpGet('index.php?pagina=login', $cookieJarUser);
$csrfUser = $runner->extractCsrfToken($loginGetUser['body']);

$loginPostUser = $runner->httpPost('index.php', [
    'accion' => 'login_credenciales',
    'csrf_token' => $csrfUser,
    'email' => 'mariana@gmail.com',
    'clave' => 'pasajero123',
], $cookieJarUser);

$runner->assertEqual(302, $loginPostUser['code'], "Login de Pasajero responde 302 Redirección");
$runner->assertContains('Location: index.php?pagina=reservas', $loginPostUser['headers'], "Login de Pasajero redirige a 'reservas'");

$userPerfilGet = $runner->httpGet('index.php?pagina=perfil', $cookieJarUser);
$runner->assertEqual(200, $userPerfilGet['code'], "Pasajero accede a su perfil (HTTP 200 OK)");

$userReservasGet = $runner->httpGet('index.php?pagina=reservas', $cookieJarUser);
$runner->assertEqual(200, $userReservasGet['code'], "Pasajero accede a sus reservas (HTTP 200 OK)");

// Intento de Pasajero accediendo a paneles de CEO o Admin -> Debe ser bloqueado
$userAccessCeo = $runner->httpGet('index.php?pagina=ceo_panel', $cookieJarUser);
$runner->assertEqual(302, $userAccessCeo['code'], "Pasajero intentando acceder a ceo_panel es bloqueado (HTTP 302)");

$userAccessAdmin = $runner->httpGet('index.php?pagina=admin_panel', $cookieJarUser);
$runner->assertEqual(302, $userAccessAdmin['code'], "Pasajero intentando acceder a admin_panel es bloqueado (HTTP 302)");

// 6.4 Intento de Login de Usuario Suspendido / Denegado
$loginGetSusp = $runner->httpGet('index.php?pagina=login');
$csrfSusp = $runner->extractCsrfToken($loginGetSusp['body']);

$loginPostSusp = $runner->httpPost('index.php', [
    'accion' => 'login_credenciales',
    'csrf_token' => $csrfSusp,
    'email' => 'usuario.suspendido@gmail.com',
    'clave' => 'pasajero123',
]);

$runner->assertEqual(302, $loginPostSusp['code'], "Login de usuario suspendido rechaza el acceso con 302 a login");

// 6.5 Ataque de CSRF: Envío sin token CSRF válido
$loginPostAtaque = $runner->httpPost('index.php', [
    'accion' => 'login_credenciales',
    'csrf_token' => 'token_malicioso_inventado',
    'email' => 'admin@aeroreserva.com',
    'clave' => 'admin123',
]);
$runner->assertEqual(302, $loginPostAtaque['code'], "Envío con token CSRF inválido es bloqueado de inmediato por el Front Controller");

// Limpiar archivos temporales de cookies
@unlink($cookieJarAdmin);
@unlink($cookieJarCeo);
@unlink($cookieJarUser);

// -------------------------------------------------------------
// IMPRESIÓN DEL RESUMEN FINAL
// -------------------------------------------------------------
$runner->printSummary();
