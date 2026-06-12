<template>
  <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl hover-card transition-all duration-200">
    <div class="flex justify-between items-center mb-4">
      <p class="text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Equipos en Riesgo TI</p>
      <span class="material-symbols-outlined text-on-surface-variant">monitor_heart</span>
    </div>
    <div class="flex items-end justify-between h-20 px-2">
      <div v-for="item in items" :key="item.label" class="flex flex-col items-center">
        <div
          :class="[item.barClass, 'w-12 rounded-t-lg flex items-center justify-center font-bold text-sm chart-bar transition-all duration-500']"
          :style="{ height: animated ? item.heightPct + '%' : '0%' }"
        >
          <span :class="item.textClass">{{ item.count }}</span>
        </div>
        <span class="text-[10px] mt-1 text-on-surface-variant font-bold">{{ item.label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    critico: { type: Number, default: 0 },
    alerta: { type: Number, default: 0 },
    estable: { type: Number, default: 0 },
});

const animated = ref(false);

const items = computed(() => [
    {
        label: 'CRÍTICO',
        count: props.critico,
        heightPct: 20,
        barClass: 'bg-error text-white',
        textClass: 'text-[10px] text-white',
    },
    {
        label: 'ALERTA',
        count: props.alerta,
        heightPct: 50,
        barClass: 'bg-secondary text-on-secondary-fixed',
        textClass: 'text-[10px] text-on-secondary-fixed',
    },
    {
        label: 'ESTABLE',
        count: props.estable,
        heightPct: 100,
        barClass: 'bg-primary text-white',
        textClass: 'text-[10px] text-white',
    },
]);

onMounted(() => {
    setTimeout(() => {
        animated.value = true;
    }, 100);
});
</script>
