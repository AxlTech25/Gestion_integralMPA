<template>
<<<<<<< HEAD
  <div class="flex h-screen w-full font-sans bg-background text-on-surface overflow-hidden">
    <!-- Sidebar -->
    <aside
      :class="[
        isCollapsed ? 'w-sidebar-collapsed' : 'w-sidebar-width',
        'sidebar-transition flex flex-col h-full overflow-y-auto bg-inverse-surface text-primary-fixed z-50 shrink-0',
        'max-md:hidden'
      ]"
    >
      <div class="flex items-center gap-3 px-6 h-16 border-b border-outline/20 shrink-0">
        <div class="flex-shrink-0 w-8 h-8 rounded bg-primary-container flex items-center justify-center">
          <span class="material-symbols-outlined text-on-primary-container" style="font-variation-settings: 'FILL' 1">account_balance</span>
        </div>
        <div v-show="!isCollapsed" class="overflow-hidden whitespace-nowrap">
          <h1 class="text-headline-md text-primary-fixed font-semibold">Acobamba</h1>
          <p class="text-label-md text-surface-variant opacity-70 font-semibold">Gerencia Municipal</p>
        </div>
      </div>

      <div v-if="auth.can('doc.expediente.registrar')" class="px-4 py-6 shrink-0">
        <router-link
          :to="{ name: 'registro-expediente' }"
          :class="[
            isCollapsed ? 'p-2.5 justify-center' : 'py-2.5 px-4 gap-2',
            'flex items-center justify-center w-full bg-primary text-on-primary rounded text-label-md font-semibold hover:bg-primary-container transition-all active:scale-95 duration-150'
=======
  <div class="flex h-screen w-full font-sans bg-background overflow-hidden text-on-surface">
    <!-- Collapsible Sidebar -->
    <aside
      :class="[
        isCollapsed ? 'w-16' : 'w-sidebar-width',
        'sidebar-transition flex flex-col h-full overflow-y-auto bg-inverse-surface text-primary-fixed z-50 shrink-0 border-r border-outline/20'
      ]"
    >
      <!-- Sidebar Header -->
      <div class="flex items-center gap-3 px-4 h-16 border-b border-outline/20 shrink-0">
        <div class="flex-shrink-0 w-8 h-8 rounded bg-primary flex items-center justify-center border border-secondary">
          <span class="material-symbols-outlined text-on-primary text-[20px]" style="font-variation-settings: 'FILL' 1;">account_balance</span>
        </div>
        <div v-show="!isCollapsed" class="overflow-hidden whitespace-nowrap">
          <h1 class="font-headline-md text-[16px] font-bold text-primary-fixed leading-tight">Acobamba</h1>
          <p class="font-label-md text-[10px] text-surface-variant opacity-70 leading-none">Gerencia Municipal</p>
        </div>
      </div>

      <!-- Action Button -->
      <div class="px-3 py-6 shrink-0">
        <router-link
          :to="{ name: 'registro-expediente' }"
          :class="[
            isCollapsed ? 'p-2 justify-center' : 'py-2.5 px-4 justify-content-center gap-2',
            'flex items-center bg-primary text-on-primary rounded font-label-md text-label-md hover:bg-primary-container transition-all active:scale-95 duration-150 shadow'
>>>>>>> ee948b02ce7a4035c5f7fe51191eac1c09ddaf42
          ]"
          :title="isCollapsed ? 'Nuevo Documento' : ''"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
<<<<<<< HEAD
          <span v-show="!isCollapsed" class="whitespace-nowrap">Nuevo Documento</span>
        </router-link>
      </div>

      <nav class="flex-1 px-2 space-y-1 overflow-y-auto custom-scrollbar">
        <template v-for="item in auth.menu" :key="item.key">
          <router-link
            v-if="item.route"
            :to="{ name: item.route }"
            custom
            v-slot="{ href, navigate, isActive }"
          >
            <a
              :href="href"
              @click="navigate"
              :class="navItemClass(isActive || isSectionActive(item))"
              :title="isCollapsed ? item.label : ''"
            >
              <span
                class="material-symbols-outlined"
                :style="isActive || isSectionActive(item) ? { fontVariationSettings: 'FILL 1' } : {}"
              >{{ item.icon }}</span>
              <span v-show="!isCollapsed" class="whitespace-nowrap text-label-md font-semibold">{{ item.label }}</span>
            </a>
          </router-link>
          <span
            v-else
            :class="navItemClass(false)"
            :title="isCollapsed ? item.label : ''"
          >
            <span class="material-symbols-outlined">{{ item.icon }}</span>
            <span v-show="!isCollapsed" class="whitespace-nowrap text-label-md font-semibold opacity-60">{{ item.label }}</span>
          </span>
        </template>
      </nav>

