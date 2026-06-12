import { defineStore } from 'pinia';

export const useDocumentosStore = defineStore('documentos', {
    state: () => ({
        expedientes: [
            {
                id: 'EXP-2026-0045',
                tipo: 'Informe Técnico',
                asunto: 'Mantenimiento de vías - Sector San Juan de Acobamba',
                prioridad: 'alta', // alta, media, baja
                unidad_origen: 'Gerencia de Infraestructura',
                estado: 'EN TRÁNSITO', // REVISIÓN, APROBADO, RECIBIDO, EN TRÁNSITO
                fecha_creacion: '11/06/2026',
                fecha_actualizacion: '13/06/2026',
                oficina_actual: 'Secretaría General',
                detalles: 'Solicitud urgente de asignación de presupuesto y maquinaria para la reparación de vías afectadas por deslizamientos en el sector de San Juan de Acobamba.',
                historial: [
                    {
                        titulo: 'En Tránsito',
                        estado: 'ESTADO ACTUAL',
                        fecha: '13/06/2026 - 09:00',
                        descripcion: 'Expediente derivado a la oficina de Secretaría General para el trámite final de proveído.',
                        icono: 'pending',
                        color: 'secondary'
                    },
                    {
                        titulo: 'Firmado Electrónicamente',
                        estado: 'COMPLETADO',
                        fecha: '12/06/2026 - 15:42',
                        descripcion: 'Gerente Municipal firmó el proveído de aprobación del presupuesto preliminar.',
                        icono: 'draw',
                        color: 'primary',
                        extra: 'ID Firma: FIR-AC-2026-X99'
                    },
                    {
                        titulo: 'Derivado',
                        estado: 'COMPLETADO',
                        fecha: '11/06/2026 - 09:15',
                        descripcion: 'Expediente derivado a la Gerencia Municipal por parte del Ing. Carlos Ramos.',
                        icono: 'forward_to_inbox',
                        color: 'gray'
                    },
                    {
                        titulo: 'Creado y Registrado',
                        estado: 'COMPLETADO',
                        fecha: '11/06/2026 - 08:30',
                        descripcion: 'Documento técnico ingresado al sistema por la Gerencia de Infraestructura.',
                        icono: 'create_new_folder',
                        color: 'gray'
                    }
                ]
            },
            {
                id: 'EXP-2026-0038',
                tipo: 'Memorándum',
                asunto: 'Solicitud de personal - Campaña de Salud 2026',
                prioridad: 'media',
                unidad_origen: 'OMAPED',
                estado: 'REVISIÓN',
                fecha_creacion: '08/06/2026',
                fecha_actualizacion: '10/06/2026',
                oficina_actual: 'Gerencia Municipal',
                detalles: 'Requerimiento de 4 enfermeros y 2 médicos de apoyo para la campaña de vacunación municipal que se llevará a cabo del 15 al 20 de junio.',
                historial: [
                    {
                        titulo: 'En Revisión',
                        estado: 'ESTADO ACTUAL',
                        fecha: '10/06/2026 - 11:30',
                        descripcion: 'Evaluación de disponibilidad presupuestal por parte de la Gerencia Municipal.',
                        icono: 'pending',
                        color: 'secondary'
                    },
                    {
                        titulo: 'Creado y Registrado',
                        estado: 'COMPLETADO',
                        fecha: '08/06/2026 - 10:15',
                        descripcion: 'Solicitud ingresada por la oficina de OMAPED.',
                        icono: 'create_new_folder',
                        color: 'gray'
                    }
                ]
            },
            {
                id: 'EXP-2026-0022',
                tipo: 'Resolución',
                asunto: 'Designación de Comisión de Fiestas Patronales',
                prioridad: 'baja',
                unidad_origen: 'Gerencia Municipal',
                estado: 'APROBADO',
                fecha_creacion: '01/06/2026',
                fecha_actualizacion: '03/06/2026',
                oficina_actual: 'Alcaldía',
                detalles: 'Resolución de Alcaldía para formalizar la conformación de la comisión organizadora de las Fiestas Patronales de Acobamba 2026.',
                historial: [
                    {
                        titulo: 'Aprobado y Firmado',
                        estado: 'ESTADO ACTUAL',
                        fecha: '03/06/2026 - 16:45',
                        descripcion: 'Resolución de Alcaldía formalmente emitida y numerada.',
                        icono: 'check_circle',
                        color: 'primary'
                    },
                    {
                        titulo: 'Creado y Registrado',
                        estado: 'COMPLETADO',
                        fecha: '01/06/2026 - 14:00',
                        descripcion: 'Proyecto de resolución elaborado por Secretaría General.',
                        icono: 'create_new_folder',
                        color: 'gray'
                    }
                ]
            },
            {
                id: 'EXP-2026-0051',
                tipo: 'Oficio Circular',
                asunto: 'Actualización de registros de estado civil 1Q',
                prioridad: 'media',
                unidad_origen: 'Registro Civil',
                estado: 'RECIBIDO',
                fecha_creacion: '11/06/2026',
                fecha_actualizacion: '11/06/2026',
                oficina_actual: 'Gerencia de Administración',
                detalles: 'Informe consolidado sobre los registros de actas de nacimiento, matrimonio y defunción correspondientes al primer trimestre de 2026.',
                historial: [
                    {
                        titulo: 'Recibido',
                        estado: 'ESTADO ACTUAL',
                        fecha: '11/06/2026 - 17:00',
                        descripcion: 'Documento ingresado físicamente y recepcionado digitalmente en Gerencia de Administración.',
                        icono: 'mail',
                        color: 'primary'
                    },
                    {
                        titulo: 'Creado y Registrado',
                        estado: 'COMPLETADO',
                        fecha: '11/06/2026 - 15:30',
                        descripcion: 'Oficio emitido por la Oficina de Registro Civil.',
                        icono: 'create_new_folder',
                        color: 'gray'
                    }
                ]
            }
        ],
        noticias: [
            { id: 1, tag: 'Actualización del Sistema', tipo: 'primary', texto: 'Nueva versión de Inventario TI disponible este fin de semana.' },
            { id: 2, tag: 'Recursos Humanos', tipo: 'secondary', texto: 'Plazo para entrega de informes mensuales vence el 15.' }
        ],
        alertasTI: [
            { id: 1, equipo: 'Server-G-MUNI-01', estado: 'Crítico', color: 'error', gerencia: 'Informática', accion: 'Revisar' },
            { id: 2, equipo: 'Switch-Core-L2', estado: 'Alerta', color: 'secondary', gerencia: 'General', accion: 'Revisar' },
            { id: 3, equipo: 'PC-ADM-RE04', estado: 'Mantenimiento', color: 'secondary', gerencia: 'Rentas', accion: 'Programar' },
            { id: 4, equipo: 'Printer-HP-500-LOG', estado: 'Suministro', color: 'tertiary', gerencia: 'Logística', accion: 'Reponer' },
            { id: 5, equipo: 'Laptop-EX-AC09', estado: 'Obsolescencia', color: 'secondary', gerencia: 'Alcaldía', accion: 'Evaluar' }
        ]
    }),
    getters: {
        obtenerExpedientePorId: (state) => (id) => {
            return state.expedientes.find(exp => exp.id === id);
        },
        obtenerPendientesCount: (state) => {
            return state.expedientes.length;
        },
        obtenerUrgentesCount: (state) => {
            return state.expedientes.filter(exp => exp.prioridad === 'alta').length;
        }
    },
    actions: {
        registrarExpediente(datos) {
            const nextNum = Math.floor(1000 + Math.random() * 9000);
            const nuevoId = `EXP-2026-${nextNum}`;
            const hoyStr = new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
            const horaStr = new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

            const nuevo = {
                id: nuevoId,
                tipo: datos.tipo,
                asunto: datos.asunto,
                prioridad: datos.prioridad,
                unidad_origen: datos.unidad_origen || 'Mesa de Partes (Externo)',
                estado: 'RECIBIDO',
                fecha_creacion: hoyStr,
                fecha_actualizacion: hoyStr,
                oficina_actual: datos.destino || 'Secretaría General',
                detalles: datos.detalles || 'No se proporcionaron detalles adicionales.',
                historial: [
                    {
                        titulo: 'Creado y Registrado',
                        estado: 'ESTADO ACTUAL',
                        fecha: `${hoyStr} - ${horaStr}`,
                        descripcion: `Documento de tipo ${datos.tipo} registrado formalmente en el sistema.`,
                        icono: 'create_new_folder',
                        color: 'primary'
                    }
                ]
            };

            this.expedientes.unshift(nuevo);
            return nuevoId;
        },
        derivarExpediente(id, destinoOficina) {
            const exp = this.expedientes.find(e => e.id === id);
            if (exp) {
                const hoyStr = new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                const horaStr = new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

                // Update current state active node
                if (exp.historial.length > 0) {
                    exp.historial[0].estado = 'COMPLETADO';
                    exp.historial[0].color = 'gray';
                }

                exp.estado = 'EN TRÁNSITO';
                exp.oficina_actual = destinoOficina;
                exp.fecha_actualizacion = hoyStr;

                exp.historial.unshift({
                    titulo: 'En Tránsito',
                    estado: 'ESTADO ACTUAL',
                    fecha: `${hoyStr} - ${horaStr}`,
                    descripcion: `Expediente derivado a la oficina de ${destinoOficina} para su revisión y proveído.`,
                    icono: 'pending',
                    color: 'secondary'
                });
            }
        },
        devolverExpediente(id) {
            const exp = this.expedientes.find(e => e.id === id);
            if (exp) {
                const hoyStr = new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                const horaStr = new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

                if (exp.historial.length > 0) {
                    exp.historial[0].estado = 'COMPLETADO';
                    exp.historial[0].color = 'gray';
                }

                exp.estado = 'REVISIÓN';
                exp.fecha_actualizacion = hoyStr;

                exp.historial.unshift({
                    titulo: 'Devuelto',
                    estado: 'ESTADO ACTUAL',
                    fecha: `${hoyStr} - ${horaStr}`,
                    descripcion: `Expediente devuelto a la unidad de origen (${exp.unidad_origen}) por observaciones.`,
                    icono: 'undo',
                    color: 'secondary'
                });
            }
        }
    }
});
