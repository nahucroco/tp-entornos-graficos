# Comprobación de Checklist — Cóndor AeroReserva

- **Cátedra:** Entornos Gráficos — UTN Facultad Regional Rosario
- **Docentes:** Ing. Daniela Díaz, Ing. Julián Butti
- **Integrantes del equipo:**
  - Alfaro, Bautista — Legajo 47881
  - Crocombette, Nahuel — Legajo 49720
  - Franco, Natalia — Legajo 48952
- **Sitio evaluado:** Cóndor AeroReserva (TPI 2026)

---

## CHECKLIST USABILIDAD

### Identidad corporativa

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿La portada del Sitio refleja la identidad y pertenencia de la institución? | X | | La portada (`inicio.php`) presenta la marca Cóndor AeroReserva, su paleta visual y el buscador principal de vuelos. |
| ¿Existen elementos de la imagen corporativa de la organización en la Portada de su Sitio? ¿Se repiten en todas las páginas? | X | | Header y footer compartidos (`layouts/header.php` y `layouts/footer.php`) mantienen marca, colores y tipografía en todo el sitio. |
| ¿El logotipo de la organización ha sido incluido en un lugar importante en la Portada y en las páginas interiores del Sitio? | X | | El logotipo figura en la barra superior de navegación en todas las vistas. |
| ¿Todas las páginas cuentan con un título que indique el nombre de la institución e información de contactos virtuales y físicos al pie de la página? | X | | El `<title>` incluye el nombre de la plataforma y el pie de página incluye los datos de contacto e institucionales. |

### Utilidad del Sitio Web

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿El Sitio ofrece información sobre las actividades y servicios más recientes e importantes que está llevando a cabo la institución? | X | | Muestra novedades vigentes, promociones aprobadas y catálogo de vuelos disponibles. |
| ¿Los usuarios pueden encontrar fácilmente en la portada la información acerca de las actividades y servicios más importantes de la institución? | X | | El buscador de vuelos, promociones destacadas y novedades aparecen directamente en la página de inicio. |

### Navegación

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿El diseño del Sitio es eficiente, rápido e intuitivo? | X | | Basado en Bootstrap 5.3 con grilla responsiva y acciones directas por rol. |
| ¿Aparece el menú de navegación en un lugar destacado? ¿Se ve fácilmente? | X | | Barra de navegación fija/superior en todas las vistas, colapsable en móviles. |
| ¿Verificó la consistencia de todos los enlaces? | X | | Verificado mediante pruebas automatizadas HTTP en `tests/test_suite.php` (rutas públicas, protegidas y 404). |
| ¿El Sitio cuenta con un mapa o buscador que facilite el acceso directo a los contenidos? | X | | Cuenta con buscador de vuelos (`vuelos.php`), buscador de promociones (`promociones.php`) y mapa del sitio por rol (`mapa_sitio.php`). |
| ¿El Sitio mantiene una navegación consistente y coherente en todas las pantallas? | X | | Todas las vistas comparten `header.php`, `footer.php` y migas de pan (`breadcrumb`). |

### Visibilidad del estado del sistema

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿Se informa al usuario claramente el área del Sitio que está visitando? | X | | Mediante encabezados `<h1>`, breadcrumbs (`breadcrumb-condor`) y estado activo en menú. |
| ¿El Sitio Web diferencia entre enlaces visitados y enlaces por visitar? | X | | Estilos diferenciados para enlaces activos y visitados en `estilos.css`. |
| En caso de servicios o trámites en línea, ¿ofrece información de cuántos pasos faltan para terminar? | X | | El flujo de reserva muestra búsqueda → detalle/selección de promoción (`reservar.php`) → confirmación de pago (`reservas.php` / `reserva_detalle.php`). |

