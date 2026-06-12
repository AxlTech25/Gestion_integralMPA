/** Clases Tailwind por estado de expediente documental */
export const ESTADO_BADGE_CLASSES = {
    registrado: 'bg-surface-variant text-on-surface-variant',
    por_recepcionar: 'bg-secondary/15 text-secondary',
    en_tramite: 'bg-secondary-container text-on-secondary-container',
    devuelto: 'bg-error-container text-on-error-container',
    archivado: 'bg-primary-container text-on-primary-container',
    'REVISIÓN': 'bg-secondary-container text-on-secondary-container',
    'APROBADO': 'bg-primary-container text-on-primary-container',
    'RECIBIDO': 'bg-surface-variant text-on-surface-variant',
    'EN TRÁNSITO': 'bg-secondary/15 text-secondary',
};

export const ESTADO_LABELS = {
    registrado: 'Registrado',
    por_recepcionar: 'Por recepcionar',
    en_tramite: 'En trámite',
    devuelto: 'Devuelto',
    archivado: 'Archivado',
};

export function estadoBadgeClass(estado) {
    return ESTADO_BADGE_CLASSES[estado] ?? 'bg-surface-variant text-on-surface-variant';
}

/** Abrevia nombres largos de unidades para tablas compactas */
export function abreviarUnidad(unidad) {
    const map = {
        'Gerencia de Infraestructura': 'Infraestructura',
        'Gerencia de Administración': 'Administración',
        'Gerencia Municipal': 'Gerencia Municipal',
        'Asesoría Jurídica': 'Asesoría Jurídica',
        'Registro Civil': 'Registro Civil',
        'OMAPED': 'OMAPED',
        'Alcaldía': 'Alcaldía',
    };
    return map[unidad] ?? unidad;
}
