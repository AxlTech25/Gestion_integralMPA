<template>
  <div class="bg-surface-container-low p-6 rounded-xl border border-outline-variant flex items-start gap-4">
    <div :class="[iconBg, 'p-3 rounded-lg shrink-0']">
      <span class="material-symbols-outlined">{{ icon }}</span>
    </div>
    <div>
      <h3 class="text-label-md text-on-surface font-semibold">{{ title }}</h3>
      <p class="text-body-sm text-on-surface-variant mt-1">{{ text }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
    icon: { type: String, required: true },
    title: { type: String, required: true },
    text: { type: String, required: true },
    iconBg: { type: String, required: true },
});
</script>
