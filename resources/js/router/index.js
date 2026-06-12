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
import NucleoHubPage from '../pages/nucleo/NucleoHubPage.vue';
import UsuariosPage from '../pages/nucleo/UsuariosPage.vue';
import UnidadesPage from '../pages/nucleo/UnidadesPage.vue';
import AuditoriaPage from '../pages/nucleo/AuditoriaPage.vue';
import PatrimonioHubPage from '../pages/patrimonio/PatrimonioHubPage.vue';
import InventarioPage from '../pages/patrimonio/InventarioPage.vue';
import EquipoDetallePage from '../pages/patrimonio/EquipoDetallePage.vue';
import IncidenciasPage from '../pages/patrimonio/IncidenciasPage.vue';
import SemaforoPage from '../pages/patrimonio/SemaforoPage.vue';

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
                meta: { permission: 'dash.estrategico.ver' },
            },
            {
                path: 'gestion-documental/bandeja',
                name: 'bandeja-pendientes',
                component: BandejaPendientesPage,
                meta: { requiresOperacionDocumentaria: true },
            },
            {
                path: 'gestion-documental/registro',
                name: 'registro-expediente',
                component: RegistroExpedientePage,
                meta: { permission: 'doc.expediente.registrar' },
            },
            {
                path: 'gestion-documental/trazabilidad/:id?',
                name: 'trazabilidad-expediente',
                component: TrazabilidadExpedientePage,
            },
            {
                path: 'nucleo',
                name: 'nucleo',
                component: NucleoHubPage,
            },
            {
                path: 'nucleo/usuarios',
                name: 'nucleo-usuarios',
                component: UsuariosPage,
                meta: { permission: 'core.usuarios.gestionar' },
            },
            {
                path: 'nucleo/unidades',
                name: 'nucleo-unidades',
                component: UnidadesPage,
                meta: { permission: 'core.usuarios.gestionar' },
            },
            {
                path: 'nucleo/auditoria',
                name: 'nucleo-auditoria',
                component: AuditoriaPage,
                meta: { permission: 'core.auditoria.consultar' },
            },
            {
                path: 'patrimonio',
                name: 'patrimonio',
                component: PatrimonioHubPage,
                meta: { permission: 'pat.equipo.consultar' },
            },
            {
                path: 'patrimonio/inventario',
                name: 'patrimonio-inventario',
                component: InventarioPage,
                meta: { permission: 'pat.equipo.consultar' },
            },
            {
                path: 'patrimonio/equipo/:id',
                name: 'patrimonio-equipo',
                component: EquipoDetallePage,
                meta: { permission: 'pat.equipo.consultar' },
            },
            {
                path: 'patrimonio/incidencias',
                name: 'patrimonio-incidencias',
                component: IncidenciasPage,
                meta: { permission: 'pat.incidencia.gestionar' },
            },
            {
                path: 'patrimonio/semaforo',
                name: 'patrimonio-semaforo',
                component: SemaforoPage,
                meta: { permission: 'pat.equipo.consultar' },
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

    if (to.meta.permission && !auth.can(to.meta.permission)) {
        return { name: 'dashboard' };
    }

    if (to.meta.requiresOperacionDocumentaria && !auth.user?.puede_operar_documentaria) {
        return { name: 'dashboard' };
    }
});

export default router;
