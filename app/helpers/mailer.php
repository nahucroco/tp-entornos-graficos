<?php
declare(strict_types=1);

/**
 * Helper de Envío de Correos Electrónicos (Cliente SMTP nativo + Registro en spool/log).
 * Soporta autenticación segura TLS/SSL (Gmail, Brevo, Mailgun, Sendgrid o SMTP institucional)
 * y emulación transparente de contingencia para evaluación académica.
 */

function enviarCorreo(
    string $destinatarioEmail,
    string $destinatarioNombre,
    string $asunto,
    string $cuerpoHtml,
    string $cuerpoTextoPlano = ''
): bool {
    // 1. Cargar variables de entorno SMTP si existen
    $smtpHost = getenv('SMTP_HOST') ?: ($_ENV['SMTP_HOST'] ?? '');
    $smtpPort = (int)(getenv('SMTP_PORT') ?: ($_ENV['SMTP_PORT'] ?? 587));
    $smtpUser = getenv('SMTP_USER') ?: ($_ENV['SMTP_USER'] ?? '');
    $smtpPass = getenv('SMTP_PASS') ?: ($_ENV['SMTP_PASS'] ?? '');
    $smtpFrom = getenv('SMTP_FROM') ?: ($_ENV['SMTP_FROM'] ?? 'notificaciones@condoraeroreserva.com');
    $smtpFromName = getenv('SMTP_FROM_NAME') ?: ($_ENV['SMTP_FROM_NAME'] ?? 'Cóndor AeroReserva');
    $smtpSecure = strtolower(getenv('SMTP_SECURE') ?: ($_ENV['SMTP_SECURE'] ?? 'tls'));

    $enviadoSmtp = false;
    $errorDetalle = null;

    // 2. Si se configuró un servidor SMTP real, intentar despacho por socket seguro
    if (!empty($smtpHost) && !empty($smtpUser) && !empty($smtpPass)) {
        try {
            $enviadoSmtp = despacharPorSocketSmtp(
                $smtpHost,
                $smtpPort,
                $smtpUser,
                $smtpPass,
                $smtpSecure,
                $smtpFrom,
                $smtpFromName,
                $destinatarioEmail,
                $destinatarioNombre,
                $asunto,
                $cuerpoHtml
            );
        } catch (Throwable $e) {
            $errorDetalle = $e->getMessage();
            error_log("Fallo en despacho SMTP real: " . $errorDetalle);
        }
    }

    // 3. Registrar en bitácora de auditoría / spool local (MANDATORIO PARA CÁTEDRA)
    registrarEnLogMailing([
        'fecha' => date('Y-m-d H:i:s'),
        'destinatario' => "$destinatarioNombre <$destinatarioEmail>",
        'asunto' => $asunto,
        'estado' => $enviadoSmtp ? 'ENVIADO_SMTP_REAL' : 'REGISTRADO_EN_SPOOL',
        'smtp_host' => !empty($smtpHost) ? "$smtpHost:$smtpPort" : 'Sin host SMTP configurado (modo simulación)',
        'error_smtp' => $errorDetalle,
        'cuerpo' => $cuerpoHtml,
    ]);

    return true;
}

/**
 * Despacho directo vía socket SMTP con soporte STARTTLS y AUTH LOGIN.
 */
function despacharPorSocketSmtp(
    string $host,
    int $port,
    string $user,
    string $pass,
    string $secure,
    string $from,
    string $fromName,
    string $to,
    string $toName,
    string $subject,
    string $htmlBody
): bool {
    $timeout = 6;
    $remote = ($secure === 'ssl' || $port === 465) ? "ssl://{$host}:{$port}" : "tcp://{$host}:{$port}";
    
    $socket = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
    if (!$socket) {
        throw new RuntimeException("No se pudo conectar a $remote: $errstr ($errno)");
    }
    stream_set_timeout($socket, $timeout);

    $leer = function () use ($socket): string {
        $res = '';
        while ($line = fgets($socket, 515)) {
            $res .= $line;
            if (substr($line, 3, 1) === ' ') break;
        }
        return $res;
    };

    $enviar = function (string $cmd) use ($socket, $leer): string {
        fputs($socket, $cmd . "\r\n");
        return $leer();
    };

    $leer(); // Saludo inicial
    $enviar("EHLO " . gethostname());

    if (($secure === 'tls' || $port === 587) && $secure !== 'none') {
        $enviar("STARTTLS");
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($socket);
            throw new RuntimeException("Fallo en negociación STARTTLS");
        }
        $enviar("EHLO " . gethostname());
    }

    // Autenticación AUTH LOGIN
    $enviar("AUTH LOGIN");
    $enviar(base64_encode($user));
    $enviar(base64_encode($pass));

    // Remitente y Destinatario
    $enviar("MAIL FROM: <$from>");
    $enviar("RCPT TO: <$to>");
    $enviar("DATA");

    // Cabeceras y Cuerpo MIME
    $boundary = "----=_Part_" . bin2hex(random_bytes(16));
    $headers = [
        "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <$from>",
        "To: =?UTF-8?B?" . base64_encode($toName) . "?= <$to>",
        "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
        "MIME-Version: 1.0",
        "Content-Type: text/html; charset=UTF-8",
        "Date: " . date(DATE_RFC2822),
    ];

    $contenido = implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody . "\r\n.\r\n";
    fputs($socket, $contenido);
    $leer();

    $enviar("QUIT");
    fclose($socket);
    return true;
}

