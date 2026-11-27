-- Guarda en cada reserva los datos del pasajero titular ingresados al reservar.
-- Ejecutar una sola vez sobre la base existente, ANTES de subir el código que los usa.
-- (Aplicada en la base de producción el 2026-11-27.)
ALTER TABLE reservas
    ADD COLUMN nombrePasajero VARCHAR(100) DEFAULT NULL,
    ADD COLUMN dniPasajero VARCHAR(20) DEFAULT NULL,
    ADD COLUMN emailPasajero VARCHAR(100) DEFAULT NULL,
    ADD COLUMN telefonoPasajero VARCHAR(20) DEFAULT NULL;