=======
          <span v-show="!isCollapsed" class="whitespace-nowrap font-bold">Nuevo Documento</span>
        </router-link>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-1 px-2 space-y-1 overflow-y-auto custom-scrollbar">
        <!-- Dashboard Principal -->
        <router-link
          :to="{ name: 'dashboard' }"
          custom
          v-slot="{ href, navigate, isActive }"
        >
          <a
            :href="href"
            @click="navigate"
            :class="[
              isActive
                ? 'bg-primary/20 text-white border-l-4 border-secondary'
                : 'text-surface-variant hover:bg-surface-variant/10',
              'flex items-center gap-3 px-4 py-3 cursor-pointer transition-all rounded-r duration-150'
            ]"
            :title="isCollapsed ? 'Panel de Control' : ''"
          >
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">dashboard</span>
            <span v-show="!isCollapsed" class="whitespace-nowrap font-label-md text-label-md">Panel de Control</span>
          </a>
        </router-link>

        <!-- Dashboard Estratégico -->
        <router-link
          :to="{ name: 'dashboard-estrategico' }"
          custom
          v-slot="{ href, navigate, isActive }"
        >
          <a
            :href="href"
            @click="navigate"
            :class="[
              isActive
                ? 'bg-primary/20 text-white border-l-4 border-secondary'
                : 'text-surface-variant hover:bg-surface-variant/10',
              'flex items-center gap-3 px-4 py-3 cursor-pointer transition-all rounded-r duration-150'
            ]"
            :title="isCollapsed ? 'Dashboard Estratégico' : ''"
          >
            <span class="material-symbols-outlined">analytics</span>
            <span v-show="!isCollapsed" class="whitespace-nowrap font-label-md text-label-md">Dashboard Estratégico</span>
          </a>
        </router-link>

        <!-- Gestión Documental (Bandeja) -->
        <router-link
          :to="{ name: 'bandeja-pendientes' }"
          custom
          v-slot="{ href, navigate, isActive }"
        >
          <a
            :href="href"
            @click="navigate"
            :class="[
              isActive || $route.path.startsWith('/gestion-documental')
                ? 'bg-primary/20 text-white border-l-4 border-secondary'
                : 'text-surface-variant hover:bg-surface-variant/10',
              'flex items-center gap-3 px-4 py-3 cursor-pointer transition-all rounded-r duration-150'
            ]"
            :title="isCollapsed ? 'Gestión Documental' : ''"
          >
            <span class="material-symbols-outlined">description</span>
            <span v-show="!isCollapsed" class="whitespace-nowrap font-label-md text-label-md">Gestión Documental</span>
          </a>
        </router-link>

        <!-- Núcleo (Placeholder link) -->
        <a
          href="#"
          class="flex items-center gap-3 text-surface-variant hover:bg-surface-variant/10 px-4 py-3 cursor-pointer transition-all rounded-r duration-150"
          :title="isCollapsed ? 'Núcleo' : ''"
        >
          <span class="material-symbols-outlined">group</span>
          <span v-show="!isCollapsed" class="whitespace-nowrap font-label-md text-label-md">Núcleo</span>
        </a>

        <!-- Inventario IT (Placeholder link) -->
        <a
          href="#"
          class="flex items-center gap-3 text-surface-variant hover:bg-surface-variant/10 px-4 py-3 cursor-pointer transition-all rounded-r duration-150"
          :title="isCollapsed ? 'Inventario IT' : ''"
        >
          <span class="material-symbols-outlined">inventory_2</span>
          <span v-show="!isCollapsed" class="whitespace-nowrap font-label-md text-label-md">Inventario IT</span>
        </a>
      </nav>

      <!-- Sidebar Footer -->
