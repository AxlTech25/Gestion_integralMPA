/** Datos mock para vista ejecutiva (SIAF/SIGA en producción) */
export const KPI_ESTRATEGICO = {
    expedientesPendientes: 1248,
    tendenciaPendientes: '+4.2% desde ayer',
    tramitadosHoy: 312,
    metaDiariaPct: 85,
    equiposEstables: 45,
};

export const TRAMITACION_GERENCIAS = [
    { nombre: 'Infraestructura', valor: 145, heightPct: 85, barClass: 'bg-primary' },
    { nombre: 'Salud', valor: 98, heightPct: 60, barClass: 'bg-secondary' },
    { nombre: 'Educación', valor: 112, heightPct: 70, barClass: 'bg-primary/60' },
    { nombre: 'Rentas', valor: 168, heightPct: 95, barClass: 'bg-secondary/70' },
];

export const SUGERENCIA_ESTRATEGICA = {
    titulo: 'Sugerencia Estratégica',
    texto: 'Se recomienda priorizar el mantenimiento preventivo en la Gerencia de Rentas para evitar retrasos en la recaudación trimestral.',
};

export const ALERTA_ESTADO_CLASSES = {
    error: 'bg-error-container text-on-error-container',
    secondary: 'bg-secondary-container text-on-secondary-container',
    tertiary: 'bg-tertiary-fixed text-on-tertiary-fixed',
};
