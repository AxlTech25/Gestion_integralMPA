export const TIPOS_DOCUMENTALES = [
    { value: 'Oficio', label: 'Oficio' },
    { value: 'Memorándum', label: 'Memorándum' },
    { value: 'Informe Técnico', label: 'Informe Técnico' },
    { value: 'Solicitud Ciudadana', label: 'Solicitud Ciudadana' },
    { value: 'Resolución', label: 'Resolución de Alcaldía' },
];

export const UNIDADES_ORIGEN = [
    'Gerencia de Administración',
    'Gerencia de Infraestructura',
    'Oficina de Presupuesto',
    'Asesoría Jurídica',
    'Mesa de Partes (Externo)',
];

export const PRIORIDADES_REGISTRO = [
    { value: 'baja', label: 'Baja', inputClass: 'text-primary focus:ring-primary' },
    { value: 'media', label: 'Normal', inputClass: 'text-primary focus:ring-primary', default: true },
    { value: 'alta', label: 'Urgente', inputClass: 'text-error focus:ring-error' },
    { value: 'muy_alta', label: 'Muy Urgente', inputClass: 'text-tertiary focus:ring-tertiary' },
];

export const INFO_REGISTRO_EXPEDIENTE = [
    {
        icon: 'timer',
        title: 'Plazo Estimado',
        text: 'El tiempo promedio de respuesta para este tipo documental es de 72 horas hábiles.',
        iconBg: 'bg-secondary-container text-on-secondary-container',
    },
    {
        icon: 'history_edu',
        title: 'Trazabilidad',
        text: 'Cada paso del expediente será registrado y notificado a la unidad de origen automáticamente.',
        iconBg: 'bg-tertiary-container text-on-tertiary-container',
    },
    {
        icon: 'security',
        title: 'Confidencialidad',
        text: 'Los documentos adjuntos están protegidos bajo protocolos de seguridad institucional.',
        iconBg: 'bg-primary-container text-on-primary-container',
    },
];

export const MAX_ARCHIVO_MB = 10;

/** Convierte prioridad del formulario al valor almacenado en expediente */
export function prioridadParaStore(valor) {
    return valor === 'muy_alta' ? 'alta' : valor;
}
