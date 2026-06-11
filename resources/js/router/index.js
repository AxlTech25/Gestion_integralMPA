import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import LoginPage from '../pages/LoginPage.vue';
import DashboardPage from '../pages/DashboardPage.vue';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: LoginPage,
        meta: { guest: true },
    },
    {
        path: '/',
        name: 'dashboard',
        component: DashboardPage,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();

    if (!auth.initialized) {
        await auth.fetchUser();
    }

    if (to.meta.requiresAuth && !auth.user) {
        return { name: 'login' };
    }

    if (to.meta.guest && auth.user) {
        return { name: 'dashboard' };
    }
});

export default router;
