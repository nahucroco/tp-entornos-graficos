/**
 * Esquemas de validación con Zod para todos los formularios del sitio.
 *
 * Cada campo indica qué esquema usa con el atributo data-zod="<nombre>".
 * Reglas entre campos:
 *   data-zod-igual-a="<name>"     el valor debe coincidir con otro campo (ej.: repetir contraseña)
 *   data-zod-distinto-de="<name>" el valor debe ser distinto de otro campo (ej.: origen / destino)
 *   data-zod-desde="<name>"       una fecha no puede ser anterior a la de otro campo
 *
 * validaciones.js llama a window.validacionZod(campo) después de la validación nativa del
 * navegador y muestra el mensaje debajo del campo. Si Zod no carga (sin conexión al CDN),
 * el sitio sigue funcionando con la validación nativa y la del servidor.
 * Las mismas reglas se repiten en PHP (app/helpers/validacion.php).
 */
import { z } from 'https://cdn.jsdelivr.net/npm/zod@3.23.8/+esm';

const soloDigitos = (v) => v.replace(/\D/g, '');

const texto = (etiqueta, min, max) => z.string().trim()
    .min(min, `${etiqueta} debe tener al menos ${min} caracteres.`)
    .max(max, `${etiqueta} no puede superar los ${max} caracteres.`);

const nombrePersona = (etiqueta) => texto(etiqueta, 2, 100)
    .regex(/^[\p{L}][\p{L} .'-]*$/u, `${etiqueta} solo puede contener letras, espacios, apóstrofos y guiones.`);

const ciudad = (etiqueta) => texto(etiqueta, 2, 50)
    .regex(/^[\p{L}0-9][\p{L}0-9 .,'()-]*$/u, `${etiqueta} contiene caracteres no permitidos.`);

const numero = (etiqueta, min, max, entero = false) => {
    let esquema = z.coerce.number({ invalid_type_error: `${etiqueta} debe ser un número.` })
        .min(min, `${etiqueta} debe ser mayor o igual a ${min}.`)
        .max(max, `${etiqueta} debe ser menor o igual a ${max}.`);
    if (entero) esquema = esquema.int(`${etiqueta} debe ser un número entero.`);
    return esquema;
};

export const esquemas = {
    nombrePersona: nombrePersona('El nombre'),
    apellido: nombrePersona('El apellido'),

    documento: z.string().trim().refine(
        (v) => /^\d{7,8}$/.test(v.replace(/\./g, '')) || /^(?=.*[A-Za-z])[A-Za-z0-9]{6,9}$/.test(v),
        'Ingresá un DNI de 7 u 8 dígitos o un pasaporte de 6 a 9 letras y números.'
    ),

    email: z.string().trim()
        .max(100, 'El correo no puede superar los 100 caracteres.')
        .email('Ingresá un correo electrónico válido (por ejemplo, nombre@correo.com).'),

    telefono: z.string().trim()
        .max(20, 'El teléfono no puede superar los 20 caracteres.')
        .regex(/^\+?[0-9 ()-]+$/, 'El teléfono solo puede tener números, espacios, guiones, paréntesis y el signo +.')
        .refine((v) => soloDigitos(v).length >= 8 && soloDigitos(v).length <= 15, 'El teléfono debe tener entre 8 y 15 dígitos.'),

    clave: z.string()
        .min(6, 'La contraseña debe tener al menos 6 caracteres.')
        .max(72, 'La contraseña no puede superar los 72 caracteres.'),

    direccion: texto('La dirección', 0, 150),
    mensajeContacto: texto('El mensaje', 10, 2000),

    ciudadOrigen: ciudad('El origen'),
    ciudadDestino: ciudad('El destino'),
    precioVuelo: numero('El precio', 1000, 10000000),
    asientos: numero('La cantidad de asientos', 1, 500, true),

    descripcionPromocion: texto('La descripción', 5, 200),
    descuento: numero('El porcentaje de descuento', 1, 90, true),

    nombreAerolinea: texto('El nombre de la aerolínea', 2, 100),
    codigoIata: z.string().trim().regex(/^[A-Za-z0-9]{2,3}$/, 'El código IATA debe tener 2 o 3 letras o números (por ejemplo, AR).'),
    codigoPais: z.string().trim().regex(/^[A-Za-z]{3}$/, 'El código de país debe tener exactamente 3 letras (por ejemplo, ARG).'),
    descripcionAerolinea: texto('La descripción', 0, 200),

    textoNovedad: texto('El texto de la novedad', 10, 200),
};

const otroCampo = (campo, nombre) => campo.form?.querySelector(`[name="${nombre}"]`);

/** Devuelve el mensaje de error de Zod para el campo, o '' si es válido. */
window.validacionZod = (campo) => {
    const valor = campo.value;
    // Los campos vacíos los resuelve el atributo required (mensaje "Completá este campo.").
    if (valor.trim() === '') return '';

    const esquema = esquemas[campo.dataset.zod];
    if (esquema) {
        const resultado = esquema.safeParse(valor);
        if (!resultado.success) return resultado.error.issues[0].message;
    }

    const igualA = campo.dataset.zodIgualA && otroCampo(campo, campo.dataset.zodIgualA);
    if (igualA && igualA.value !== valor) return 'Las contraseñas no coinciden.';

    const distintoDe = campo.dataset.zodDistintoDe && otroCampo(campo, campo.dataset.zodDistintoDe);
    if (distintoDe && distintoDe.value.trim().toLowerCase() === valor.trim().toLowerCase()) {
        return 'El destino no puede ser igual al origen.';
    }

    const desde = campo.dataset.zodDesde && otroCampo(campo, campo.dataset.zodDesde);
    if (desde && desde.value && valor < desde.value) {
        return 'Esta fecha no puede ser anterior a la fecha de inicio.';
    }

    return '';
};
