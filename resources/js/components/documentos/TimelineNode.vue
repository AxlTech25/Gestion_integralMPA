<template>
  <div class="timeline-item flex gap-6 relative">
    <div class="relative z-10 transition-transform duration-200" :class="{ 'scale-110': hovered }" @mouseenter="hovered = true" @mouseleave="hovered = false">
      <div :class="iconCircleClass">
        <span class="material-symbols-outlined" :style="isCurrent ? { fontVariationSettings: 'FILL 1' } : {}">
          {{ node.icono }}
        </span>
      </div>
    </div>

    <div :class="cardClass">
      <div class="flex justify-between items-start mb-2 gap-2">
        <h5 class="text-headline-md text-on-surface font-semibold">{{ node.titulo }}</h5>
        <span v-if="isCurrent" class="text-body-sm text-secondary font-bold px-2 py-1 bg-secondary/10 rounded shrink-0">
          ESTADO ACTUAL
        </span>
        <span v-else class="text-body-sm text-on-surface-variant bg-surface-container px-2 py-1 rounded shrink-0">
          {{ node.fecha }}
        </span>
      </div>

      <!-- En tránsito / estado actual -->
      <div v-if="isCurrent" class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div>
          <p class="text-label-md text-on-surface-variant uppercase mb-1 font-semibold">Fecha</p>
          <p class="text-body-md font-medium text-on-surface">{{ fechaSolo }}</p>
        </div>
        <div>
          <p class="text-label-md text-on-surface-variant uppercase mb-1 font-semibold">Oficina Actual</p>
          <p class="text-body-md font-medium text-on-surface">{{ oficinaActual }}</p>
        </div>
      </div>

      <!-- Firmado -->
      <div v-else-if="node.firmante || node.extra" class="flex flex-wrap items-center gap-4">
        <div v-if="node.firmante" class="flex items-center gap-2">
          <span class="material-symbols-outlined text-primary text-sm">verified_user</span>
          <p class="text-body-md font-medium text-on-surface">{{ node.firmante }}</p>
        </div>
        <span v-if="node.firmante && node.extra" class="text-on-surface-variant opacity-30 hidden sm:inline">|</span>
        <p v-if="node.extra" class="text-body-sm text-on-surface-variant italic">{{ node.extra }}</p>
        <p v-if="node.descripcion && !node.firmante" class="text-body-md text-on-surface-variant">{{ node.descripcion }}</p>
      </div>

      <!-- Derivado -->
      <template v-else-if="node.destino">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
          <div>
            <p class="text-label-md text-on-surface-variant uppercase mb-1 font-semibold">Destino</p>
            <p class="text-body-md font-medium text-on-surface">{{ node.destino }}</p>
          </div>
        </div>
        <div v-if="node.observacion" class="bg-surface p-3 rounded border-l-4 border-primary/20">
          <p class="text-label-md text-on-surface-variant font-bold mb-1">Observación:</p>
          <p class="text-body-md text-on-surface">{{ node.observacion }}</p>
        </div>
        <p v-if="node.descripcion && !node.observacion" class="text-body-md text-on-surface-variant mt-2">{{ node.descripcion }}</p>
      </template>

      <!-- Registrado -->
      <div v-else-if="node.usuario" class="grid grid-cols-2 gap-4">
        <div>
          <p class="text-label-md text-on-surface-variant uppercase mb-1 font-semibold">Usuario</p>
          <p class="text-body-md font-medium text-on-surface">{{ node.usuario }}</p>
        </div>
        <div>
          <p class="text-label-md text-on-surface-variant uppercase mb-1 font-semibold">Unidad</p>
          <p class="text-body-md font-medium text-on-surface">{{ node.unidad }}</p>
        </div>
      </div>

      <!-- Genérico -->
      <p v-else class="text-body-md text-on-surface-variant">{{ node.descripcion }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    node: { type: Object, required: true },
    oficinaActual: { type: String, default: '' },
});

const hovered = ref(false);

const isCurrent = computed(() => props.node.estado === 'ESTADO ACTUAL');

const fechaSolo = computed(() => props.node.fecha?.split(' - ')[0] ?? props.node.fecha);

const iconCircleClass = computed(() => {
    const base = 'w-12 h-12 rounded-full flex items-center justify-center shadow-md border-4 border-surface-container-lowest shrink-0';
    if (isCurrent.value) {
        return props.node.color === 'secondary'
            ? `${base} bg-secondary-container text-on-secondary-container`
            : `${base} bg-primary-container text-on-primary-container`;
    }
    if (props.node.color === 'primary') {
        return `${base} bg-primary-container text-on-primary-container`;
    }
    return `${base} bg-surface-container-highest text-on-surface`;
});

const cardClass = computed(() => {
    const base = 'flex-1 p-4 rounded-lg transition-colors';
    if (isCurrent.value) {
        return `${base} bg-surface-container-low border border-secondary/30`;
    }
    return `${base} bg-white border border-outline-variant hover:border-primary/40`;
});
</script>
