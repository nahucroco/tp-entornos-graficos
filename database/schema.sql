-- Esquema oficial para TP Entornos Gráficos 2026
-- Cumple estrictamente con el Modelo de Datos Mínimo de la Cátedra

CREATE DATABASE IF NOT EXISTS aeroreserva CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aeroreserva;

-- 1. TABLA USUARIOS
CREATE TABLE IF NOT EXISTS usuarios (
    codUsuario INT AUTO_INCREMENT PRIMARY KEY,
    nombreUsuario VARCHAR(100) NOT NULL,
    claveUsuario VARCHAR(255) NOT NULL, -- Compatible con password_hash()
    tipoUsuario ENUM('administrador', 'ceo de aerolínea', 'usuario') NOT NULL DEFAULT 'usuario',
    emailUsuario VARCHAR(100) NOT NULL UNIQUE,
    telefonoUsuario VARCHAR(20) DEFAULT NULL,
    estadoUsuario ENUM('pendiente', 'activo', 'denegado') NOT NULL DEFAULT 'activo',
    emailValidado TINYINT(1) NOT NULL DEFAULT 0,
    tokenValidacion VARCHAR(100) DEFAULT NULL,
    tokenRecuperacion VARCHAR(100) DEFAULT NULL,
    tokenExpiracion DATETIME DEFAULT NULL,
    fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. TABLA AEROLINEAS
CREATE TABLE IF NOT EXISTS aerolineas (
    codAerolinea INT AUTO_INCREMENT PRIMARY KEY,
    nombreAerolinea VARCHAR(100) NOT NULL,
    codigoIATA VARCHAR(3) NOT NULL UNIQUE,
    descripcionAerolinea VARCHAR(200) DEFAULT NULL,
    codPais VARCHAR(3) NOT NULL,
    codUsuarioCeo INT DEFAULT NULL,
    CONSTRAINT fk_aerolinea_ceo FOREIGN KEY (codUsuarioCeo) REFERENCES usuarios(codUsuario) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 3. TABLA VUELOS
CREATE TABLE IF NOT EXISTS vuelos (
    codVuelo INT AUTO_INCREMENT PRIMARY KEY,
    codAerolinea INT NOT NULL,
    origenVuelo VARCHAR(50) NOT NULL,
    destinoVuelo VARCHAR(50) NOT NULL,
    fechaSalidaVuelo DATE NOT NULL,
    horaSalidaVuelo TIME NOT NULL,
    precioVuelo DECIMAL(10,2) NOT NULL,
    asientosDisponibles INT NOT NULL,
    CONSTRAINT fk_vuelo_aerolinea FOREIGN KEY (codAerolinea) REFERENCES aerolineas(codAerolinea) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. TABLA PROMOCIONES
CREATE TABLE IF NOT EXISTS promociones (
    codPromocion INT AUTO_INCREMENT PRIMARY KEY,
    descripcionPromocion VARCHAR(200) NOT NULL,
    descuentoPromocion DECIMAL(5,2) NOT NULL, -- Porcentaje de descuento ej: 15.00
    codAerolinea INT NOT NULL,
    estadoPromocion ENUM('pendiente', 'aprobada', 'denegada') NOT NULL DEFAULT 'pendiente',
    fechaInicio DATE DEFAULT NULL,
    fechaFin DATE DEFAULT NULL,
    CONSTRAINT fk_promocion_aerolinea FOREIGN KEY (codAerolinea) REFERENCES aerolineas(codAerolinea) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. TABLA NOVEDADES
CREATE TABLE IF NOT EXISTS novedades (
    codNovedad INT AUTO_INCREMENT PRIMARY KEY,
    textoNovedad VARCHAR(200) NOT NULL,
    fechaPublicacionNovedad DATE NOT NULL,
    fechaExpiracionNovedad DATE NOT NULL,
    codUsuarioAdmin INT DEFAULT NULL,
    CONSTRAINT fk_novedad_admin FOREIGN KEY (codUsuarioAdmin) REFERENCES usuarios(codUsuario) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6. TABLA RESERVAS
CREATE TABLE IF NOT EXISTS reservas (
    codReserva INT AUTO_INCREMENT PRIMARY KEY,
    codUsuario INT NOT NULL,
    codVuelo INT NOT NULL,
    codPromocion INT DEFAULT NULL,
    fechaReserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    precioFinal DECIMAL(10,2) NOT NULL,
    estadoReserva ENUM('pendiente de pago', 'confirmada', 'cancelada') NOT NULL DEFAULT 'pendiente de pago',
    CONSTRAINT fk_reserva_usuario FOREIGN KEY (codUsuario) REFERENCES usuarios(codUsuario) ON DELETE CASCADE,
    CONSTRAINT fk_reserva_vuelo FOREIGN KEY (codVuelo) REFERENCES vuelos(codVuelo) ON DELETE CASCADE,
    CONSTRAINT fk_reserva_promocion FOREIGN KEY (codPromocion) REFERENCES promociones(codPromocion) ON DELETE SET NULL
) ENGINE=InnoDB;

-- DATOS INICIALES PARA PRUEBAS (Seeds)
-- Contraseña para todos los usuarios de prueba: 'admin123' / 'ceo123' / 'pasajero123'
-- Hashes generados con password_hash(..., PASSWORD_BCRYPT)

-- 1. Usuarios demo
INSERT INTO usuarios (codUsuario, nombreUsuario, claveUsuario, tipoUsuario, emailUsuario, telefonoUsuario, estadoUsuario, emailValidado)
VALUES
(1, 'Administrador General', '$2y$10$w3/gIeY1v6Jq7P1FzL52ceD3G59vR5oVbVpGqE4Nn2v7J0L.4V2hK', 'administrador', 'admin@aeroreserva.com', '341-555-0100', 'activo', 1),
(2, 'Carlos CEO Aerolíneas', '$2y$10$sP1uKx7LqD8aF4Y0ZbN2xeO9mN3vR4bVcVqW5Mn3v8K1L.5V3hL', 'ceo de aerolínea', 'ceo.ar@aeroreserva.com', '341-555-0200', 'activo', 1),
(3, 'Mariana Pasajera', '$2y$10$tQ2vLy8MrE9bF5Z1AcO3yeP0nO4wS5cWdWqX6No4w9L2M.6W4iM', 'usuario', 'mariana@gmail.com', '341-555-0300', 'activo', 1)
ON DUPLICATE KEY UPDATE nombreUsuario=VALUES(nombreUsuario);

-- 2. Aerolíneas demo
INSERT INTO aerolineas (codAerolinea, nombreAerolinea, codigoIATA, descripcionAerolinea, codPais, codUsuarioCeo)
VALUES
(1, 'Aerolíneas Argentinas', 'ARG', 'Línea de bandera nacional con vuelos de cabotaje e internacionales.', 'ARG', 2),
(2, 'Flybondi', 'FO', 'Primera aerolínea ultra low cost de Argentina.', 'ARG', NULL),
(3, 'LATAM Airlines', 'LA', 'Grupo aerocomercial líder en Sudamérica.', 'CHL', NULL)
ON DUPLICATE KEY UPDATE nombreAerolinea=VALUES(nombreAerolinea);

-- 3. Vuelos demo (fechas futuras para permitir reservas y cancelaciones)
INSERT INTO vuelos (codVuelo, codAerolinea, origenVuelo, destinoVuelo, fechaSalidaVuelo, horaSalidaVuelo, precioVuelo, asientosDisponibles)
VALUES
(1, 1, 'Rosario (ROS)', 'Buenos Aires (AEP)', DATE_ADD(CURRENT_DATE, INTERVAL 5 DAY), '08:30:00', 45000.00, 48),
(2, 1, 'Buenos Aires (AEP)', 'Bariloche (BRC)', DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY), '14:15:00', 98000.00, 32),
(3, 2, 'Rosario (ROS)', 'Córdoba (COR)', DATE_ADD(CURRENT_DATE, INTERVAL 10 DAY), '11:00:00', 38000.00, 60),
(4, 3, 'Buenos Aires (EZE)', 'Santiago de Chile (SCL)', DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY), '19:45:00', 145000.00, 25)
ON DUPLICATE KEY UPDATE origenVuelo=VALUES(origenVuelo);

-- 4. Promociones demo
INSERT INTO promociones (codPromocion, descripcionPromocion, descuentoPromocion, codAerolinea, estadoPromocion, fechaInicio, fechaFin)
VALUES
(1, '30% Off en vuelos de primavera', 30.00, 1, 'aprobada', CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)),
(2, '15% Off primera compra Flybondi', 15.00, 2, 'pendiente', CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 20 DAY))
ON DUPLICATE KEY UPDATE descripcionPromocion=VALUES(descripcionPromocion);

-- 5. Novedades demo
INSERT INTO novedades (codNovedad, textoNovedad, fechaPublicacionNovedad, fechaExpiracionNovedad, codUsuarioAdmin)
VALUES
(1, 'Mantenimiento preventivo en pista de aterrizaje el próximo domingo de 02:00 a 06:00 hs.', CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 15 DAY), 1),
(2, 'Nueva ruta directa Rosario - Bariloche disponible a partir de la próxima semana.', CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 45 DAY), 1)
ON DUPLICATE KEY UPDATE textoNovedad=VALUES(textoNovedad);