>>>>>>> ee948b02ce7a4035c5f7fe51191eac1c09ddaf42
      <div class="p-2 border-t border-outline/20 shrink-0">
        <a
          @click.prevent="handleLogout"
          class="flex items-center gap-3 text-surface-variant px-4 py-3 cursor-pointer hover:bg-error/10 hover:text-error transition-all rounded"
          :title="isCollapsed ? 'Cerrar Sesión' : ''"
        >
<<<<<<< HEAD
          <span class="material-symbols-outlined">logout</span>
          <span v-show="!isCollapsed" class="whitespace-nowrap text-label-md font-semibold">Cerrar Sesión</span>
=======
          <span class="material-symbols-outlined text-[20px]">logout</span>
          <span v-show="!isCollapsed" class="whitespace-nowrap font-label-md text-label-md font-bold">Cerrar Sesión</span>
>>>>>>> ee948b02ce7a4035c5f7fe51191eac1c09ddaf42
        </a>
      </div>
    </aside>

<<<<<<< HEAD
    <!-- Main wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <header class="flex justify-between items-center w-full px-container-padding h-16 bg-surface border-b border-outline-variant z-40 shrink-0">
        <div class="flex items-center gap-4 min-w-0">
          <button
            type="button"
            class="p-2 hover:bg-surface-container-low rounded-full transition-colors active:opacity-80 cursor-pointer text-on-surface-variant"
            :title="isCollapsed ? 'Expandir menú' : 'Contraer menú'"
            @click="isCollapsed = !isCollapsed"
          >
            <span class="material-symbols-outlined">{{ isCollapsed ? 'menu' : 'menu_open' }}</span>
          </button>
          <div class="hidden md:block">
            <h2 class="text-headline-lg text-primary font-bold">SGMI Acobamba</h2>
          </div>

          <nav v-if="isNucleo" class="hidden lg:flex gap-6 ml-4">
            <router-link
              v-for="sub in auth.nucleoMenu"
              :key="sub.key"
              :to="{ name: sub.route }"
              custom
              v-slot="{ href, navigate, isActive }"
            >
              <a
                :href="href"
                @click="navigate"
                :class="[
                  isActive ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:bg-surface-container-low',
                  'h-16 flex items-center px-1 transition-colors'
                ]"
              >
                {{ sub.label }}
              </a>
            </router-link>
          </nav>

          <nav v-if="isPatrimonio" class="hidden lg:flex gap-6 ml-4">
            <router-link
              v-for="sub in patrimonioNav"
              :key="sub.key"
              :to="{ name: sub.route }"
              custom
              v-slot="{ href, navigate, isActive }"
            >
              <a
                :href="href"
                @click="navigate"
                :class="[
                  isActive ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:bg-surface-container-low',
                  'h-16 flex items-center px-1 transition-colors'
                ]"
              >
                {{ sub.label }}
              </a>
            </router-link>
          </nav>

          <nav v-if="isGestionDocumental" class="hidden lg:flex gap-6 ml-4">
            <router-link
              :to="{ name: 'bandeja-pendientes' }"
              custom
              v-slot="{ href, navigate, isActive }"
            >
              <a
                :href="href"
                @click="navigate"
                :class="[
                  isActive ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:bg-surface-container-low',
                  'h-16 flex items-center px-1 transition-colors'
                ]"
              >
                Bandeja
              </a>
            </router-link>
            <span class="text-on-surface-variant px-3 h-16 flex items-center cursor-default" title="Próximamente">Archivados</span>
            <span class="text-on-surface-variant px-3 h-16 flex items-center cursor-default" title="Próximamente">Reportes</span>
          </nav>
        </div>

        <div class="flex items-center gap-3 md:gap-6">
          <div class="hidden lg:flex items-center bg-surface-container-low px-4 py-2 rounded-full border border-outline-variant w-64">
            <span class="material-symbols-outlined text-on-surface-variant text-[20px]">search</span>
            <input
              v-model="busqueda"
              type="text"
              :placeholder="isGestionDocumental ? 'Buscar expediente...' : 'Buscar registros...'"
              class="bg-transparent border-none focus:ring-0 text-body-sm w-full placeholder:text-outline ml-2 text-on-surface focus:outline-none"
              @keydown.enter="ejecutarBusqueda"
            />
          </div>

          <div class="flex items-center gap-2 border-r border-outline-variant pr-4">
            <button type="button" class="p-2 text-on-surface-variant hover:bg-surface-container-low transition-colors rounded-full relative">
              <span class="material-symbols-outlined">notifications</span>
              <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full" />
            </button>
            <button type="button" class="p-2 text-on-surface-variant hover:bg-surface-container-low transition-colors rounded-full">
