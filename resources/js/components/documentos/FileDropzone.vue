<template>
  <div class="space-y-2">
    <span class="block text-label-md text-on-surface font-semibold">Documento Adjunto (PDF/Digitalizado)</span>
    <div class="relative">
      <input
        ref="inputRef"
        class="hidden"
        type="file"
        accept=".pdf,application/pdf"
        @change="onInputChange"
      />
      <label
        :class="[
          hasFile || isDragging
            ? 'bg-primary/5 border-primary'
            : 'bg-surface-container-low hover:bg-surface-container-high hover:border-primary border-outline-variant',
          'flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-xl transition-all cursor-pointer'
        ]"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="onDrop"
        @click="inputRef?.click()"
      >
        <span class="material-symbols-outlined text-[32px] text-on-surface-variant mb-2">cloud_upload</span>
        <span class="text-label-md text-on-surface font-semibold">{{ labelText }}</span>
        <span class="text-body-sm text-on-surface-variant mt-1">Soporta PDF hasta {{ maxMb }}MB</span>
        <span v-if="error" class="text-body-sm text-error mt-2 font-medium">{{ error }}</span>
      </label>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    maxMb: { type: Number, default: 10 },
});

const emit = defineEmits(['file-selected', 'error']);

const inputRef = ref(null);
const fileName = ref('');
const error = ref('');
const isDragging = ref(false);

const hasFile = computed(() => Boolean(fileName.value));
const labelText = computed(() => fileName.value || 'Haga clic para subir o arrastre');

function validateFile(file) {
    if (!file) return false;
    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        error.value = 'Solo se permiten archivos PDF.';
        emit('error', error.value);
        return false;
    }
    const maxBytes = props.maxMb * 1024 * 1024;
    if (file.size > maxBytes) {
        error.value = `El archivo supera el límite de ${props.maxMb}MB.`;
        emit('error', error.value);
        return false;
    }
    error.value = '';
    return true;
}

function applyFile(file) {
    if (!validateFile(file)) return;
    fileName.value = file.name;
    emit('file-selected', file);
}

function onInputChange(e) {
    const file = e.target.files?.[0];
    if (file) applyFile(file);
}

function onDrop(e) {
    isDragging.value = false;
    const file = e.dataTransfer.files?.[0];
    if (file) applyFile(file);
}

function reset() {
    fileName.value = '';
    error.value = '';
    if (inputRef.value) inputRef.value.value = '';
}

defineExpose({ reset });
</script>
