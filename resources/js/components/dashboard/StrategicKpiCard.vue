<template>
  <div
    class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl flex flex-col justify-between hover-card transition-all duration-200"
  >
    <div class="flex justify-between items-start">
      <div>
        <p class="text-label-md text-on-surface-variant mb-1 uppercase tracking-wider font-semibold">{{ label }}</p>
        <p class="text-headline-xl text-on-surface font-bold">{{ value }}</p>
      </div>
      <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center text-primary shrink-0">
        <span class="material-symbols-outlined text-[32px]">{{ icon }}</span>
      </div>
    </div>
    <div v-if="trend" class="mt-4 flex items-center text-body-sm font-semibold" :class="trend.class">
      <span v-if="trend.icon" class="material-symbols-outlined text-[18px] mr-1">{{ trend.icon }}</span>
      <span>{{ trend.text }}</span>
    </div>
  </div>
</template>

<script setup>
defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    icon: { type: String, required: true },
    trend: { type: Object, default: null },
});
</script>
