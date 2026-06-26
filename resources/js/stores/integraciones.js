import { defineStore } from 'pinia';
import axios from 'axios';

export const useIntegracionesStore = defineStore('integraciones', {
    state: () => ({
        estado: null,
        logs: [],
        loading: false,
        syncing: null,
        error: null,
        lastResult: null,
    }),
    actions: {
        async cargarEstado() {
            const { data } = await axios.get('/api/integraciones/estado');
            this.estado = data;
            return data;
        },

        async cargarLogs(limit = 20) {
            const { data } = await axios.get('/api/integraciones/sync-logs', { params: { limit } });
            this.logs = data.data ?? [];
            return this.logs;
        },

        async syncSigaPatrimonio() {
            this.syncing = 'siga-patrimonio';
            this.error = null;
            try {
                const { data } = await axios.post('/api/integraciones/siga/patrimonio');
                this.lastResult = data;
                await Promise.all([this.cargarEstado(), this.cargarLogs()]);
                return data;
            } catch (e) {
                this.error = e.response?.data?.message ?? 'Error al sincronizar patrimonio SIGA.';
                throw e;
            } finally {
                this.syncing = null;
            }
        },

        async syncSigaOrganigrama() {
            this.syncing = 'siga-organigrama';
            this.error = null;
            try {
                const { data } = await axios.post('/api/integraciones/siga/organigrama');
                this.lastResult = data;
                await Promise.all([this.cargarEstado(), this.cargarLogs()]);
                return data;
            } catch (e) {
                this.error = e.response?.data?.message ?? 'Error al sincronizar organigrama SIGA.';
                throw e;
            } finally {
                this.syncing = null;
            }
        },

        async syncSiafEjecucion() {
            this.syncing = 'siaf';
            this.error = null;
            try {
                const { data } = await axios.post('/api/integraciones/siaf/ejecucion');
                this.lastResult = data;
                await Promise.all([this.cargarEstado(), this.cargarLogs()]);
                return data;
            } catch (e) {
                this.error = e.response?.data?.message ?? 'Error al sincronizar SIAF.';
                throw e;
            } finally {
                this.syncing = null;
            }
        },
    },
});