### Consistencia y cumplimiento de estándares

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿El HTML del Sitio ha sido validado satisfactoriamente según w3c.org? | X | | Estructura HTML5 semántica validada y corregida junto con la evaluación TAW. |
| ¿El o los archivos de Hojas de estilo (CSS) han sido aprobados según w3c.org? | X | | Hojas de estilo en `public/assets/css/estilos.css` sin errores de sintaxis. |
| ¿Comprobó la consistencia de Links usando el verificador de w3c.org? | X | | Enlaces internos verificados en navegación y suite de pruebas automatizadas. |

### Atención de errores

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿Usa Javascript para validar formularios durante su llenado y antes de enviarlos? | X | | `public/assets/js/validaciones.js` valida formularios Bootstrap (`needs-validation` / `data-validar`) antes del envío, complementado con validación en servidor. |
| ¿Usa elementos destacados para indicar los campos obligatorios dentro de un formulario? | X | | Los campos requeridos se indican con asterisco rojo (`*`) y atributo `required`. |
| ¿Después de que ocurre un error, es fácil volver a la página donde se encontraba antes que se produjese o entrega recomendaciones de los pasos a seguir? | X | | Los mensajes flash claros redirigen al formulario correspondiente indicando qué corregir, sin exponer errores técnicos SQL. |

### Estética y diseño

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿Usa jerarquías visuales para determinar lo importante con una sola mirada? | X | | Uso de encabezados jerárquicos (`h1`–`h6`), tarjetas, badges de estado y botones primarios destacados. |
| ¿Las imágenes tienen tamaños adecuados que no dificultan el acceso a las páginas? | X | | Logos e imágenes optimizados en `public/assets/img/`. |
| ¿Las imágenes tienen etiqueta ALT en el código HTML para facilitar la navegación? | X | | Todas las imágenes informativas incluyen atributo `alt` descriptivo. |

### Ayuda ante errores

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| En caso de errores de consistencia dentro del sitio, ¿se ofrece un mensaje personalizado mediante una página explicativa? (Por ejemplo: Error 404 para página inexistente) | X | | `app/views/404.php` muestra una página explicativa con enlace para volver al inicio. |
| ¿Entrega información de contacto fuera de Internet? (Por ejemplo: teléfono institucional, fono 0800, mesa de ayuda, OIRS) | X | | En `contacto.php` y el footer se incluyen canales de contacto y atención al pasajero. |
| ¿Ofrece área de Preguntas Frecuentes con datos de ayuda a usuarios? | | X | No posee una sección dedicada de Preguntas Frecuentes; se cubren condiciones y políticas en `terminos.php` y `contacto.php`. |
| ¿Ofrece páginas de ayuda que explican cómo usar el Sitio? | X | | Se brindan indicaciones en formularios, `terminos.php`, `mapa_sitio.php` y detalle de reserva (`reserva_detalle.php`). |

### Retroalimentación

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿Puede el usuario ponerse en contacto con el encargado del Sitio Web para hacer sugerencias o comentarios? | X | | Mediante el formulario de `contacto.php`. |
| ¿Funcionan correctamente los formularios de contacto?, ¿Ha probado cada uno de ellos? | X | | Probado con envío validado vía CSRF y despacho/registro en `mailer.php`. |
| ¿Hay alguien encargado de recibir y contestar estos mensajes? | X | | El rol Administrador atiende los mensajes recibidos en la casilla configurada. |

---

