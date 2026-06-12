<template>
  <span :class="[estadoBadgeClass(estado), 'px-2 py-0.5 rounded-full text-label-md text-[10px] font-bold uppercase']">
    {{ estado }}
  </span>
</template>

<script setup>
import { estadoBadgeClass } from '../../utils/estadoDocumento';

defineProps({
    estado: { type: String, required: true },
});
</script>
