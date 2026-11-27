/**
 * Script de validaciones del lado del cliente y comportamiento interactivo.
 * Cumple con validaciones de formulario accesibles y dinámicas.
 */

/**
 * Mensajes de validación en español.
 * Los mensajes nativos del navegador salen en el idioma del navegador
 * (por ejemplo "Please fill out this field."), sin importar el lang de la página.
 * Se reemplazan con setCustomValidity según el tipo de error del campo.
 * Un campo puede definir su propio mensaje con el atributo data-error.
 */
const formatearFecha = (iso) => {
    const [a, m, d] = String(iso).split('-');
    return d && m && a ? `${d}/${m}/${a}` : iso;
};

const mensajeValidacion = (campo) => {
    const v = campo.validity;
    const tipo = (campo.type || '').toLowerCase();

    if (campo.dataset.error) return campo.dataset.error;

    if (v.valueMissing) {
        if (tipo === 'checkbox') return 'Marcá esta casilla para continuar.';
        if (tipo === 'radio') return 'Seleccioná una de las opciones.';
        if (tipo === 'file') return 'Seleccioná un archivo.';
        if (campo.tagName === 'SELECT') return 'Seleccioná una opción de la lista.';
        return 'Completá este campo.';
    }
    if (v.badInput) {
        if (tipo === 'number') return 'Ingresá un número válido.';
        if (tipo === 'date') return 'Ingresá una fecha válida.';
        return 'El valor ingresado no es válido.';
    }
    if (v.typeMismatch) {
        if (tipo === 'email') return 'Ingresá un correo electrónico válido (por ejemplo, nombre@correo.com).';
        if (tipo === 'url') return 'Ingresá una dirección web válida (por ejemplo, https://sitio.com).';
        return 'El valor ingresado no tiene el formato correcto.';
    }
    if (v.patternMismatch) {
        return campo.title ? `Formato incorrecto: ${campo.title}` : 'El valor ingresado no tiene el formato solicitado.';
    }
    if (v.tooShort) {
        return `Ingresá al menos ${campo.minLength} caracteres (actualmente tiene ${campo.value.length}).`;
    }
    if (v.tooLong) {
        return `Ingresá como máximo ${campo.maxLength} caracteres (actualmente tiene ${campo.value.length}).`;
    }
    if (v.rangeUnderflow) {
        return tipo === 'date'
            ? `La fecha debe ser igual o posterior al ${formatearFecha(campo.min)}.`
            : `El valor debe ser mayor o igual a ${campo.min}.`;
    }
    if (v.rangeOverflow) {
        return tipo === 'date'
            ? `La fecha debe ser igual o anterior al ${formatearFecha(campo.max)}.`
            : `El valor debe ser menor o igual a ${campo.max}.`;
    }
    if (v.stepMismatch) {
        return campo.step && campo.step !== 'any'
            ? `Ingresá un valor válido (múltiplo de ${campo.step}).`
            : 'Ingresá un valor válido.';
    }
    return 'El valor ingresado no es válido.';
};

const camposValidables = (raiz) =>
    raiz.querySelectorAll('input:not([type="hidden"]):not([type="submit"]):not([type="button"]), select, textarea');

// Devuelve (o crea) el contenedor .invalid-feedback donde se muestra el mensaje.
// Si el campo ya tiene uno escrito en el HTML, se respeta su texto.
const obtenerFeedback = (campo) => {
    if (campo.type === 'radio' || (campo.type === 'checkbox' && !campo.closest('.form-check'))) return null;

    const contenedor = campo.closest('.input-group') || campo.closest('.form-check') || campo.parentElement;
    let fb = Array.from(contenedor.children).find(el => el.classList.contains('invalid-feedback'));
    if (fb) return fb.dataset.auto ? fb : null;

    fb = document.createElement('div');
    fb.className = 'invalid-feedback';
    fb.dataset.auto = '1';
    fb.id = `${campo.id || campo.name || 'campo'}-error`;
    contenedor.appendChild(fb);
    if (contenedor.classList.contains('input-group')) contenedor.classList.add('has-validation');

    const descritoPor = (campo.getAttribute('aria-describedby') || '').split(' ').filter(Boolean);
    descritoPor.push(fb.id);
    campo.setAttribute('aria-describedby', descritoPor.join(' '));
    return fb;
};

const validarCampo = (campo) => {
    if (!campo.willValidate) return;
    campo.setCustomValidity('');
    const mensaje = campo.validity.valid ? '' : mensajeValidacion(campo);
    campo.setCustomValidity(mensaje);

    if (campo.form && campo.form.noValidate) {
        const fb = obtenerFeedback(campo);
        if (fb) fb.textContent = mensaje;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    // 0. Mensajes de validación en español para todos los formularios
    document.querySelectorAll('form').forEach(form => {
        camposValidables(form).forEach(campo => {
            validarCampo(campo);
            campo.addEventListener('input', () => validarCampo(campo));
            campo.addEventListener('change', () => validarCampo(campo));
            campo.addEventListener('invalid', () => validarCampo(campo));
        });
    });

    // 1. Validación de formularios estándar con Bootstrap 5
    const forms = document.querySelectorAll('.needs-validation, [data-validar]');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            camposValidables(form).forEach(validarCampo);
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // 2. Cálculo dinámico de descuento en el formulario de Checkout / Reserva
    const selectPromo = document.getElementById('select-promocion');
    const inputPrecioBase = document.getElementById('precio-base');
    const displayTotal = document.getElementById('display-precio-total');
    const displayDescuento = document.getElementById('display-descuento-monto');

    if (selectPromo && inputPrecioBase && displayTotal) {
        const calcularTotal = () => {
            const precioBase = parseFloat(inputPrecioBase.value) || 0;
            const selectedOption = selectPromo.options[selectPromo.selectedIndex];
            const porcentaje = parseFloat(selectedOption?.dataset?.descuento || 0);

            const montoDescuento = (precioBase * porcentaje) / 100;
            const total = Math.max(0, precioBase - montoDescuento);

            if (displayDescuento) {
                displayDescuento.textContent = montoDescuento > 0
                    ? `-$${montoDescuento.toLocaleString('es-AR', { minimumFractionDigits: 2 })} (${porcentaje}%)`
                    : '$0,00';
            }
            displayTotal.textContent = `$${total.toLocaleString('es-AR', { minimumFractionDigits: 2 })}`;
        };

        selectPromo.addEventListener('change', calcularTotal);
        calcularTotal();
    }

    // 3. Filtro en vivo para tablas de búsqueda rápida (aerolíneas, historial)
    const buscadorEnVivo = document.getElementById('filtro-tabla-input');
    if (buscadorEnVivo) {
        buscadorEnVivo.addEventListener('input', (e) => {
            const termino = e.target.value.toLowerCase().trim();
            const filas = document.querySelectorAll('[data-fila-busqueda]');
            filas.forEach(fila => {
                const texto = fila.textContent.toLowerCase();
                fila.style.display = texto.includes(termino) ? '' : 'none';
            });
        });
    }

    // 4. Botón mostrar/ocultar contraseña
    document.querySelectorAll('[data-toggle-password]').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-toggle-password');
            const input = targetId ? document.getElementById(targetId) : null;
            if (!input) return;

            const mostrar = input.type === 'password';
            input.type = mostrar ? 'text' : 'password';
            btn.setAttribute('aria-pressed', mostrar ? 'true' : 'false');
            btn.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');

            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-eye', !mostrar);
                icon.classList.toggle('bi-eye-slash', mostrar);
            }
        });
    });
});