## CHECKLIST ACCESIBILIDAD

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿Se proporciona un texto equivalente para todo elemento no textual, tales como imágenes, para explicar su contenido a discapacitados visuales? | X | | Atributos `alt` en imágenes y `aria-hidden="true"` en íconos decorativos junto con `aria-label` en botones de acción. |
| ¿La información transmitida a través de los colores también está disponible sin color? | X | | Estados y mensajes acompañados siempre de texto explícito y/o íconos (no solo color). |
| ¿El documento está estructurado para que pueda ser leído con o sin una hoja de estilo, utilizando adecuadamente los tags de HTML? | X | | Uso de `header`, `nav`, `main`, `aside`, `article`, `footer`, `fieldset`, `legend` y tablas con `scope="col"`. |
| ¿El documento está escrito en un lenguaje adecuado y se deja claro cuando se cambia de idioma? | X | | `<html lang="es">` definido en el layout principal y redacción clara en español. |
| ¿Las tablas se utilizan para presentar información y no para diagramar el contenido del Sitio Web? | X | | La diagramación usa CSS Grid / Flexbox (Bootstrap 5); las etiquetas `<table>` se reservan exclusivamente para listados tabulares. |
| ¿Las páginas que utilizan nuevas tecnologías siguen funcionando cuando dicha tecnología no está presente (por ejemplo, los plug-ins de Flash)? | X | | No depende de plug-ins externos; los flujos críticos funcionan con HTML/PHP estándar en el servidor. |
| ¿Es posible controlar los objetos o las páginas que se actualizan o se cambian automáticamente, permitiendo incluso generar pausas para su revisión? | X | | No hay carruseles ni actualizaciones automáticas forzadas que impidan la lectura. |
| ¿Se asegura la accesibilidad de los elementos de la página que tengan sus propias interfaces? (Por ejemplo, para el uso de Portlets) | X | | Controles y tarjetas integrados con HTML semántico y atributos ARIA estándar. |
| ¿Se permite al usuario activar elementos de las páginas, usando cualquier dispositivo como el mouse o el teclado y no sólo uno en particular? | X | | Controles nativos `<button>`, `<a>`, `<input>` y `<select>` operables por teclado (`Tab`, `Enter`, `Space`). |
| ¿Se ofrecen soluciones transitorias que permiten a usuarios con browsers antiguos, acceder a contenidos que han sido creados en nuevas tecnologías? | X | | Contenido textual y formularios HTML estándar degradan correctamente sin requerir JS avanzado. |
| ¿Se usan las tecnologías y guías de trabajo generadas por la W3C? | X | | Validado con TAW bajo pautas WCAG 2.1 nivel AA (0 problemas automáticos). |
| ¿Se ofrece ayuda y orientación a los usuarios para entender páginas o elementos complejos dentro de ellas? (Por ejemplo: mapas y gráficos) | X | | Textos de ayuda en buscadores, criterios de filtrado y mapa del sitio estructurado por rol. |
| ¿Se ofrecen elementos de navegación claros? | X | | Menú principal consistente, breadcrumbs y paginador accesible. |
| ¿Se asegura que los documentos que se ofrecen a través del Sitio son simples, claros y pueden ser fácilmente entendidos? | X | | Textos breves, etiquetas claras y mensajes de error comprensibles para el usuario. |

---

