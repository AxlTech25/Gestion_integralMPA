<template>
  <div
    class="bg-surface-container-lowest border border-outline-variant p-4 flex flex-col justify-between h-32 hover:border-primary transition-colors cursor-default"
  >
    <div class="flex justify-between items-start">
      <span class="text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">{{ label }}</span>
      <span class="material-symbols-outlined" :class="iconClass">{{ icon }}</span>
    </div>
    <div class="flex items-end justify-between">
      <span class="text-headline-xl text-on-surface font-bold">{{ value }}</span>
      <span v-if="trend" class="flex items-center text-label-md font-semibold" :class="trend.class">
        <span v-if="trend.icon" class="material-symbols-outlined text-[16px]">{{ trend.icon }}</span>
        {{ trend.text }}
      </span>
    </div>
  </div>
</template>

<script setup>
defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    icon: { type: String, required: true },
    iconClass: { type: String, default: 'text-primary' },
    trend: {
        type: Object,
        default: null,
        // { text, icon?, class }
    },
});
</script>
