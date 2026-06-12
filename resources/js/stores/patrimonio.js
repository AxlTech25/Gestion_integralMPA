import { defineStore } from 'pinia';
import axios from 'axios';

export const usePatrimonioStore = defineStore('patrimonio', {
    state: () => ({
        equipos: [],
        meta: { total: 0, current_page: 1, last_page: 1 },
        equipoActual: null,
        incidencias: [],
        semaforo: { verde: 0, amarillo: 0, rojo: 0, total: 0 },
        criticos: [],
        loading: false,
        error: null,
    }),
    actions: {
        async cargarEquipos(params = {}) {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await axios.get('/api/equipos', { params });
                this.equipos = data.data ?? [];
                this.meta = data.meta ?? this.meta;
            } catch (e) {
                this.error = e.response?.data?.message ?? 'No se pudo cargar el inventario.';
                this.equipos = [];
                throw e;
            } finally {
                this.loading = false;
            }
        },

        async cargarEquipo(id) {
            this.loading = true;
            try {
                const { data } = await axios.get(`/api/equipos/${id}`);
                this.equipoActual = data;
                return data;
            } finally {
                this.loading = false;
            }
        },

        async registrarEquipo(payload) {
            const { data } = await axios.post('/api/equipos', payload);
            await this.cargarEquipos();
            return data;
        },

        async guardarFichaTecnica(equipoId, payload) {
            const { data } = await axios.post(`/api/equipos/${equipoId}/ficha-tecnica`, payload);
            await this.cargarEquipo(equipoId);
            return data;
        },

        async registrarMantenimiento(equipoId, payload) {
            const { data } = await axios.post(`/api/equipos/${equipoId}/mantenimiento`, payload);
            await this.cargarEquipo(equipoId);
            return data;
        },

        async cargarIncidencias(params = {}) {
            const { data } = await axios.get('/api/incidencias', { params });
            this.incidencias = data;
            return data;
        },

        async reportarIncidencia(payload) {
            const { data } = await axios.post('/api/incidencias', payload);
            await this.cargarIncidencias({ solo_abiertas: true });
            return data;
        },

        async actualizarIncidencia(id, payload) {
            const { data } = await axios.put(`/api/incidencias/${id}`, payload);
            await this.cargarIncidencias({ solo_abiertas: true });
            return data;
        },

        async cargarSemaforo() {
            const { data } = await axios.get('/api/ml/semaforo');
            this.semaforo = data;
            return data;
        },

        async cargarCriticos(nivel = 'rojo') {
            const { data } = await axios.get('/api/ml/criticos', { params: { nivel } });
            this.criticos = data;
            return data;
        },

        async ejecutarMl() {
            const { data } = await axios.post('/api/ml/ejecutar');
            await this.cargarSemaforo();
            await this.cargarCriticos('rojo');
            return data;
        },
    },
});
