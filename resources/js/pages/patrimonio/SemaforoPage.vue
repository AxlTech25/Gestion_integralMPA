<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-headline-lg font-bold">Semáforo de riesgo ML</h1>
        <p class="text-body-sm text-on-surface-variant">Predicción Random Forest — mantenimiento prioritario.</p>
      </div>
      <button
        v-if="auth.can('pat.ficha.gestionar')"
        type="button"
        class="bg-primary text-on-primary px-4 py-2 rounded-lg font-semibold"
        :disabled="ejecutando"
        @click="recalcular"
      >
        {{ ejecutando ? 'Calculando…' : 'Recalcular predicciones' }}
      </button>
    </div>

    <div class="grid grid-cols-3 gap-4">
      <div class="bg-primary-container rounded-xl p-4 text-center">
        <p class="text-label-md font-semibold">Verde</p>
        <p class="text-headline-xl font-bold">{{ store.semaforo.verde }}</p>
      </div>
      <div class="bg-secondary-container rounded-xl p-4 text-center">
        <p class="text-label-md font-semibold">Amarillo</p>
        <p class="text-headline-xl font-bold">{{ store.semaforo.amarillo }}</p>
      </div>
      <div class="bg-error-container rounded-xl p-4 text-center">
        <p class="text-label-md font-semibold">Rojo</p>
        <p class="text-headline-xl font-bold">{{ store.semaforo.rojo }}</p>
      </div>
    </div>

    <div class="bg-surface border border-outline-variant rounded-xl overflow-hidden">
      <div class="px-4 py-3 border-b font-semibold">Equipos en riesgo crítico</div>
      <table class="w-full text-body-sm">
        <thead class="bg-surface-container-low">
          <tr>
            <th class="px-4 py-2 text-left">Código</th>
            <th class="px-4 py-2 text-left">Equipo</th>
            <th class="px-4 py-2 text-left">Unidad</th>
            <th class="px-4 py-2 text-left">Probabilidad</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in store.criticos" :key="c.equipo_id" class="border-t border-outline-variant/40">
            <td class="px-4 py-2 font-mono text-primary">
              <router-link :to="{ name: 'patrimonio-equipo', params: { id: c.equipo_id } }">{{ c.codigo_patrimonial }}</router-link>
            </td>
            <td class="px-4 py-2">{{ c.marca }} {{ c.modelo }}</td>
            <td class="px-4 py-2">{{ c.unidad }}</td>
            <td class="px-4 py-2 font-semibold text-error">{{ (c.probabilidad_falla * 100).toFixed(1) }}%</td>
          </tr>
          <tr v-if="!store.criticos.length">
            <td colspan="4" class="px-4 py-6 text-center text-on-surface-variant">Sin equipos en nivel rojo.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePatrimonioStore } from '../../stores/patrimonio';
import { useAuthStore } from '../../stores/auth';

const store = usePatrimonioStore();
const auth = useAuthStore();
const ejecutando = ref(false);

onMounted(async () => {
    await store.cargarSemaforo();
    await store.cargarCriticos('rojo');
});

async function recalcular() {
    ejecutando.value = true;
    try {
        await store.ejecutarMl();
    } finally {
        ejecutando.value = false;
    }
}
</script>
