<template>
  <div class="space-y-6">
    <header>
      <h1 class="text-headline-lg font-bold text-primary">Organigrama institucional</h1>
      <p class="text-body-sm text-on-surface-variant">
        Unidades operativas y gerencias. UTIS puede activar o desactivar unidades y marcar destinos de derivación (PA-05).
      </p>
    </header>

    <div v-if="error" class="bg-error-container/30 border border-error rounded-lg p-3 text-body-sm text-on-error-container">
      {{ error }}
    </div>

    <div v-if="loading" class="text-on-surface-variant">Cargando organigrama...</div>
    <div v-else class="bg-surface border border-outline-variant rounded-xl p-6">
      <UnidadTree :nodes="tree" editable @update="actualizarUnidad" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import UnidadTree from '../../components/nucleo/UnidadTree.vue';

const tree = ref([]);
const loading = ref(true);
const error = ref('');

onMounted(async () => {
    await cargar();
});

async function cargar() {
    loading.value = true;
    error.value = '';
    try {
        const { data } = await axios.get('/api/unidades/tree');
        tree.value = data;
    } catch (e) {
        error.value = e.response?.data?.message ?? 'No se pudo cargar el organigrama.';
    } finally {
        loading.value = false;
    }
}

async function actualizarUnidad(unidadId, cambios) {
    error.value = '';
    try {
        await axios.put(`/api/unidades/${unidadId}`, cambios);
        await cargar();
    } catch (e) {
        error.value = e.response?.data?.message
            ?? e.response?.data?.errors?.permite_derivacion?.[0]
            ?? 'No se pudo actualizar la unidad.';
    }
}
</script>
