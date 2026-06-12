<template>
  <div class="max-w-5xl mx-auto space-y-8">
    <header>
      <h1 class="text-headline-lg font-bold text-primary">Patrimonio TI</h1>
      <p class="text-body-md text-on-surface-variant mt-2">
        Inventario de equipos municipales, fichas técnicas, incidencias y análisis predictivo (MOD-PAT-TI).
      </p>
    </header>

    <div v-if="items.length" class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <router-link
        v-for="item in items"
        :key="item.route"
        :to="{ name: item.route }"
        class="bg-surface rounded-xl border border-outline-variant p-6 hover:border-primary transition-colors group"
      >
        <span class="material-symbols-outlined text-primary text-3xl mb-3 group-hover:scale-110 transition-transform">{{ item.icon }}</span>
        <h2 class="text-headline-sm font-semibold text-on-surface">{{ item.label }}</h2>
        <p class="text-body-sm text-on-surface-variant mt-2">{{ item.description }}</p>
      </router-link>
    </div>
    <div
      v-else
      class="bg-surface border border-outline-variant rounded-xl p-8 text-center space-y-4"
    >
      <p class="text-on-surface-variant">No hay módulos visibles con su perfil actual.</p>
      <router-link
        v-if="auth.can('pat.equipo.consultar')"
        :to="{ name: 'patrimonio-inventario' }"
        class="inline-block bg-primary text-on-primary px-6 py-2 rounded-lg font-semibold"
      >
        Ir al inventario
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../../stores/auth';

const auth = useAuthStore();

const catalog = [
    {
        route: 'patrimonio-inventario',
        label: 'Inventario',
        icon: 'inventory_2',
        description: 'Equipos municipales, custodio y estado operativo.',
        permission: 'pat.equipo.consultar',
    },
    {
        route: 'patrimonio-incidencias',
        label: 'Incidencias',
        icon: 'support_agent',
        description: 'Panel de soporte UTIS: fallas y requerimientos.',
        permission: 'pat.incidencia.gestionar',
    },
    {
        route: 'patrimonio-semaforo',
        label: 'Semáforo ML',
        icon: 'insights',
        description: 'Riesgo predictivo Random Forest por equipo.',
        permission: 'pat.equipo.consultar',
    },
];

const items = computed(() =>
    catalog.filter((item) => !item.permission || auth.can(item.permission))
);
</script>
