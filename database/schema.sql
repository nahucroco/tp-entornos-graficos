-- Base mínima para implementar en MySQL en la siguiente etapa.
CREATE DATABASE IF NOT EXISTS aeroreserva CHARACTER SET utf8mb4;
USE aeroreserva;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  rol ENUM('pasajero','ceo','admin') NOT NULL DEFAULT 'pasajero',
  estado ENUM('pendiente','activo','denegado') NOT NULL DEFAULT 'pendiente',
  email_validado BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE aerolineas (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(100) NOT NULL);
CREATE TABLE vuelos (
  id INT AUTO_INCREMENT PRIMARY KEY, aerolinea_id INT NOT NULL, origen VARCHAR(100) NOT NULL,
  destino VARCHAR(100) NOT NULL, salida DATETIME NOT NULL, precio DECIMAL(12,2) NOT NULL,
  asientos_disponibles INT NOT NULL, FOREIGN KEY (aerolinea_id) REFERENCES aerolineas(id)
);
CREATE TABLE reservas (
  id INT AUTO_INCREMENT PRIMARY KEY, usuario_id INT NOT NULL, vuelo_id INT NOT NULL,
  estado ENUM('pendiente_pago','confirmada','cancelada') NOT NULL DEFAULT 'pendiente_pago',
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id), FOREIGN KEY (vuelo_id) REFERENCES vuelos(id)
);
