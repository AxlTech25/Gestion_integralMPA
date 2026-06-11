import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        initialized: false,
        loading: false,
        error: null,
    }),
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
                this.error = e.response?.data?.message ?? 'Credenciales inválidas';
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
