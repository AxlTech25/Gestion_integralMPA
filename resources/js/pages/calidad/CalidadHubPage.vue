<template>
  <div class="max-w-5xl mx-auto space-y-8">
    <header>
      <h1 class="text-headline-lg font-bold text-primary">Calidad — SGC ISO 9001</h1>
      <p class="text-body-md text-on-surface-variant mt-2">
        No conformidades, acciones correctivas y mejora continua (cláusula 10.2).
      </p>
    </header>

    <div v-if="resumen" class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-surface border border-outline-variant rounded-xl p-4 text-center">
        <p class="text-label-md text-on-surface-variant">Abiertas</p>
        <p class="text-headline-lg font-bold text-error">{{ resumen.abiertas }}</p>
      </div>
      <div class="bg-surface border border-outline-variant rounded-xl p-4 text-center">
        <p class="text-label-md text-on-surface-variant">Cerradas</p>
        <p class="text-headline-lg font-bold text-primary">{{ resumen.cerradas }}</p>
      </div>
      <div class="bg-surface border border-outline-variant rounded-xl p-4 text-center">
        <p class="text-label-md text-on-surface-variant">Con AC pendiente</p>
        <p class="text-headline-lg font-bold">{{ resumen.con_ac_pendiente }}</p>
      </div>
      <div class="bg-surface border border-outline-variant rounded-xl p-4 text-center">
        <p class="text-label-md text-on-surface-variant">AC abiertas</p>
        <p class="text-headline-lg font-bold text-secondary">{{ resumen.ac_abiertas }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <router-link
        :to="{ name: 'calidad-no-conformidades' }"
        class="bg-surface rounded-xl border border-outline-variant p-6 hover:border-primary transition-colors group"
      >
        <span class="material-symbols-outlined text-primary text-3xl mb-3">report</span>
        <h2 class="text-title-md font-semibold">No conformidades</h2>
        <p class="text-body-sm text-on-surface-variant mt-2">Registrar NC (F-ISO-01) y gestionar acciones correctivas (F-ISO-02).</p>
      </router-link>
      <div class="bg-surface-container-low rounded-xl border border-outline-variant p-6 opacity-90">
        <span class="material-symbols-outlined text-on-surface-variant text-3xl mb-3">description</span>
        <h2 class="text-title-md font-semibold text-on-surface">Documentación SGC</h2>
        <p class="text-body-sm text-on-surface-variant mt-2">
          Procedimientos y formatos en <code class="text-xs">docs/ISO/</code> del repositorio.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useCalidadStore } from '../../stores/calidad';
import { useAuthStore } from '../../stores/auth';

const store = useCalidadStore();
const auth = useAuthStore();
const resumen = ref(null);

onMounted(async () => {
    if (auth.can('calidad.nc.consultar') || auth.can('calidad.nc.gestionar')) {
        resumen.value = await store.cargarResumen();
    }
});
</script>
