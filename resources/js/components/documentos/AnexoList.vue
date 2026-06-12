<template>
  <div>
    <p class="text-label-md text-on-surface-variant uppercase mb-1 font-semibold">Anexos</p>
    <div v-if="anexos.length" class="flex flex-col gap-2 mt-2">
      <button
        v-for="anexo in anexos"
        :key="anexo.id ?? anexo.nombre"
        type="button"
        class="flex items-center justify-between bg-surface p-2 rounded text-on-surface cursor-pointer hover:bg-surface-container transition-colors w-full text-left"
        @click="$emit('download', anexo)"
      >
        <div class="flex items-center gap-2 min-w-0">
          <span class="material-symbols-outlined shrink-0" :class="iconClass(anexo.tipo)">
            {{ iconName(anexo.tipo) }}
          </span>
          <span class="text-body-sm font-medium truncate">{{ anexo.nombre }}</span>
        </div>
        <span class="material-symbols-outlined text-sm shrink-0">download</span>
      </button>
    </div>
    <p v-else class="text-body-sm text-on-surface-variant mt-2">Sin anexos registrados.</p>
  </div>
</template>

<script setup>
defineProps({
    anexos: { type: Array, default: () => [] },
});

defineEmits(['download']);

function iconName(tipo) {
    if (tipo === 'pdf') return 'picture_as_pdf';
    if (tipo === 'xlsx' || tipo === 'xls') return 'table_chart';
    return 'attach_file';
}

function iconClass(tipo) {
    if (tipo === 'pdf') return 'text-error';
    if (tipo === 'xlsx' || tipo === 'xls') return 'text-primary';
    return 'text-on-surface-variant';
}
</script>