=======
    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- TopAppBar -->
      <header class="flex justify-between items-center w-full px-container-padding h-16 bg-surface border-b border-outline-variant z-40 shrink-0">
        <div class="flex items-center gap-4">
          <button
            @click="isCollapsed = !isCollapsed"
            class="p-2 hover:bg-surface-container-low rounded-full transition-colors active:opacity-80 cursor-pointer text-on-surface-variant"
            id="sidebar-toggle"
            title="Contraer/Expandir Sidebar"
          >
            <span class="material-symbols-outlined">{{ isCollapsed ? 'menu' : 'menu_open' }}</span>
          </button>
          <div>
            <h2 class="font-headline-lg text-headline-lg font-bold text-primary">SGMI Acobamba</h2>
          </div>
        </div>

        <div class="flex items-center gap-3 md:gap-6">
          <!-- Quick search -->
          <div class="hidden lg:flex items-center bg-surface-container-low px-4 py-2 rounded-full border border-outline-variant w-64">
            <span class="material-symbols-outlined text-on-surface-variant text-[20px]">search</span>
            <input
              class="bg-transparent border-none focus:outline-none focus:ring-0 text-body-sm font-body-sm w-full placeholder:text-outline ml-2 text-on-surface"
              placeholder="Buscar registros..."
              type="text"
            />
          </div>

          <!-- Icons -->
          <div class="flex items-center gap-2 border-r border-outline-variant pr-4">
            <button class="p-2 text-on-surface-variant hover:bg-surface-container-low transition-colors rounded-full relative">
              <span class="material-symbols-outlined">notifications</span>
              <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full"></span>
            </button>
            <button class="p-2 text-on-surface-variant hover:bg-surface-container-low transition-colors rounded-full">
>>>>>>> ee948b02ce7a4035c5f7fe51191eac1c09ddaf42
              <span class="material-symbols-outlined">settings</span>
            </button>
          </div>

<<<<<<< HEAD
          <div class="flex items-center gap-3 pl-2 cursor-pointer active:opacity-80">
            <div class="text-right hidden sm:block">
              <p class="text-label-md text-on-surface leading-tight font-semibold">{{ userName }}</p>
              <p class="text-[11px] text-on-surface-variant">{{ userRole }}</p>
            </div>
            <img
              v-if="!userInitials"
              alt="Foto de perfil"
              class="w-10 h-10 rounded-full border border-outline-variant object-cover"
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuAbwr_9xPPVLVIK-8WDVjoTRamQtOLFLiwelMCL5FTZ_-nIvDKVUUiqWYGf5qxLgFyHI6YXFZ7jOxpS3Mifi8ZemT_QFPphOw21dp7bD9YuwDySF91E6TQ_Mmd6IDtnvmNFzbA3wx49YYbWtvWnbWh-wRtIOTcVvUhhzVvpTT87n0GCxAQAeR-FkGDEuSj1WjD8xIq67hXWluwHiz50OqFIVLHJH-vTwFC56yIDKU8iv8hpnFceKS9K6IfawoTFbEC0hGaNLbRjSfs"
            />
            <div
              v-else
              class="w-10 h-10 rounded-full bg-secondary-fixed flex items-center justify-center border border-outline-variant text-on-secondary-fixed text-sm font-bold"
            >
              {{ userInitials }}
            </div>
