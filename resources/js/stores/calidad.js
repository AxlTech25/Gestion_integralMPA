import { defineStore } from 'pinia';
import axios from 'axios';

export const useCalidadStore = defineStore('calidad', {
    state: () => ({
        resumen: null,
        noConformidades: [],
        ncActual: null,
        loading: false,
        error: null,
    }),
    actions: {
        async cargarResumen() {
            const { data } = await axios.get('/api/calidad/resumen');
            this.resumen = data;
            return data;
        },

        async cargarNoConformidades(params = {}) {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await axios.get('/api/no-conformidades', { params });
                this.noConformidades = data;
                return data;
            } catch (e) {
                this.error = e.response?.data?.message ?? 'No se pudo cargar las no conformidades.';
                throw e;
            } finally {
                this.loading = false;
            }
        },

        async cargarNc(id) {
            this.loading = true;
            try {
                const { data } = await axios.get(`/api/no-conformidades/${id}`);
                this.ncActual = data;
                return data;
            } finally {
                this.loading = false;
            }
        },

        async reportarNc(payload) {
            const { data } = await axios.post('/api/no-conformidades', payload);
            await this.cargarNoConformidades({ solo_abiertas: true });
            return data;
        },

        async actualizarNc(id, payload) {
            const { data } = await axios.put(`/api/no-conformidades/${id}`, payload);
            this.ncActual = data;
            return data;
        },

        async cerrarNc(id, payload) {
            const { data } = await axios.post(`/api/no-conformidades/${id}/cerrar`, payload);
            this.ncActual = data;
            return data;
        },

        async crearAc(ncId, payload) {
            const { data } = await axios.post(`/api/no-conformidades/${ncId}/acciones-correctivas`, payload);
            await this.cargarNc(ncId);
            return data;
        },

        async actualizarAc(acId, payload) {
            const { data } = await axios.put(`/api/acciones-correctivas/${acId}`, payload);
            if (this.ncActual?.id) {
                await this.cargarNc(this.ncActual.id);
            }
            return data;
        },
    },
});
