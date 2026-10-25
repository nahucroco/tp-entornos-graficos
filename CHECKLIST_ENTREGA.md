# Checklist Oficial de Entrega de Proyecto — Entornos Gráficos (UTN FRRo)

Cátedra: **Ing. Daniela Díaz / Ing. Julián Butti**  
Proyecto: **Cóndor AeroReserva (2026)**  
Equipo: **Alfaro, Bautista · Crocombette, Nahuel · Franco, Natalia**  
URL Producción: [https://tp-entornos-graficos-bhrg.onrender.com/](https://tp-entornos-graficos-bhrg.onrender.com/)  
Repositorio GitHub: [https://github.com/nahucroco/tp-entornos-graficos](https://github.com/nahucroco/tp-entornos-graficos)

---

## 1. Despliegue y acceso

- [x] El sistema está subido a un hosting y es accesible desde el navegador con una URL pública: `https://tp-entornos-graficos-bhrg.onrender.com/`.
- [x] La URL funciona desde cualquier red y dispositivo (probado desde datos móviles 4G/5G y redes hogareñas).
- [x] El sitio carga sin errores de certificado ni advertencias del navegador (HTTPS provisto por Cloudflare / Render con TLS 1.3).
- [x] No quedan páginas rotas, links muertos ni rutas que devuelvan error 404 / 500 no controlado. Cuenta con página explicativa 404 personalizada (`app/views/404.php`).
- [x] Los archivos estáticos (imágenes de aerolíneas, logos, Bootstrap Icons, CSS, JS) cargan bien en producción.
- [x] El entorno de producción usa la base de datos de producción (cluster serverless en TiDB Cloud con cifrado SSL en AWS `us-east-1`).

## 2. Repositorio

- [x] El repositorio es público y la URL está incluida en la entrega y documentación.
- [x] Tiene un `README.md` completo con: qué hace el sistema, URL de producción, tecnologías usadas, integrantes del grupo y usuarios de prueba.
- [x] Historial de commits real y distribuido entre los integrantes (Bautista, Nahuel y Natalia).
- [x] Mensajes de commit entendibles bajo la convención *Conventional Commits* (`feat:`, `fix:`, etc.).
- [x] Hay un `.gitignore` correcto: **no** están subidos `vendor/`, `node_modules/`, `.env`, ni archivos temporales.
- [x] **No hay credenciales, claves de API ni contraseñas en el código.** Se utilizan variables de entorno cargadas dinámicamente vía `.env`.
- [x] La rama principal `main` es la que corresponde a lo entregado y está sincronizada con el despliegue.

## 3. Se puede testear sin instrucciones

- [x] Existe al menos un usuario de prueba con su contraseña documentado en el README y en el informe.
- [x] Si hay roles (admin, ceo, pasajero), hay un usuario de prueba por cada rol documentado en la sección de Puesta en Funcionamiento.
- [x] Hay datos de prueba cargados: 33 vuelos, 6 aerolíneas, 8 promociones, 6 novedades y 7 reservas iniciales (el sistema nunca abre vacío).
- [x] El registro de un usuario nuevo funciona de punta a punta (`index.php?pagina=registro`).
- [x] Cualquier flujo importante (búsqueda de vuelo, selección, reserva, pago de pasaje y descarga de constancia) se puede completar de forma intuitiva.

## 4. Emails

- [x] El email de confirmación de reserva y compra **llega efectivamente** a la casilla vía SMTP.
- [x] El email de recuperación de contraseña y contacto se procesan y cuentan con registro en `storage/logs/mail.log`.
- [x] Los links dentro de los emails apuntan al dominio de producción en Render.
- [x] El contenido del email está en español, con diseño responsive y sin textos de plantilla sin reemplazar.
- [x] El remitente es identificable institucionalmente como `Cóndor AeroReserva <notificaciones@aeroreserva.com>`.

## 5. Navegación y usabilidad

- [x] Un usuario que no conoce el sistema entiende qué hacer al entrar gracias al Hero Search superior estilo Despegar.
- [x] Todas las pantallas son alcanzables desde el menú principal, los breadcrumbs o el Mapa del Sitio (`index.php?pagina=mapa_sitio`). No hay pantallas huérfanas.
- [x] Siempre se puede volver atrás o a la pantalla principal mediante breadcrumbs y logo de cabecera.
- [x] Los botones y links dicen explícitamente qué hacen ("Ingresar a mi cuenta", "Buscar vuelos", "Reservar este vuelo", "Pagar reserva").
- [x] El estado actual es visible: se muestra el nombre y rol del usuario logueado en la barra superior, la sección activa en el menú y breadcrumbs.
- [x] Las acciones destructivas (cancelar reserva, eliminar vuelos o aerolíneas) piden confirmación explícita mediante diálogo `confirm()`.
- [x] Después de cada acción hay feedback con alertas Flash de Bootstrap (éxito, advertencia o error) gestionadas en `$_SESSION['mensaje']`.
- [x] Las listas vacías muestran un mensaje amigable con ilustración/icono ("No se encontraron vuelos para los filtros seleccionados"), no tablas vacías.

## 6. Idioma y textos

- [x] Todos los textos visibles están en español: botones, títulos, menús, placeholders y alertas.
- [x] Los mensajes de validación están en español ("Por favor, ingresá un correo electrónico válido").
- [x] Las alertas y mensajes de error del sistema están completamente traducidos e interceptados.
- [x] Los mensajes de error de la base de datos nunca se muestran crudos al usuario; se capturan con bloques `try/catch` y se muestra un mensaje accesible.
- [x] Sin faltas de ortografía ni textos de prueba provisionales.
- [x] Fechas en formato local (`dd/mm/aaaa`), números con separador de miles y moneda en Pesos Argentinos (`$ 45.000,00`).

## 7. Formularios y validaciones

- [x] Todos los campos obligatorios están validados **en el servidor** (no solo en el navegador).
- [x] Los errores se muestran al lado del campo que falla mediante clases `.is-invalid` y `.invalid-feedback` de Bootstrap.
- [x] Emails (`FILTER_VALIDATE_EMAIL`), números, fechas y contraseñas se validan con su formato estricto.
- [x] No se puede romper el sistema mandando datos vacíos, textos excesivamente largos o caracteres maliciosos (`htmlspecialchars()`).
- [x] Protección contra doble submit mediante desactivación de botón y tokens CSRF atómicos.

## 8. Seguridad mínima

- [x] Las contraseñas están hasheadas en la base de datos con Bcrypt (`password_hash` con costo por defecto), nunca en texto plano.
- [x] Las páginas privadas no se pueden abrir sin estar logueado (protección RBAC centralizada en el Front Controller antes de renderizar).
- [x] Control estricto de propiedad: un usuario no puede pagar, ver ni cancelar reservas que pertenezcan a otro pasajero (`WHERE r.codUsuario = ?`).
- [x] Las consultas usan sentencias preparadas PDO con parámetros atados (100% preparadas, cero concatenación SQL).
- [x] El contenido cargado por usuarios se escapa al mostrarlo con `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` (prevención de XSS).
- [x] El logout funciona y destruye realmente la sesión, borrando `$_SESSION`, la cookie de sesión y llamando a `session_destroy()`.

## 9. Visual y responsive

- [x] Se adapta fluidamente a celular (360px+), tablet (768px+) y escritorio (1200px+).
- [x] Nada se superpone ni requiere scroll horizontal (grilla fluida de Bootstrap 5.3).
- [x] Estilo consistente entre pantallas (paleta oficial violeta/coral inspirada en Despegar).
- [x] Las imágenes tienen tamaño optimizado y etiquetas `alt`.
- [x] Las imágenes cuentan con atributo `title` descriptivo para información flotante al pasar el mouse.
- [x] Ratio de contraste superior a 4.5:1 verificado para texto normal, complementado con el **Modo Alto Contraste (WCAG AAA)** persistido mediante cookie `modo_contraste`.

## 10. Última pasada antes de entregar

- [x] La consola del navegador no muestra errores en rojo.
- [x] No quedan `var_dump()`, `print_r()`, `console.log()` ni mensajes de debug en el código fuente.
- [x] Modo debug apagado en producción (manejo centralizado de logs en `storage/logs/`).
- [x] Recorrido completo del sistema desde cero en modo incógnito simulando un pasajero nuevo.
- [x] La entrega incluye: URL de producción, URL de GitHub, usuarios de prueba de todos los roles e informe final completo.

---

## 11. Cumplimiento de Pautas de Cátedra (Material Didáctico)

- [x] **Cookies:** Implementado botón de accesibilidad en la barra superior para alternar "Modo Alto Contraste" persistido con `setcookie("modo_contraste", ...)` y recuperado con `$_COOKIE["modo_contraste"]`, tal como enseña la Ing. Daniela Díaz en `UTN_EntornosGráficos_Ejemplo_Cookies.pdf`.
- [x] **Sesiones:** Manejo riguroso de `session_start()`, `$_SESSION` para datos de usuario autenticado, roles y mensajes flash, y destrucción completa con `session_destroy()` (`UTN_Entornos_Gráficos_Sesiones_PHP.pdf`).
- [x] **6 Módulos ABMC:** Vuelos, Aerolíneas, Promociones, Novedades, Reservas y Usuarios (`MANEJO DE BASE DE DATOS _PHP 5_versión2019.pdf`).
- [x] **Accesibilidad Web (WCAG 2.0 / Unidad 6):** Skip-links, navegación por teclado, focus visible, contraste WCAG AAA, semántica HTML5 pura (`UTN_EntornosGráficos_AccesibilidadWeb.pdf`).
- [x] **Suite de Tests:** 117 pruebas automatizadas ejecutadas y superadas con 100% de éxito en `tests/test_suite.php`.
