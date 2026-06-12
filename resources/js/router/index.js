import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

import AppLayout from '../layouts/AppLayout.vue';
import PortalPage from '../pages/PortalPage.vue';
import LoginPage from '../pages/LoginPage.vue';
import DashboardPage from '../pages/DashboardPage.vue';
import DashboardEstrategicoPage from '../pages/DashboardEstrategicoPage.vue';
import BandejaPendientesPage from '../pages/BandejaPendientesPage.vue';
import RegistroExpedientePage from '../pages/RegistroExpedientePage.vue';
import TrazabilidadExpedientePage from '../pages/TrazabilidadExpedientePage.vue';

const routes = [
    {
        path: '/',
        redirect: { name: 'portal' },
    },
    {
        path: '/portal',
        name: 'portal',
        component: PortalPage,
        meta: { guest: true },
    },
    {
        path: '/login',
        name: 'login',
        component: LoginPage,
        meta: { guest: true },
    },
    {
        path: '/admin',
        component: AppLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: 'dashboard',
                name: 'dashboard',
                component: DashboardPage,
            },
            {
                path: 'dashboard-estrategico',
                name: 'dashboard-estrategico',
                component: DashboardEstrategicoPage,
            },
            {
                path: 'gestion-documental/bandeja',
                name: 'bandeja-pendientes',
                component: BandejaPendientesPage,
            },
            {
                path: 'gestion-documental/registro',
                name: 'registro-expediente',
                component: RegistroExpedientePage,
            },
            {
                path: 'gestion-documental/trazabilidad/:id?',
                name: 'trazabilidad-expediente',
                component: TrazabilidadExpedientePage,
            },
        ],
    },
    // Fallback redirect
    {
        path: '/:pathMatch(.*)*',
        redirect: { name: 'portal' },
    }
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

    // Redirect authenticated users trying to access guest pages
    // Exception: we can allow them to view the portal page
    if (to.meta.guest && auth.user && to.name !== 'portal') {
        return { name: 'dashboard' };
    }
});

export default router;