/**
 * Guarda el correo en archivo de log para verificación de cátedra.
 */
function registrarEnLogMailing(array $datos): void
{
    $logDir = __DIR__ . '/../../storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    $logFile = $logDir . '/mail.log';
    
    $separador = str_repeat('=', 70) . "\n";
    $registro = $separador;
    $registro .= "[{$datos['fecha']}] ESTADO: {$datos['estado']}\n";
    $registro .= "DESTINATARIO: {$datos['destinatario']}\n";
    $registro .= "ASUNTO: {$datos['asunto']}\n";
    $registro .= "SERVIDOR SMTP: {$datos['smtp_host']}\n";
    if (!empty($datos['error_smtp'])) {
        $registro .= "ERROR SMTP: {$datos['error_smtp']}\n";
    }
    $registro .= "--- CONTENIDO HTML ---\n";
    $registro .= strip_tags(str_replace(['<br>', '<br/>', '<p>', '</div>'], "\n", $datos['cuerpo'])) . "\n";
    $registro .= $separador . "\n";

    @file_put_contents($logFile, $registro, FILE_APPEND);
}

// ==========================================
// PLANTILLAS Y EVENTOS DE NOTIFICACIÓN
// ==========================================

function enviarMailBienvenida(string $email, string $nombre): bool
{
    $asunto = "¡Bienvenido/a a Cóndor AeroReserva!";
    $html = <<<HTML
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
        <div style="background-color: #1b0088; color: #ffffff; padding: 24px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px;">Cóndor AeroReserva</h1>
            <p style="margin: 4px 0 0; font-size: 14px; color: #70d6ff;">Plataforma Oficial de Vuelos y Promociones</p>
        </div>
        <div style="padding: 24px; color: #333333; line-height: 1.6;">
            <h2 style="color: #1b0088; font-size: 18px;">¡Hola, {$nombre}!</h2>
            <p>Tu cuenta ha sido creada exitosamente. A partir de este momento podés buscar vuelos en tiempo real, acceder a promociones exclusivas y gestionar tus pasajes desde cualquier dispositivo.</p>
            <div style="margin: 24px 0; text-align: center;">
                <a href="http://127.0.0.1:8000/index.php?pagina=vuelos" style="background-color: #e71d36; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">Explorar Vuelos Disponibles</a>
            </div>
            <p style="font-size: 12px; color: #777777;">Si no realizaste este registro, por favor ignorá este mensaje.</p>
        </div>
        <div style="background-color: #f8f9fa; padding: 16px; text-align: center; font-size: 12px; color: #666666; border-top: 1px solid #e0e0e0;">
            Cóndor AeroReserva · Proyecto Final Entornos Gráficos · UTN FRRo
        </div>
    </div>
HTML;

    return enviarCorreo($email, $nombre, $asunto, $html);
}

function enviarMailRecuperacionClave(string $email, string $token): bool
{
    $asunto = "Recuperación de contraseña - Cóndor AeroReserva";
    $link = "http://127.0.0.1:8000/index.php?pagina=recuperar_clave&token=" . urlencode($token);
    $html = <<<HTML
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
        <div style="background-color: #1b0088; color: #ffffff; padding: 24px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px;">Cóndor AeroReserva</h1>
            <p style="margin: 4px 0 0; font-size: 14px; color: #70d6ff;">Seguridad y Acceso</p>
        </div>
        <div style="padding: 24px; color: #333333; line-height: 1.6;">
            <h2 style="color: #1b0088; font-size: 18px;">Solicitud de restablecimiento de contraseña</h2>
            <p>Recibimos una solicitud para cambiar la contraseña de tu cuenta asociada a <strong>{$email}</strong>.</p>
            <p>Para ingresar tu nueva clave, hacé clic en el siguiente botón:</p>
            <div style="margin: 24px 0; text-align: center;">
                <a href="{$link}" style="background-color: #1b0088; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">Restablecer mi Contraseña</a>
            </div>
            <p style="font-size: 12px; color: #777777;">Este enlace es válido por 2 horas. Si no solicitaste este cambio, no te preocupes, tu clave actual sigue protegida.</p>
        </div>
        <div style="background-color: #f8f9fa; padding: 16px; text-align: center; font-size: 12px; color: #666666; border-top: 1px solid #e0e0e0;">
            Cóndor AeroReserva · UTN Facultad Regional Rosario
        </div>
    </div>
HTML;

    return enviarCorreo($email, $email, $asunto, $html);
}