## CHECKLIST RAPIDEZ DE ACCESO

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿El usuario puede encontrar en no más de 3 clics la información buscada? | X | | Vuelos, promociones, novedades, reservas e historial accesibles en 1 a 2 clics desde la portada o navbar. |
| ¿Aparece el menú de navegación en un lugar destacado? ¿Se ve fácilmente? | X | | Encabezado superior visible en todas las pantallas. |
| ¿El Sitio cuenta con un mapa y/o buscador que dé un acceso alternativo a los contenidos? | X | | Mapa del sitio (`mapa_sitio.php`) y buscadores de vuelos/promociones. |
| ¿Es fácil llegar a las secciones más importantes del Sitio desde cualquier página? | X | | Todas las secciones clave están en el navbar superior y en el footer. |
| ¿El Sitio mantiene una navegación consistente y coherente en todas sus páginas? | X | | Estructura uniforme en todas las vistas públicas y paneles de rol. |
| ¿El diseño usa jerarquías visuales para determinar lo importante con una sola mirada? | X | | Contraste tipográfico, cards y botones de acción principales bien diferenciados. |
| ¿Los formularios ofrecen opciones que permitan al usuario evitar, cancelar o rehacer una acción? | X | | Botones de cancelar/volver, confirmación previa antes de eliminar o cancelar reservas, y botón de limpiar filtros. |
| ¿El tamaño de la letra de los textos es adecuado y ajustable o modificable por el usuario usando las herramientas del programa visualizador? | X | | Uso de unidades relativas (`rem`/`em` de Bootstrap 5), escalable con el zoom del navegador. |
| ¿Los vínculos, imágenes e íconos son claramente visibles y distinguibles? | X | | Íconos Bootstrap Icons con contraste adecuado y etiquetas claras. |
| ¿Los vínculos (links) visitados y no visitados son claramente diferenciables? | X | | Diferenciación visual definida en hoja de estilos. |
| ¿Los íconos son representativos de la función o acción que realizan y son aclarados mediante una etiqueta ALT en HTML? | X | | Íconos acompañados de texto visible, `title` o `aria-label` descriptivo. |
| ¿Todas las páginas cuentan con un título que indique el nombre de la institución e información de contactos virtuales y físicos al pie de la página? | X | | Título institucional en `<title>` y datos de contacto en `footer.php`. |
| ¿Provee información del organigrama de la institución?, ¿Incluye nombres actualizados de las autoridades y la forma de contactarlos? | | X | No aplica organigrama formal por ser una plataforma comercial de reservas; los responsables de cada aerolínea (CEOs) figuran en el sistema. |
| ¿El nombre de la URL está vinculado con el nombre o función de la institución y se ofrece en la barra superior del programa visualizador? | X | | Rutas descriptivas (`?pagina=vuelos`, `?pagina=promociones`, `?pagina=reservas`, etc.) y título en pestaña. |
| ¿Ofrece el Sitio contenidos sobre la visión, misión, objetivos y plan estratégico de la institución? | X | | Presentados de forma resumida en la portada y sección institucional/términos. |
| En el caso que existan palabras técnicas en los contenidos del Sitio ¿Existe una sección de glosario que las explique?, ¿es fácil llegar a él? | | X | No requiere glosario dedicado; se evitan tecnicismos y se aclaran códigos IATA junto al nombre de ciudad/aerolínea. |
| ¿Ofrece páginas de ayuda que explican cómo usar el Sitio Web? | X | | Guías contextuales en reserva, detalle de reserva y términos de servicio. |
| ¿Ofrece área de Preguntas Frecuentes con datos de ayuda a usuarios? | | X | Cubierto mediante `terminos.php` y formulario de `contacto.php`. |
| En caso de errores de consistencia dentro del sitio, ¿se ofrece un mensaje personalizado mediante una página explicativa?, (Por ejemplo: Error 404 para página inexistente) | X | | Página `404.php` personalizada con enlace de retorno. |

---

## PERCEPTIBLE
*Hacer contenidos y controles perceptibles por todos los usuarios*

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿Las imágenes tienen un texto alternativo que las describa? | X | | Todas las imágenes llevan atributo `alt`. |
| ¿Los videos tienen subtítulos y los audios tienen transcripción? | | X | No aplica (`N/A`): el sitio no reproduce contenido de video ni audio. |
| La página web o documento ¿incluye encabezados, listas, puntos de referencia ARIA y otros elementos semánticos para comunicar estructura documental? | X | | Landmarks HTML5 (`nav`, `main`, `aside`, `footer`), listas `<ul>`/`<ol>`, `aria-label` y jerarquía `h1`–`h6`. |
| ¿El orden de tabulación y el orden de lectura son lógicos e intuitivos? | X | | Flujo DOM natural de arriba hacia abajo e izquierda a derecha sin `tabindex` positivos. |
| ¿Los campos de los formularios de las páginas web y documentos tienen las etiquetas y marcadores codificados apropiadamente? | X | | Todos los inputs tienen `<label for="...">` asociado, `fieldset`/`legend` donde corresponde y `aria-label` en toggles. |
| ¿Se evita utilizar características visuales para comunicar información (v.gr. "haga click en el círculo de la derecha" o "los campos requeridos están en rojo")? | X | | Los requerimientos y estados se comunican con texto explícito, no únicamente por posición o color. |
| ¿La interfaz tiene suficiente contraste texto-fondo? | X | | Paleta de colores con contraste AA entre texto oscuro sobre fondos claros y botones con texto blanco. |
| ¿El contenido se escala correctamente cuando el texto se agranda hasta el 200%? | X | | Diseño responsivo con Bootstrap 5 que reacomoda columnas sin solapamientos ni pérdida de contenido al 200%. |

