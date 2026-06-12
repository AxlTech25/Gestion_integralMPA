<template>
  <div class="space-y-2">
    <label class="block text-label-md text-on-surface font-semibold">
      {{ label }}
      <span v-if="required" class="text-error font-bold">*</span>
    </label>
    <slot />
  </div>
</template>

<script setup>
defineProps({
    label: { type: String, required: true },
    required: { type: Boolean, default: false },
});
</script>
