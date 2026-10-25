# Cóndor AeroReserva - Plataforma de Gestión y Reserva de Vuelos

Trabajo Práctico Integrador para la cátedra de **Entornos Gráficos (2026)** - Universidad Tecnológica Nacional (UTN FRRo).

Sistema integral de búsqueda, reserva y gestión aerocomercial inspirado en plataformas líderes de la industria (estilo Despegar.com), desarrollado bajo una arquitectura limpia en **PHP 8.3 puro**, base de datos distribuida **TiDB Cloud (MySQL 8+ compatible)** con cifrado SSL, y frontend responsivo y accesible con **Bootstrap 5.3**.

---

## 🚀 Requisitos y Configuración Local

1. **PHP 8.2 o superior** con extensiones activas: `pdo_mysql`, `openssl`, `curl`.
2. Servidor web local o comando embebido de PHP.

### Puesta en marcha:

```bash
# Iniciar servidor local apuntando al directorio public
php -S 127.0.0.1:8000 -t public
```

Acceder desde el navegador a: `http://127.0.0.1:8000`

---

## 🗄️ Base de Datos y Seeding Automatizado

El esquema se encuentra en [`database/schema.sql`](database/schema.sql). Para poblar o actualizar la base de datos de forma segura e idempotente con usuarios, aerolíneas, promociones vigentes, catálogo de vuelos, novedades y reservas de prueba:

```bash
php database/seeder.php
```

### 🔑 Credenciales Oficiales de Prueba:

| Rol | Correo Electrónico | Contraseña | Estado / Detalle |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin@aeroreserva.com` | `admin123` | Control total del sistema y autorizaciones |
| **Administrador (Soporte)** | `soporte@aeroreserva.com` | `admin123` | Gestión operativa y de novedades |
| **CEO Aerolínea** | `ceo.ar@aeroreserva.com` | `ceo123` | CEO de Aerolíneas Argentinas |
| **CEO Aerolínea** | `ceo.flybondi@aeroreserva.com` | `ceo123` | CEO de Flybondi |
| **CEO Aerolínea** | `ceo.latam@aeroreserva.com` | `ceo123` | CEO de LATAM Airlines |
| **CEO Aerolínea (Pendiente)** | `ceo.pendiente@aeroreserva.com` | `ceo123` | Pendiente de aprobación por Admin |
| **Pasajero (Cliente)** | `mariana@gmail.com` | `pasajero123` | Cuenta activa con reservas emitidas |
| **Pasajero (Cliente)** | `juan.perez@gmail.com` | `pasajero123` | Cuenta activa con historial |
| **Usuario Suspendido** | `usuario.suspendido@gmail.com` | `pasajero123` | Estado denegado (bloqueo por sanción) |

---

## 🧪 Suite de Pruebas Automatizadas

El proyecto cuenta con una suite completa de pruebas unitarias, de integración y HTTP End-to-End con soporte de cookies de sesión y tokens CSRF:

```bash
php tests/test_suite.php
```

### Cobertura de las pruebas (113/113 tests exitosos):
- **Capa de Datos:** Singleton PDO con SSL, transacciones atómicas de reserva, decremento de cupos y liberación de asientos ante cancelación.
- **Reglas de Negocio:** Validación estricta de la regla de cancelación hasta 72 horas antes de la salida del vuelo (Regla #13).
- **Seguridad:** Tokens CSRF criptográficos con `hash_equals()`, hashing Bcrypt (`password_hash` / `password_verify`), protección contra inyección SQL y timing attacks.
- **Control de Acceso (RBAC):** Guards en el Front Controller que garantizan que roles no autorizados o invitados sean redirigidos inmediatamente con HTTP 302 hacia el login.
- **Rutas Públicas y Privadas:** Cobertura HTTP 200 en todas las vistas públicas y redirecciones 302 seguras en todas las vistas protegidas.
- **Paginación:** Renderizado dinámico de navegación accesible WAI-AA y Bootstrap 5.3.

---

## ♿ Validación de Accesibilidad

Se evaluó la página de inicio desplegada en [TAW](https://www.tawdis.net/) bajo las pautas **WCAG 2.1, nivel AA**, para tecnologías HTML y CSS.

- La primera evaluación identificó **2 problemas automáticos** en el criterio 1.3.1 (Información y relaciones).
- Se corrigió la agrupación del selector de tipo de viaje mediante los elementos semánticos `fieldset` y `legend`.
- La evaluación final informó **0 problemas detectados de forma automática**.
- TAW mantiene advertencias y criterios no verificados que requieren revisión manual, principalmente sobre navegación por teclado, foco visible, etiquetas de formulario y alternativas textuales.

---

## 📐 Arquitectura del Sistema

- **Front Controller Pattern:** [`public/index.php`](public/index.php) centraliza todas las peticiones POST y solicitudes de vistas GET.
- **Helpers y Servicios:**
  - `Database.php`: Conexión PDO Singleton segura con soporte SSL para TiDB Cloud.
  - `auth.php`: Manejo de autenticación, control de roles (`esAdmin()`, `esCeo()`, `esPasajero()`) y ciclo de vida de sesión.
  - `csrf.php`: Generación y validación de tokens anti-CSRF.
  - `datos.php`: Capa de persistencia, consultas optimizadas, métricas y lógica de reservas.
  - `mailer.php`: Envío de correos transaccionales por SMTP (bienvenida, confirmación de reserva y comprobantes).
  - `paginador.php`: Generador de paginación numérica reutilizable.
- **Despliegue Continuo (CI/CD):** Configuración lista para producción con Dockerfile y `render.yaml`.
