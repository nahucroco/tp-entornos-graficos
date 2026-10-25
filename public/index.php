<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/datos.php';
require_once __DIR__ . '/../app/helpers/mailer.php';

if (isset($_GET['salir'])) {
    cerrarSesion();
    header('Location: index.php');
    exit;
}

// Gestión de Cookies de Preferencias (Accesibilidad / Alto Contraste según Cátedra EG)
if (isset($_GET['cambiar_contraste'])) {
    $modoActual = $_COOKIE['modo_contraste'] ?? 'normal';
    $nuevoModo = ($modoActual === 'alto') ? 'normal' : 'alto';
    setcookie('modo_contraste', $nuevoModo, time() + (86400 * 90), '/');
    $_COOKIE['modo_contraste'] = $nuevoModo;
    $volver = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header('Location: ' . $volver);
    exit;
}

// ==========================================
// PROCESAMIENTO DE FORMULARIOS (POST)
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokenCsrf = $_POST['csrf_token'] ?? null;
    if (!validarTokenCsrf($tokenCsrf)) {
        $_SESSION['mensaje'] = 'La sesión del formulario expiró o el token de seguridad es inválido (Protección CSRF activa). Por favor, intentá nuevamente.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
        exit;
    }

    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        // --- AUTENTICACIÓN Y CUENTAS ---
        case 'login_credenciales':
            $email = trim($_POST['email'] ?? '');
            $clave = trim($_POST['clave'] ?? '');
            $user = autenticarUsuario($email, $clave);

            if ($user) {
                if ($user['estadoUsuario'] !== 'activo') {
                    $_SESSION['mensaje'] = $user['estadoUsuario'] === 'pendiente'
                        ? 'Tu solicitud de cuenta está pendiente de aprobación.'
                        : 'Tu cuenta ha sido suspendida por el administrador.';
                    $_SESSION['tipo_mensaje'] = 'danger';
                    header('Location: index.php?pagina=login');
                    exit;
                }

                iniciarSesionUsuario($user);
                $_SESSION['mensaje'] = '¡Bienvenido/a de nuevo, ' . htmlspecialchars($user['nombreUsuario']) . '!';
                $_SESSION['tipo_mensaje'] = 'success';

                if ($user['tipoUsuario'] === 'administrador') {
                    header('Location: index.php?pagina=admin_panel');
                } elseif ($user['tipoUsuario'] === 'ceo de aerolínea') {
                    header('Location: index.php?pagina=ceo_panel');
                } else {
                    $destino = 'index.php?pagina=reservas';
                    $volver = (string)($_POST['volver'] ?? '');

                    if ($volver === 'reservar') {
                        $vueloId = filter_var($_POST['vuelo_id'] ?? null, FILTER_VALIDATE_INT);
                        if ($vueloId && obtenerVueloPorId((int)$vueloId)) {
                            $destino = 'index.php?pagina=reservar&vuelo=' . $vueloId;
                        }
                    } elseif (in_array($volver, ['reservas', 'historial'], true)) {
                        $destino = 'index.php?pagina=' . $volver;
                    }

                    header('Location: ' . $destino);
                }
                exit;
            } else {
                $_SESSION['mensaje'] = 'Credenciales inválidas. Verificá tu correo y contraseña.';
                $_SESSION['tipo_mensaje'] = 'danger';
                header('Location: index.php?pagina=login');
                exit;
            }

        case 'registro_usuario':
            $nombre = trim($_POST['nombre'] ?? '') . ' ' . trim($_POST['apellido'] ?? '');
            $nombre = trim($nombre);
            $email = trim($_POST['email'] ?? '');
            $clave = (string)($_POST['clave'] ?? '');
            $confirmar = (string)($_POST['clave_confirm'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $tipoUsuario = (string)($_POST['tipo_usuario'] ?? 'usuario');

            if (!in_array($tipoUsuario, ['usuario', 'ceo de aerolínea'], true)) {
                $tipoUsuario = 'usuario';
            }

            if (empty($nombre) || empty($email) || empty($clave) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['mensaje'] = 'Por favor, completá todos los campos requeridos.';
                $_SESSION['tipo_mensaje'] = 'warning';
                header('Location: index.php?pagina=registro');
                exit;
            }

            if ($clave !== $confirmar) {
                $_SESSION['mensaje'] = 'Las contraseñas no coinciden. Por favor, verificálas.';
                $_SESSION['tipo_mensaje'] = 'danger';
                header('Location: index.php?pagina=registro');
                exit;
            }

            $res = registrarUsuario($nombre, $email, $clave, $telefono, $tipoUsuario);
            if ($res['ok']) {
                enviarMailBienvenida($email, $nombre);
                $_SESSION['mensaje'] = $res['mensaje'] . ' Te enviamos un correo de bienvenida.';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?pagina=login');
                exit;
            } else {
                $_SESSION['mensaje'] = $res['mensaje'];
                $_SESSION['tipo_mensaje'] = 'danger';
                header('Location: index.php?pagina=registro');
                exit;
            }

        case 'recuperar_clave':
            $email = trim($_POST['email'] ?? '');
            if (!empty($email)) {
                $tokenRecuperacion = bin2hex(random_bytes(16));
                enviarMailRecuperacionClave($email, $tokenRecuperacion);
            }
            $_SESSION['mensaje'] = 'Si el correo electrónico existe en nuestra base de datos, recibirás un enlace de recuperación a la brevedad.';
            $_SESSION['tipo_mensaje'] = 'info';
            header('Location: index.php?pagina=login');
            exit;

        case 'actualizar_perfil':
            if (!usuarioLogueado()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $user = obtenerUsuario();
            $nombre = trim($_POST['nombre'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');

            if (empty($nombre)) {
                $_SESSION['mensaje'] = 'El nombre no puede estar vacío.';
                $_SESSION['tipo_mensaje'] = 'warning';
            } else {
                $res = actualizarPerfilUsuario((int)$user['id'], $nombre, $user['email'], $telefono);
                if ($res['ok']) {
                    $_SESSION['usuario']['nombre'] = $nombre;
                    $_SESSION['usuario']['telefono'] = $telefono;
                    $_SESSION['mensaje'] = $res['mensaje'];
                    $_SESSION['tipo_mensaje'] = 'success';
                } else {
                    $_SESSION['mensaje'] = $res['mensaje'];
                    $_SESSION['tipo_mensaje'] = 'danger';
                }
            }
            header('Location: index.php?pagina=perfil');
            exit;

        // --- RESERVAS Y PAGOS ---
        case 'crear_reserva':
            if (!usuarioLogueado()) {
                $_SESSION['mensaje'] = 'Debés iniciar sesión para reservar tu vuelo.';
                $_SESSION['tipo_mensaje'] = 'warning';
                header('Location: index.php?pagina=login');
                exit;
            }
            $user = obtenerUsuario();
            $vueloId = (int)($_POST['vuelo_id'] ?? 0);
            $promoId = !empty($_POST['promo_id']) ? (int)$_POST['promo_id'] : null;

            $vuelo = obtenerVueloPorId($vueloId);
            if (!$vuelo) {
                $_SESSION['mensaje'] = 'El vuelo seleccionado no existe o no se encuentra disponible.';
                $_SESSION['tipo_mensaje'] = 'danger';
                header('Location: index.php?pagina=vuelos');
                exit;
            }

            $precioFinal = (float)$vuelo['precioVuelo'];
            if ($promoId) {
                $db = Database::getConnection();
                $stmt = $db->prepare("SELECT descuentoPromocion FROM promociones WHERE codPromocion = ? AND estadoPromocion = 'aprobada' LIMIT 1");
                $stmt->execute([$promoId]);
                $descuento = (float)$stmt->fetchColumn();
                if ($descuento > 0) {
                    $precioFinal = $precioFinal * (1 - ($descuento / 100));
                }
            }

            $reservaId = crearReserva((int)$user['id'], $vueloId, $promoId, $precioFinal);
            if ($reservaId) {
                $_SESSION['mensaje'] = '¡Reserva #' . $reservaId . ' generada con éxito! Podés abonarla ahora o gestionarla desde Mis Reservas.';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?pagina=reservas');
                exit;
            } else {
                $_SESSION['mensaje'] = 'No fue posible confirmar la reserva. El vuelo se encuentra sin asientos disponibles.';
                $_SESSION['tipo_mensaje'] = 'danger';
                header('Location: index.php?pagina=vuelos');
                exit;
            }

        case 'pagar_reserva':
            if (!usuarioLogueado()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $user = obtenerUsuario();
            $reservaId = (int)($_POST['reserva_id'] ?? 0);

            if (confirmarPagoReserva($reservaId, (int)$user['id'])) {
                $reservaDetalle = obtenerReservaPorId($reservaId);
                if ($reservaDetalle) {
                    enviarMailConfirmacionReserva($reservaDetalle);
                }
                $_SESSION['mensaje'] = '¡Pago registrado exitosamente! Comprobante emitido y enviado por correo para la reserva #' . $reservaId . '.';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: index.php?pagina=reserva_detalle&id=' . $reservaId);
                exit;
            } else {
                $_SESSION['mensaje'] = 'No se pudo procesar el pago de la reserva.';
                $_SESSION['tipo_mensaje'] = 'danger';
                header('Location: index.php?pagina=reservas');
                exit;
            }

        case 'cancelar_reserva':
            if (!usuarioLogueado()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $user = obtenerUsuario();
            $reservaId = (int)($_POST['reserva_id'] ?? 0);
            $res = cancelarReserva($reservaId, (int)$user['id']);

            $_SESSION['mensaje'] = $res['mensaje'];
            $_SESSION['tipo_mensaje'] = $res['ok'] ? 'warning' : 'danger';
            header('Location: index.php?pagina=reservas');
            exit;

        // --- CONTACTO ---
        case 'contacto':
            $nombreContacto = trim($_POST['nombre'] ?? 'Usuario');
            $emailContacto = trim($_POST['email'] ?? '');
            $asuntoContacto = trim($_POST['asunto'] ?? 'Consulta general');
            $mensajeContacto = trim($_POST['mensaje'] ?? '');

            if (!empty($emailContacto)) {
                enviarMailContacto($nombreContacto, $emailContacto, $asuntoContacto, $mensajeContacto);
            }

            $_SESSION['mensaje'] = '¡Muchas gracias por comunicarte! Tu mensaje fue registrado y te enviamos una constancia por correo.';
            $_SESSION['tipo_mensaje'] = 'success';
            header('Location: index.php?pagina=contacto');
            exit;

        // --- GESTIÓN CEO ---
        case 'crear_vuelo':
            if (!usuarioLogueado() || !esCeo()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $aeroId = (int)($_POST['aerolinea_id'] ?? 0);
            $origen = trim($_POST['origen'] ?? '');
            $destino = trim($_POST['destino'] ?? '');
            $fecha = trim($_POST['fecha'] ?? '');
            $hora = trim($_POST['hora'] ?? '');
            $precio = (float)($_POST['precio'] ?? 0);
            $asientos = (int)($_POST['asientos'] ?? 0);

            if (crearVuelo($aeroId, $origen, $destino, $fecha, $hora, $precio, $asientos)) {
                $_SESSION['mensaje'] = 'Nuevo vuelo programado con éxito.';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Ocurrió un error al registrar el vuelo.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
            header('Location: index.php?pagina=ceo_vuelos');
            exit;

        case 'modificar_vuelo':
            if (!usuarioLogueado() || !esCeo()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $vueloId = (int)($_POST['vuelo_id'] ?? 0);
            $origen = trim($_POST['origen'] ?? '');
            $destino = trim($_POST['destino'] ?? '');
            $fecha = trim($_POST['fecha'] ?? '');
            $hora = trim($_POST['hora'] ?? '');
            $precio = (float)($_POST['precio'] ?? 0);
            $asientos = (int)($_POST['asientos'] ?? 0);

            if (modificarVuelo($vueloId, $origen, $destino, $fecha, $hora, $precio, $asientos)) {
                $_SESSION['mensaje'] = 'Vuelo #' . $vueloId . ' modificado correctamente.';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar el vuelo.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
            header('Location: index.php?pagina=ceo_vuelos');
            exit;

        case 'eliminar_vuelo':
            if (!usuarioLogueado() || !esCeo()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $vueloId = (int)($_POST['vuelo_id'] ?? 0);
            $res = eliminarVuelo($vueloId);
            $_SESSION['mensaje'] = $res['mensaje'];
            $_SESSION['tipo_mensaje'] = $res['ok'] ? 'success' : 'danger';
            header('Location: index.php?pagina=ceo_vuelos');
            exit;

        case 'crear_promocion_ceo':
            if (!usuarioLogueado() || !esCeo()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $aeroId = (int)($_POST['aerolinea_id'] ?? 0);
            $desc = trim($_POST['descripcion'] ?? '');
            $descuento = (float)($_POST['descuento'] ?? 0);
            $inicio = trim($_POST['fecha_inicio'] ?? '') ?: null;
            $fin = trim($_POST['fecha_fin'] ?? '') ?: null;

            if (crearPromocion($aeroId, $desc, $descuento, $inicio, $fin)) {
                $_SESSION['mensaje'] = 'Promoción remitida para revisión. Quedará activa una vez aprobada por la cátedra/administrador.';
                $_SESSION['tipo_mensaje'] = 'info';
            } else {
                $_SESSION['mensaje'] = 'Error al crear la solicitud de promoción.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
            header('Location: index.php?pagina=ceo_promociones');
            exit;

        case 'eliminar_promocion_ceo':
            if (!usuarioLogueado() || !esCeo()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $promoId = (int)($_POST['promocion_id'] ?? 0);
            eliminarPromocion($promoId);
            $_SESSION['mensaje'] = 'Promoción retirada.';
            $_SESSION['tipo_mensaje'] = 'secondary';
            header('Location: index.php?pagina=ceo_promociones');
            exit;

        // --- GESTIÓN ADMINISTRADOR ---
        case 'crear_aerolinea':
            if (!usuarioLogueado() || !esAdmin()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $nombre = trim($_POST['nombre'] ?? '');
            $iata = trim($_POST['codigo_iata'] ?? '');
            $pais = trim($_POST['cod_pais'] ?? 'ARG');
            $ceoId = !empty($_POST['ceo_id']) ? (int)$_POST['ceo_id'] : null;
            $desc = trim($_POST['descripcion'] ?? '');

            if (crearAerolinea($nombre, $iata, $pais, $ceoId, $desc)) {
                $_SESSION['mensaje'] = 'Aerolínea "' . htmlspecialchars($nombre) . '" dada de alta con éxito.';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al dar de alta la aerolínea.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
            header('Location: index.php?pagina=admin_aerolineas');
            exit;

        case 'modificar_aerolinea':
            if (!usuarioLogueado() || !esAdmin()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $aeroId = (int)($_POST['aerolinea_id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $iata = trim($_POST['codigo_iata'] ?? '');
            $pais = trim($_POST['cod_pais'] ?? 'ARG');
            $ceoId = !empty($_POST['ceo_id']) ? (int)$_POST['ceo_id'] : null;
            $desc = trim($_POST['descripcion'] ?? '');

            if (modificarAerolinea($aeroId, $nombre, $iata, $pais, $ceoId, $desc)) {
                $_SESSION['mensaje'] = 'Aerolínea actualizada correctamente.';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al modificar la aerolínea.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
            header('Location: index.php?pagina=admin_aerolineas');
            exit;

        case 'eliminar_aerolinea':
            if (!usuarioLogueado() || !esAdmin()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $aeroId = (int)($_POST['aerolinea_id'] ?? 0);
            $res = eliminarAerolinea($aeroId);
            $_SESSION['mensaje'] = $res['mensaje'];
            $_SESSION['tipo_mensaje'] = $res['ok'] ? 'success' : 'danger';
            header('Location: index.php?pagina=admin_aerolineas');
            exit;

        case 'aprobar_promocion':
            if (!usuarioLogueado() || !esAdmin()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $promoId = (int)($_POST['promocion_id'] ?? 0);
            $estado = $_POST['estado'] === 'aprobada' ? 'aprobada' : 'denegada';

            cambiarEstadoPromocion($promoId, $estado);
            $_SESSION['mensaje'] = 'Promoción #' . $promoId . ' marcada como: ' . strtoupper($estado) . '.';
            $_SESSION['tipo_mensaje'] = $estado === 'aprobada' ? 'success' : 'warning';
            header('Location: index.php?pagina=admin_promociones');
            exit;

        case 'crear_novedad':
            if (!usuarioLogueado() || !esAdmin()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $user = obtenerUsuario();
            $texto = trim($_POST['texto'] ?? '');
            $fPub = trim($_POST['fecha_publicacion'] ?? date('Y-m-d'));
            $fExp = trim($_POST['fecha_expiracion'] ?? date('Y-m-d', strtotime('+30 days')));

            if (crearNovedad((int)$user['id'], $texto, $fPub, $fExp)) {
                $_SESSION['mensaje'] = 'Comunicado oficial publicado con éxito.';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al publicar comunicado.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
            header('Location: index.php?pagina=admin_novedades');
            exit;

        case 'modificar_novedad':
            if (!usuarioLogueado() || !esAdmin()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $novId = (int)($_POST['novedad_id'] ?? 0);
            $texto = trim($_POST['texto'] ?? '');
            $fPub = trim($_POST['fecha_publicacion'] ?? date('Y-m-d'));
            $fExp = trim($_POST['fecha_expiracion'] ?? date('Y-m-d', strtotime('+30 days')));

            if (modificarNovedad($novId, $texto, $fPub, $fExp)) {
                $_SESSION['mensaje'] = 'Comunicado #' . $novId . ' modificado.';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar el comunicado.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
            header('Location: index.php?pagina=admin_novedades');
            exit;

        case 'eliminar_novedad':
            if (!usuarioLogueado() || !esAdmin()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $novId = (int)($_POST['novedad_id'] ?? 0);
            eliminarNovedad($novId);
            $_SESSION['mensaje'] = 'Comunicado eliminado.';
            $_SESSION['tipo_mensaje'] = 'secondary';
            header('Location: index.php?pagina=admin_novedades');
            exit;

        case 'cambiar_estado_usuario':
            if (!usuarioLogueado() || !esAdmin()) {
                header('Location: index.php?pagina=login');
                exit;
            }
            $usrId = (int)($_POST['usuario_id'] ?? 0);
            $nuevoEstado = $_POST['nuevo_estado'] ?? 'activo';
            $tipoUsuario = !empty($_POST['tipo_usuario']) ? (string)$_POST['tipo_usuario'] : null;

            if (cambiarEstadoUsuario($usrId, $nuevoEstado, $tipoUsuario)) {
                $_SESSION['mensaje'] = 'Estado del usuario #' . $usrId . ' modificado a ' . $nuevoEstado . '.';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al modificar estado del usuario.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
            header('Location: index.php?pagina=admin_usuarios');
            exit;
    }
}

// ==========================================
// ENRUTADOR DE VISTAS (GET)
// ==========================================

$pagina = $_GET['pagina'] ?? 'inicio';

// Rutas exclusivas de Pasajeros / Usuarios Autenticados
$rutasPasajeros = ['perfil', 'reservas', 'reserva_detalle', 'historial'];
if (in_array($pagina, $rutasPasajeros, true) && !usuarioLogueado()) {
    header('Location: index.php?pagina=login&volver=' . urlencode($pagina));
    exit;
}

// Rutas exclusivas de Administrador
if (str_starts_with($pagina, 'admin_')) {
    if (!usuarioLogueado() || !esAdmin()) {
        header('Location: index.php?pagina=login');
        exit;
    }
}

// Rutas exclusivas de CEO de Aerolínea
if (str_starts_with($pagina, 'ceo_')) {
    if (!usuarioLogueado() || !esCeo()) {
        header('Location: index.php?pagina=login');
        exit;
    }
}

// Redirección amigable de página "panel" según rol
if ($pagina === 'panel') {
    if (!usuarioLogueado()) {
        header('Location: index.php?pagina=login');
        exit;
    }
    if (esAdmin()) {
        header('Location: index.php?pagina=admin_panel');
        exit;
    }
    if (esCeo()) {
        header('Location: index.php?pagina=ceo_panel');
        exit;
    }
    header('Location: index.php?pagina=reservas');
    exit;
}

// Mapa exhaustivo de vistas del sistema
$vistasDisponibles = [
    // Públicas y Pasajeros
    'inicio'             => __DIR__ . '/../app/views/inicio.php',
    'vuelos'             => __DIR__ . '/../app/views/vuelos.php',
    'vuelo_detalle'      => __DIR__ . '/../app/views/vuelo_detalle.php',
    'reservar'           => __DIR__ . '/../app/views/reservar.php',
    'reservas'           => __DIR__ . '/../app/views/reservas.php',
    'reserva_detalle'    => __DIR__ . '/../app/views/reserva_detalle.php',
    'historial'          => __DIR__ . '/../app/views/historial.php',
    'aerolineas'         => __DIR__ . '/../app/views/aerolineas.php',
    'aerolinea_detalle'  => __DIR__ . '/../app/views/aerolinea_detalle.php',
    'promociones'        => __DIR__ . '/../app/views/promociones.php',
    'novedades'          => __DIR__ . '/../app/views/novedades.php',
    'mapa_sitio'         => __DIR__ . '/../app/views/mapa_sitio.php',
    'mapa'               => __DIR__ . '/../app/views/mapa_sitio.php',
    '404'                => __DIR__ . '/../app/views/404.php',
    'contacto'           => __DIR__ . '/../app/views/contacto.php',
    'terminos'           => __DIR__ . '/../app/views/terminos.php',
    // Autenticación y Perfil
    'login'              => __DIR__ . '/../app/views/login.php',
    'registro'           => __DIR__ . '/../app/views/registro.php',
    'recuperar_clave'    => __DIR__ . '/../app/views/recuperar_clave.php',
    'perfil'             => __DIR__ . '/../app/views/perfil.php',
    // Panel y Gestión CEO
    'ceo_panel'          => __DIR__ . '/../app/views/ceo/panel.php',
    'ceo_vuelos'         => __DIR__ . '/../app/views/ceo/vuelos.php',
    'ceo_promociones'    => __DIR__ . '/../app/views/ceo/promociones.php',
    'ceo_reportes'       => __DIR__ . '/../app/views/ceo/reportes.php',
    // Panel y Gestión Administrador
    'admin_panel'        => __DIR__ . '/../app/views/admin/panel.php',
    'admin_aerolineas'   => __DIR__ . '/../app/views/admin/aerolineas.php',
    'admin_promociones'  => __DIR__ . '/../app/views/admin/promociones.php',
    'admin_novedades'    => __DIR__ . '/../app/views/admin/novedades.php',
    'admin_usuarios'     => __DIR__ . '/../app/views/admin/usuarios.php',
    'admin_reportes'     => __DIR__ . '/../app/views/admin/reportes.php',
];

if (!array_key_exists($pagina, $vistasDisponibles)) {
    http_response_code(404);
    $vistaArchivo = $vistasDisponibles['404'];
} else {
    $vistaArchivo = $vistasDisponibles[$pagina];
}

// RENDERIZADO DEL LAYOUT
require __DIR__ . '/../app/views/layouts/header.php';
require $vistaArchivo;
require __DIR__ . '/../app/views/layouts/footer.php';
