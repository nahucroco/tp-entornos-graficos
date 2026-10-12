<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/datos.php';

$aerolineas = obtenerAerolineas();
?>

<!-- BREADCRUMBS -->
<nav class="breadcrumb-condor" aria-label="Ruta de navegación">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="#">Usuario</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registrarse</li>
    </ol>
</nav>

<div class="row justify-content-center py-2">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0 rounded-4 p-4 p-md-5 bg-white">
            <div class="text-center mb-4 pb-2 border-bottom">
                <h1 class="h3 fw-bold mb-1">Formulario de Registro de Nuevo Usuario</h1>
                <p class="text-muted small">
                    Completá tus datos para crear tu cuenta en Cóndor AeroReserva y comenzar a gestionar tus vuelos.
                </p>
            </div>

            <form method="post" action="index.php" class="needs-validation" novalidate data-validar>
                <input type="hidden" name="accion" value="registro_usuario">

                <!-- 1. DATOS PERSONALES Y DOCUMENTACIÓN -->
                <fieldset class="mb-4">
                    <legend class="h6 text-uppercase fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-person-vcard me-2"></i>1. Datos Personales y Documentación
                    </legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="reg-nombre" class="form-label small fw-bold">Nombre/s <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reg-nombre" name="nombre" required placeholder="Ej. Juan Carlos">
                        </div>
                        <div class="col-md-6">
                            <label for="reg-apellido" class="form-label small fw-bold">Apellido/s <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reg-apellido" name="apellido" required placeholder="Ej. Pérez">
                        </div>
                        <div class="col-md-4">
                            <label for="reg-dni" class="form-label small fw-bold">DNI / Pasaporte <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reg-dni" name="dni" required placeholder="Ej. 38555123">
                        </div>
                        <div class="col-md-4">
                            <label for="reg-fecha-nac" class="form-label small fw-bold">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="reg-fecha-nac" name="fecha_nacimiento">
                        </div>
                        <div class="col-md-4">
                            <label for="reg-nacionalidad" class="form-label small fw-bold">Nacionalidad</label>
                            <select class="form-select" id="reg-nacionalidad" name="nacionalidad">
                                <option value="Argentina" selected>Argentina</option>
                                <option value="Chile">Chile</option>
                                <option value="Uruguay">Uruguay</option>
                                <option value="Brasil">Brasil</option>
                                <option value="Otra">Otra</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <!-- 2. INFORMACIÓN DE CONTACTO -->
                <fieldset class="mb-4">
                    <legend class="h6 text-uppercase fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-telephone-inbound me-2"></i>2. Información de Contacto
                    </legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="reg-telefono" class="form-label small fw-bold">Teléfono fijo</label>
                            <input type="tel" class="form-control" id="reg-telefono" name="telefono_fijo" placeholder="Ej. 341-4200000">
                        </div>
                        <div class="col-md-6">
                            <label for="reg-celular" class="form-label small fw-bold">Teléfono celular <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="reg-celular" name="telefono" required placeholder="Ej. 341-155000000">
                        </div>
                        <div class="col-12">
                            <label for="reg-direccion" class="form-label small fw-bold">Dirección de residencia</label>
                            <input type="text" class="form-control" id="reg-direccion" name="direccion" placeholder="Calle, número, ciudad">
                        </div>
                    </div>
                </fieldset>

                <!-- 3. DATOS DE CUENTA (LOGIN) Y TIPO DE USUARIO -->
                <fieldset class="mb-4">
                    <legend class="h6 text-uppercase fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-shield-lock me-2"></i>3. Datos de Cuenta (Credenciales de Acceso)
                    </legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="reg-email" class="form-label small fw-bold">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="reg-email" name="email" required placeholder="tu@email.com">
                        </div>
                        <div class="col-md-6">
                            <label for="reg-tipo" class="form-label small fw-bold">Tipo de Cuenta <span class="text-danger">*</span></label>
                            <select class="form-select" id="reg-tipo" name="tipo_usuario" required>
                                <option value="usuario" selected>Usuario / Pasajero (Cliente)</option>
                                <option value="ceo de aerolínea">CEO de Aerolínea (Requiere aprobación Admin)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="reg-clave" class="form-label small fw-bold">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="reg-clave" name="clave" minlength="6" required placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="col-md-6">
                            <label for="reg-clave2" class="form-label small fw-bold">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="reg-clave2" name="clave_confirm" minlength="6" required placeholder="Repetí tu contraseña">
                        </div>
                    </div>
                </fieldset>

                <!-- 4. PREFERENCIAS Y CONDICIONES -->
                <fieldset class="mb-4">
                    <legend class="h6 text-uppercase fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-sliders me-2"></i>4. Preferencias de Usuario y Suscripciones
                    </legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="reg-aero-fav" class="form-label small fw-bold">Aerolínea favorita</label>
                            <select class="form-select" id="reg-aero-fav" name="aerolinea_favorita">
                                <option value="">Sin preferencia específica</option>
                                <?php foreach ($aerolineas as $a): ?>
                                    <option value="<?= $a['codAerolinea'] ?>"><?= htmlspecialchars($a['nombreAerolinea']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex flex-column justify-content-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="check-novedades" name="recibir_novedades" checked>
                                <label class="form-check-label small" for="check-novedades">Recibir novedades y avisos de vuelos por email</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="check-promos" name="recibir_promociones" checked>
                                <label class="form-check-label small" for="check-promos">Recibir alertas de promociones y descuentos</label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- CONDICIONES DE USO -->
                <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="reg-terminos" required checked>
                        <label class="form-check-label small text-muted" for="reg-terminos">
                            He leído y acepto los <a href="index.php?pagina=terminos" target="_blank" class="fw-bold">Términos, Condiciones</a> y la Política de Cancelación de pasajes de Cóndor AeroReserva.
                        </label>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <a href="index.php" class="btn btn-outline-secondary px-4">Cancelar</a>
                    <button type="submit" class="btn btn-despegar px-5 shadow">
                        <i class="bi bi-person-check-fill me-1"></i>Registrarme
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