=======
          <!-- User Profile -->
          <div class="flex items-center gap-3 pl-2 cursor-pointer active:opacity-80">
            <div class="text-right hidden sm:block">
              <p class="font-label-md text-label-md text-on-surface leading-tight font-bold">{{ userName }}</p>
              <p class="font-body-sm text-[11px] text-on-surface-variant">{{ userRole }}</p>
            </div>
            <div v-if="userInitials" class="w-10 h-10 rounded-full bg-secondary-fixed flex items-center justify-center border border-secondary cursor-pointer">
              <span class="text-on-secondary-fixed text-sm font-bold">{{ userInitials }}</span>
            </div>
            <img
              v-else
              alt="User photo"
              class="w-10 h-10 rounded-full border border-outline-variant object-cover"
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuAbwr_9xPPVLVIK-8WDVjoTRamQtOLFLiwelMCL5FTZ_-nIvDKVUUiqWYGf5qxLgFyHI6YXFZ7jOxpS3Mifi8ZemT_QFPphOw21dp7bD9YuwDySF91E6TQ_Mmd6IDtnvmNFzbA3wx49YYbWtvWnbWh-wRtIOTcVvUhhzVvpTT87n0GCxAQAeR-FkGDEuSj1WjD8xIq67hXWluwHiz50OqFIVLHJH-vTwFC56yIDKU8iv8hpnFceKS9K6IfawoTFbEC0hGaNLbRjSfs"
            />
>>>>>>> ee948b02ce7a4035c5f7fe51191eac1c09ddaf42
          </div>
        </div>
      </header>

<<<<<<< HEAD
      <main class="flex-1 overflow-y-auto p-container-padding bg-background relative custom-scrollbar pb-20 md:pb-container-padding">
        <router-view />
      </main>

      <footer class="bg-surface-container-high border-t border-outline-variant px-container-padding py-4 shrink-0 hidden md:block">
        <div class="flex flex-col md:flex-row justify-between items-center w-full gap-4">
          <p class="text-body-sm text-on-surface-variant">
            © {{ anio }} Municipalidad Provincial de Acobamba - SGMI
          </p>
          <div class="flex gap-6">
            <a class="text-body-sm text-on-surface-variant hover:text-primary transition-opacity underline" href="mailto:utis@mpa.gob.pe">Soporte Técnico</a>
            <a class="text-body-sm text-on-surface-variant hover:text-primary transition-opacity underline" href="#">Manual de Usuario</a>
            <a class="text-body-sm text-on-surface-variant hover:text-primary transition-opacity underline" href="#">Privacidad</a>
          </div>
        </div>
      </footer>
    </div>

    <!-- Mobile bottom nav -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-surface border-t border-outline-variant h-16 flex justify-around items-center px-4 z-50">
      <router-link :to="{ name: 'dashboard' }" class="flex flex-col items-center gap-1 text-on-surface-variant">
        <span class="material-symbols-outlined">dashboard</span>
        <span class="text-[10px] font-semibold">Panel</span>
      </router-link>
      <router-link :to="{ name: 'bandeja-pendientes' }" class="flex flex-col items-center gap-1 text-on-surface-variant">
        <span class="material-symbols-outlined">description</span>
        <span class="text-[10px] font-semibold">Bandeja</span>
      </router-link>
      <router-link
        :to="{ name: 'registro-expediente' }"
        class="bg-primary text-on-primary p-3 rounded-full -translate-y-4 shadow-lg active:scale-90 transition-transform"
      >
        <span class="material-symbols-outlined">add</span>
      </router-link>
      <router-link :to="{ name: 'patrimonio-inventario' }" class="flex flex-col items-center gap-1 text-on-surface-variant">
        <span class="material-symbols-outlined">inventory_2</span>
        <span class="text-[10px] font-semibold">Inv IT</span>
      </router-link>
      <router-link :to="{ name: 'dashboard-estrategico' }" class="flex flex-col items-center gap-1 text-on-surface-variant">
        <span class="material-symbols-outlined">analytics</span>
        <span class="text-[10px] font-semibold">Estratégico</span>
      </router-link>
    </nav>
=======
      <!-- Main Router Canvas -->
      <main class="flex-1 overflow-y-auto p-container-padding bg-background relative custom-scrollbar">
        <div class="relative z-10 max-w-7xl mx-auto space-y-6">
          <router-view />

          <!-- Shared Footer -->
          <footer class="mt-12 flex flex-col md:flex-row justify-between items-center py-6 border-t border-outline-variant text-on-surface-variant gap-4">
            <p class="font-body-sm text-body-sm">© 2026 Municipalidad Provincial de Acobamba - SGMI</p>
            <div class="flex gap-6">
              <a class="font-body-sm text-body-sm hover:text-primary transition-opacity underline" href="#">Soporte Técnico</a>
              <a class="font-body-sm text-body-sm hover:text-primary transition-opacity underline" href="#">Manual de Usuario</a>
              <a class="font-body-sm text-body-sm hover:text-primary transition-opacity underline" href="#">Privacidad</a>
            </div>
          </footer>
        </div>
      </main>
    </div>
