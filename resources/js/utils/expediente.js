/** Parsea fecha dd/mm/yyyy a Date */
export function parseFechaExpediente(fechaStr) {
    if (!fechaStr) return null;
    const parte = fechaStr.split(' - ')[0].trim();
    const [dia, mes, anio] = parte.split('/').map(Number);
    if (!dia || !mes || !anio) return null;
    return new Date(anio, mes - 1, dia);
}

/** Calcula tiempo transcurrido entre dos fechas en formato legible */
export function calcularTiempoGestión(fechaInicio, fechaFin) {
    const inicio = parseFechaExpediente(fechaInicio);
    const fin = parseFechaExpediente(fechaFin);
    if (!inicio || !fin) return '—';

    const diffMs = Math.max(0, fin.getTime() - inicio.getTime());
    const dias = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    const horas = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

    if (dias === 0) return `${horas} horas`;
    if (horas === 0) return `${dias} ${dias === 1 ? 'día' : 'días'}`;
    return `${dias} ${dias === 1 ? 'día' : 'días'} ${horas} horas`;
}

export const PRIORIDAD_LABELS = {
    alta: 'ALTA',
    media: 'MEDIA',
    baja: 'BAJA',
};

export const PRIORIDAD_DOT = {
    alta: 'bg-error',
    media: 'bg-secondary',
    baja: 'bg-outline',
};
