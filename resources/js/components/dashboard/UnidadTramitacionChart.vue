<template>
  <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl hover-card transition-all duration-200">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-headline-md text-on-surface font-semibold">Pendientes por unidad</h2>
        <p class="text-body-sm text-on-surface-variant mt-1">Tiempo promedio en trámite (días)</p>
      </div>
    </div>
    <div v-if="!unidades.length" class="text-center text-on-surface-variant py-12 text-body-sm">
      Sin expedientes pendientes en el periodo.
    </div>
    <div v-else class="space-y-4">
      <div
        v-for="unidad in unidades"
        :key="unidad.unidad_id"
        class="group"
      >
        <div class="flex justify-between text-body-sm mb-1">
          <span class="font-semibold text-on-surface">{{ unidad.nombre }}</span>
          <span class="text-on-surface-variant">
            {{ unidad.pendientes }} pend. · {{ unidad.promedio_dias }} días prom.
          </span>
        </div>
        <div class="h-3 bg-surface-container rounded-full overflow-hidden">
          <div
            :class="[unidad.barClass, 'h-full rounded-full transition-all duration-500']"
            :style="{ width: animated ? unidad.heightPct + '%' : '0%' }"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

defineProps({
    unidades: { type: Array, required: true },
});

const animated = ref(false);

onMounted(() => {
    setTimeout(() => {
        animated.value = true;
    }, 300);
});
</script>
