<template>
  <div class="max-w-5xl mx-auto space-y-8">
    <header>
      <h1 class="text-headline-lg font-bold text-primary">Núcleo del sistema</h1>
      <p class="text-body-md text-on-surface-variant mt-2">
        Seguridad, organigrama, usuarios y auditoría institucional (NÚCLEO).
      </p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <router-link
        v-for="item in items"
        :key="item.route"
        :to="{ name: item.route }"
        class="bg-surface rounded-xl border border-outline-variant p-6 hover:border-primary transition-colors group"
      >
        <span class="material-symbols-outlined text-primary text-3xl mb-3 group-hover:scale-110 transition-transform">{{ item.icon }}</span>
        <h2 class="text-title-md font-semibold text-on-surface">{{ item.label }}</h2>
        <p class="text-body-sm text-on-surface-variant mt-2">{{ item.description }}</p>
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
        route: 'nucleo-usuarios',
        label: 'Usuarios',
        icon: 'person',
        description: 'Gestionar cuentas, roles y traslados de unidad.',
        permission: 'core.usuarios.gestionar',
    },
    {
        route: 'nucleo-unidades',
        label: 'Organigrama',
        icon: 'account_tree',
        description: 'Consultar unidades operativas y gerencias.',
        permission: 'core.usuarios.gestionar',
    },
    {
        route: 'nucleo-auditoria',
        label: 'Auditoría',
        icon: 'history',
        description: 'Registro inalterable de operaciones del sistema.',
        permission: 'core.auditoria.consultar',
    },
];

const items = computed(() =>
    catalog.filter((item) => auth.can(item.permission))
);
</script>
