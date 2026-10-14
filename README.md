# AeroReserva - TP Entornos Gráficos

Maqueta inicial en PHP y Bootstrap para el TP de gestión de vuelos.

## Ejecutar

Desde la carpeta `aerolineas-tp`, ejecutar:

```powershell
php -S localhost:8000 -t public
```

Luego abrir `http://localhost:8000` en el navegador.

## Alcance actual

- Pantalla inicial, búsqueda de vuelos y tarjetas con datos de ejemplo.
- Registro de cuentas de pasajero y solicitud de cuenta CEO, con inicio de sesión por credenciales.
- Panel con opciones según el rol y reserva simulada.
- Bootstrap, navegación accesible básica y validación HTML/JavaScript.

La conexión MySQL y los ABMC reales quedan como siguiente etapa. El archivo `database/schema.sql` contiene la base inicial para ello.