>>>>>>> ee948b02ce7a4035c5f7fe51191eac1c09ddaf42
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
<<<<<<< HEAD
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useDocumentosStore } from '../stores/documentos';

const auth = useAuthStore();
const docsStore = useDocumentosStore();
const router = useRouter();
const route = useRoute();
const isCollapsed = ref(false);
const busqueda = ref('');
const anio = new Date().getFullYear();

const isGestionDocumental = computed(() => route.path.includes('gestion-documental'));
const isNucleo = computed(() => route.path.includes('/admin/nucleo'));
const isPatrimonio = computed(() => route.path.includes('/admin/patrimonio'));

const patrimonioNav = computed(() => {
    if (auth.patrimonioMenu?.length) return auth.patrimonioMenu;
    if (!auth.can('pat.equipo.consultar')) return [];
    return [
        { key: 'pat-inventario', label: 'Inventario', route: 'patrimonio-inventario' },
        ...(auth.can('pat.incidencia.gestionar')
            ? [{ key: 'pat-incidencias', label: 'Incidencias', route: 'patrimonio-incidencias' }]
            : []),
        { key: 'pat-semaforo', label: 'Semáforo ML', route: 'patrimonio-semaforo' },
    ];
});

const userName = computed(() => auth.user?.nombre_completo ?? 'Servidor Municipal');
const userRole = computed(() => auth.user?.unidad?.nombre ?? 'Gerencia Municipal');
const userInitials = computed(() => {
    if (!auth.user?.nombre_completo) return '';
    const names = auth.user.nombre_completo.split(' ');
    if (names.length >= 2) return (names[0][0] + names[1][0]).toUpperCase();
    return names[0][0].toUpperCase();
});

function isSectionActive(item) {
    if (item.key === 'gestion-documental') return isGestionDocumental.value;
    if (item.key === 'nucleo') return isNucleo.value;
    if (item.key === 'patrimonio') return isPatrimonio.value;
    return false;
}

function navItemClass(isActive) {
    return [
        isActive
            ? 'bg-primary text-on-primary border-l-4 border-secondary'
            : 'text-surface-variant hover:bg-surface-variant/10',
        'flex items-center gap-3 px-4 py-3 cursor-pointer active:scale-95 duration-150 transition-all rounded-r'
    ];
}

async function handleLogout() {
    await auth.logout();
    router.push({ name: 'login' });
}

async function ejecutarBusqueda() {
    const q = busqueda.value.trim();
    if (q.length < 2) return;

    if (!auth.can('doc.expediente.consultar')) {
        return;
    }

    const resultados = await docsStore.buscarExpedientes(q);

    if (resultados.length === 1) {
        router.push({ name: 'trazabilidad-expediente', params: { id: resultados[0].codigo } });
        return;
    }

    if (isGestionDocumental.value) {
        router.push({ name: 'bandeja-pendientes', query: { q } });
    } else if (resultados.length > 0) {
        router.push({ name: 'trazabilidad-expediente', params: { id: resultados[0].codigo } });
    }
=======
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const auth = ref(useAuthStore());
const router = useRouter();
const isCollapsed = ref(false);

const userName = computed(() => {
  return auth.value.user?.nombre_completo || 'Juan Pérez';
});

const userRole = computed(() => {
  return auth.value.user?.email ? 'Servidor Municipal' : 'Gerente Municipal';
});

const userInitials = computed(() => {
  if (!auth.value.user) return '';
  const names = auth.value.user.nombre_completo.split(' ');
  if (names.length >= 2) {
    return (names[0][0] + names[1][0]).toUpperCase();
  }
  return names[0][0].toUpperCase();
});

async function handleLogout() {
  await auth.value.logout();
  router.push({ name: 'login' });
>>>>>>> ee948b02ce7a4035c5f7fe51191eac1c09ddaf42
}
</script>