---

## OPERABLE
*Hacer los contenidos y controles operables por todos los usuarios*

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿Todos los menús, enlaces, botones y otros controles pueden ser operados por teclado, a fin de hacerlos accesibles a usuarios que no utilizan el mouse? | X | | Navegación y formularios 100% operables mediante teclado. |
| ¿La página web incluye un indicador visible de enfoque para que todos los usuarios, especialmente los que utilizan el teclado, puedan fácilmente rastrear su posición? | X | | Indicadores de foco visibles (`:focus-visible` / Bootstrap focus ring) en botones, enlaces y campos. |
| ¿Aquellos elementos que se actualizan o tienen scroll automáticos tienen controles prominentes y accesibles que permiten a los usuarios pausar o avanzar esos elementos ellos mismos? | X | | No existen elementos con desplazamiento automático no controlable. |
| ¿Las páginas que tienen temporizadores incluyen mecanismos que permiten ajustar los límites temporales a las necesidades de los usuarios que necesitan más tiempo? | X | | No hay temporizadores de cuenta regresiva que expulsen al usuario durante la carga de formularios. |
| ¿Se ha evitado la utilización de contenido que parpadea o emite destellos? | X | | Sin animaciones intermitentes ni destellos. |
| ¿La página web o documento tiene un título que describe su propósito o tópicos? | X | | `<title>` dinámico por página y encabezado `<h1>` descriptivo. |
| ¿Los mecanismos de atajo están en lugares que permiten a los usuarios saltear bloques de contenidos? | X | | Estructura con landmarks y encabezado principal para navegación rápida con lectores de pantalla. |
| ¿El sitio web incluye dos o más maneras de encontrar el contenido, como un menú de navegación, un robot de búsqueda o un mapa del sitio? | X | | Menú principal, buscadores de vuelos/promociones y mapa del sitio (`mapa_sitio.php`). |
| ¿El texto de enlace tiene sentido pleno, independientemente del contexto? | X | | Enlaces descriptivos ("Buscar nuevos vuelos", "Ver detalle completo", "Ir al mapa del sitio"). |

---

## COMPRENSIBLE
*Hacer el contenido y la interfaz de usuario comprensible para todos los usuarios*

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿La lengua de la página web o documento (o partes individuales de un documento multilingüe) aparece claramente definida? | X | | Atributo `lang="es"` declarado en la etiqueta `<html>`. |
| ¿Se han evitado enlaces, controles y campos de formularios que automáticamente desencadenan un cambio en el contexto? | X | | Los formularios solo se envían al pulsar el botón de envío; los selectores no redirigen automáticamente sin acción del usuario. |
| ¿El sitio web incluye una navegación consistente? | X | | Barra de navegación y pie de página uniformes en todo el sitio. |
| ¿Los formularios en línea proveen mensajes de verificación y error accesibles y útiles? | X | | Validaciones claras en cliente (`validaciones.js`) y servidor (`index.php` / `datos.php`) con alertas descriptivas. |

---

## ROBUSTEZ
*Hacer el contenido lo suficientemente robusto como para ser interpretado de forma fiable por una amplia variedad de agentes incluyendo ayudas técnicas.*

| Contenido | SI | NO | Observaciones |
| :--- | :---: | :---: | :--- |
| ¿La web está codificada utilizando HTML válido? | X | | HTML5 válido verificado con TAW (0 errores automáticos). |
| ¿Las interfaces web ricas, dinámicas, tales como ventanas modales, menús desplegables, pases de diapositivas y galerías incluyen marcadores ARIA? | X | | Menús colapsables, alertas, breadcrumbs, paginación y botones toggle implementan atributos ARIA (`aria-expanded`, `aria-current`, `aria-label`, `aria-pressed`). |
