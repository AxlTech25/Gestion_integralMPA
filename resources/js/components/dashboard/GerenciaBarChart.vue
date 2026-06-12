<template>
  <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl hover-card transition-all duration-200">
    <div class="flex justify-between items-center mb-8">
      <h2 class="text-headline-md text-on-surface font-semibold">Tramitación por Gerencia</h2>
      <button type="button" class="material-symbols-outlined text-outline cursor-pointer hover:text-primary transition-colors" title="Opciones">
        more_vert
      </button>
    </div>
    <div class="h-64 flex flex-col justify-end">
      <div class="grid grid-cols-4 gap-4 items-end flex-1 h-full pt-4">
        <div
          v-for="gerencia in gerencias"
          :key="gerencia.nombre"
          class="flex flex-col items-center group h-full justify-end"
        >
          <div class="relative w-full">
            <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity">
              {{ gerencia.valor }}
            </div>
            <div
              :class="[gerencia.barClass, 'chart-bar w-full rounded-t-md hover:brightness-110 transition-all duration-500 cursor-pointer']"
              :style="{ height: animated ? gerencia.heightPct + '%' : '0%' }"
            />
          </div>
          <span class="text-[11px] mt-2 font-semibold text-on-surface-variant text-center leading-tight">
            {{ gerencia.nombre }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

defineProps({
    gerencias: { type: Array, required: true },
});

const animated = ref(false);

onMounted(() => {
    setTimeout(() => {
        animated.value = true;
    }, 300);
});
</script>
