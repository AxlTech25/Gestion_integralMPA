import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        initialized: false,
        loading: false,
        error: null,
    }),
    getters: {
        permisos: (state) => state.user?.permisos ?? [],
        menu: (state) => state.user?.menu ?? [],
        nucleoMenu: (state) => state.user?.nucleo_menu ?? [],
        patrimonioMenu: (state) => state.user?.patrimonio_menu ?? [],
        calidadMenu: (state) => state.user?.calidad_menu ?? [],
        can: (state) => (codigo) => {
            if (!state.user) return false;
            if (state.user.permisos?.includes(codigo)) return true;
            if (state.user.roles?.some((r) => r.codigo === 'ADMIN_SISTEMA')) return true;
            return false;
        },
    },
    actions: {
        async fetchUser() {
            try {
                const { data } = await axios.get('/api/user');
                this.user = data;
            } catch {
                this.user = null;
            } finally {
                this.initialized = true;
            }
        },
        async login(username, password) {
            this.loading = true;
            this.error = null;
            try {
                await axios.get('/sanctum/csrf-cookie');
                await axios.post('/api/login', { username, password });
                await this.fetchUser();
            } catch (e) {
                const data = e.response?.data;
                if (data?.errors?.username?.[0]) {
                    this.error = data.errors.username[0];
                } else if (data?.message && data.message !== 'The given data was invalid.') {
                    this.error = data.message;
                } else {
                    this.error = 'Credenciales inválidas. Verifique usuario y contraseña.';
                }
                throw e;
            } finally {
                this.loading = false;
            }
        },
        async logout() {
            await axios.post('/api/logout');
            this.user = null;
        },
    },
});
