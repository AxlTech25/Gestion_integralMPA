import { defineStore } from 'pinia';
import axios from 'axios';

export const useDocumentosStore = defineStore('documentos', {
    state: () => ({
        expedientes: [],
        resumen: {
            total: 0,
            urgentes: 0,
            por_recepcionar: 0,
            promedio_dias: 0,
        },
        expedienteActual: null,
        tiposDocumentales: [],
        unidadesDerivacion: [],
        loading: false,
        error: null,
        noticias: [
            { id: 1, tag: 'MOD-DOC', tipo: 'primary', texto: 'Bandeja y trazabilidad conectadas al servidor SGMI.' },
            { id: 2, tag: 'Recepción digital', tipo: 'secondary', texto: 'Recepcione expedientes derivados antes de tramitar.' },
        ],
        alertasTI: [
            { id: 1, equipo: 'PC-UTIS-01', problema: 'Disco SSD 92%', color: 'error', accion: 'Revisar' },
            { id: 2, equipo: 'SRV-BACKUP', problema: 'Backup pendiente', color: 'secondary', accion: 'Programar' },
        ],
    }),
    getters: {
        obtenerExpedientePorCodigo: (state) => (codigo) => {
            if (state.expedienteActual?.codigo === codigo) {
                return state.expedienteActual;
            }
            return state.expedientes.find((exp) => exp.codigo === codigo);
        },
        obtenerPendientesCount: (state) => state.resumen.total,
        obtenerUrgentesCount: (state) => state.resumen.urgentes,
    },
    actions: {
        async cargarBandeja(params = {}) {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await axios.get('/api/expedientes/bandeja', { params });
                this.expedientes = data.expedientes ?? [];
                this.resumen = data.resumen ?? this.resumen;
            } catch (e) {
                this.error = e.response?.data?.message ?? 'No se pudo cargar la bandeja.';
                throw e;
            } finally {
                this.loading = false;
            }
        },

        async cargarTiposDocumentales() {
            const { data } = await axios.get('/api/tipos-documentales');
            this.tiposDocumentales = data;
            return data;
        },

        async previewCodigo(tipoId) {
            const { data } = await axios.get(`/api/tipos-documentales/${tipoId}/preview-codigo`);
            return data.codigo_preview;
        },

        async cargarUnidadesDerivacion() {
            const { data } = await axios.get('/api/unidades/derivacion');
            this.unidadesDerivacion = data;
            return data;
        },

        async cargarExpediente(codigo) {
            this.loading = true;
            try {
                const { data } = await axios.get(`/api/expedientes/codigo/${encodeURIComponent(codigo)}`);
                this.expedienteActual = data;
                return data;
            } finally {
                this.loading = false;
            }
        },

        async registrarExpediente(formData) {
            const { data } = await axios.post('/api/expedientes', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            this.expedienteActual = data;
            await this.cargarBandeja();
            return data.codigo;
        },

        async derivarExpediente(expedienteId, unidadDestinoId, proveido = null) {
            const { data } = await axios.post(`/api/expedientes/${expedienteId}/derivar`, {
                unidad_destino_id: unidadDestinoId,
                proveido,
            });
            this.expedienteActual = data;
            await this.cargarBandeja();
            return data;
        },

        async recepcionarExpediente(expedienteId) {
            const { data } = await axios.post(`/api/expedientes/${expedienteId}/recepcionar`);
            this.expedienteActual = data;
            await this.cargarBandeja();
            return data;
        },

        async devolverExpediente(expedienteId, observacion) {
            const { data } = await axios.post(`/api/expedientes/${expedienteId}/devolver`, {
                observacion,
            });
            this.expedienteActual = data;
            await this.cargarBandeja();
            return data;
        },

        async archivarExpediente(expedienteId) {
            const { data } = await axios.post(`/api/expedientes/${expedienteId}/archivar`);
            this.expedienteActual = data;
            await this.cargarBandeja();
            return data;
        },

        async firmarDocumento(documentoId) {
            const { data } = await axios.post(`/api/documentos/${documentoId}/firmar`);
            if (this.expedienteActual?.codigo === data.expediente_codigo) {
                await this.cargarExpediente(data.expediente_codigo);
            }
            return data;
        },

        async buscarExpedientes(q) {
            const { data } = await axios.get('/api/expedientes/buscar', { params: { q } });
            return data;
        },

        async descargarAdjunto(adjuntoId, nombreArchivo) {
            const response = await axios.get(`/api/expedientes/adjuntos/${adjuntoId}/download`, {
                responseType: 'blob',
            });
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', nombreArchivo);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        },
    },
});
