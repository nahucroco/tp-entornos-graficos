/**
 * Script de validaciones del lado del cliente y comportamiento interactivo.
 * Cumple con validaciones de formulario accesibles y dinámicas.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Validación de formularios estándar con Bootstrap 5
    const forms = document.querySelectorAll('.needs-validation, [data-validar]');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
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
});
