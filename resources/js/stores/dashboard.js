import { defineStore } from 'pinia';
import axios from 'axios';

export const useDashboardStore = defineStore('dashboard', {
    state: () => ({
        operativo: null,
        estrategico: null,
        loading: false,
        error: null,
        periodoDias: 30,
    }),
    actions: {
        async cargarOperativo(dias = 30) {
            this.loading = true;
            this.error = null;
            this.periodoDias = dias;
            try {
                const { data } = await axios.get('/api/dashboard/operativo', { params: { dias } });
                this.operativo = data;
                return data;
            } catch (e) {
                this.error = e.response?.data?.message ?? 'No se pudo cargar el panel.';
                throw e;
            } finally {
                this.loading = false;
            }
        },

        async cargarEstrategico(dias = 30) {
            this.loading = true;
            this.error = null;
            this.periodoDias = dias;
            try {
                const { data } = await axios.get('/api/dashboard/estrategico', { params: { dias } });
                this.estrategico = data;
                return data;
            } catch (e) {
                this.error = e.response?.data?.message ?? 'No se pudo cargar el dashboard estratégico.';
                throw e;
            } finally {
                this.loading = false;
            }
        },
    },
});