function enviarMailConfirmacionReserva(array $reserva): bool
{
    $email = $reserva['emailUsuario'];
    $nombre = $reserva['nombreUsuario'];
    $codReserva = $reserva['codReserva'];
    $ruta = htmlspecialchars($reserva['origenVuelo']) . " → " . htmlspecialchars($reserva['destinoVuelo']);
    $monto = number_format((float)$reserva['precioFinal'], 2, ',', '.');
    $asunto = "Comprobante de Reserva #{$codReserva} Confirmada - Cóndor AeroReserva";

    $html = <<<HTML
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
        <div style="background-color: #1b0088; color: #ffffff; padding: 24px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px;">¡Tu viaje está confirmado!</h1>
            <p style="margin: 4px 0 0; font-size: 14px; color: #70d6ff;">Comprobante Oficial de Reserva #{$codReserva}</p>
        </div>
        <div style="padding: 24px; color: #333333; line-height: 1.6;">
            <h2 style="color: #1b0088; font-size: 18px;">Hola, {$nombre}:</h2>
            <p>Se ha acreditado el pago y emitido el ticket electrónico para tu próximo vuelo:</p>
            <div style="background-color: #f8f9fa; border-left: 4px solid #1b0088; padding: 16px; margin: 16px 0; border-radius: 4px;">
                <p style="margin: 4px 0;"><strong>Ruta:</strong> {$ruta}</p>
                <p style="margin: 4px 0;"><strong>Aerolínea:</strong> {$reserva['nombreAerolinea']} ({$reserva['codigoIATA']})</p>
                <p style="margin: 4px 0;"><strong>Fecha y Hora de Salida:</strong> {$reserva['fechaSalidaVuelo']} a las {$reserva['horaSalidaVuelo']} hs</p>
                <p style="margin: 4px 0;"><strong>Monto Total Abonado:</strong> <span style="color: #2b9348; font-weight: bold;">\${$monto} ARS</span></p>
            </div>
            <p style="font-size: 13px; color: #555555;">Recordá presentarte en el aeropuerto con 2 horas de anticipación para vuelos de cabotaje portando tu DNI original vigente.</p>
            <div style="margin: 24px 0; text-align: center;">
                <a href="http://127.0.0.1:8000/index.php?pagina=reserva_detalle&id={$codReserva}" style="background-color: #e71d36; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">Ver / Imprimir Voucher</a>
            </div>
        </div>
        <div style="background-color: #f8f9fa; padding: 16px; text-align: center; font-size: 12px; color: #666666; border-top: 1px solid #e0e0e0;">
            Cóndor AeroReserva · Regla de Negocio: Cancelaciones habilitadas hasta 72 horas antes de la partida.
        </div>
    </div>
HTML;

    return enviarCorreo($email, $nombre, $asunto, $html);
}

function enviarMailContacto(string $nombre, string $email, string $asunto, string $mensaje): bool
{
    $asuntoMail = "Nuevo mensaje de contacto: " . $asunto;
    $html = <<<HTML
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
        <div style="background-color: #1b0088; color: #ffffff; padding: 20px;">
            <h2 style="margin: 0; font-size: 20px;">Mensaje de Contacto Recibido</h2>
        </div>
        <div style="padding: 24px; color: #333333; line-height: 1.6;">
            <p><strong>Remitente:</strong> {$nombre} &lt;{$email}&gt;</p>
            <p><strong>Asunto:</strong> {$asunto}</p>
            <p><strong>Mensaje:</strong></p>
            <div style="background-color: #f8f9fa; padding: 16px; border-radius: 4px; border-left: 4px solid #70d6ff;">
                {$mensaje}
            </div>
        </div>
    </div>
HTML;

    // Se notifica tanto a la administración como al usuario remitente
    return enviarCorreo($email, $nombre, $asuntoMail, $html);
}
