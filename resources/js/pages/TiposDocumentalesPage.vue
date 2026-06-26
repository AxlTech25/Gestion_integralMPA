<template>
  <div class="space-y-6">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h1 class="text-headline-lg font-bold text-primary">Catálogo de tipos documentales</h1>
        <p class="text-body-sm text-on-surface-variant">Administración institucional (HU-DOC-07, PA-29).</p>
      </div>
      <button
        type="button"
        class="px-4 py-2 bg-primary text-on-primary rounded-lg text-label-md font-semibold"
        @click="abrirCrear"
      >
        Nuevo tipo
      </button>
    </header>

    <div v-if="error" class="bg-error-container/30 border border-error rounded-lg p-3 text-body-sm">{{ error }}</div>

    <div v-if="loading" class="text-on-surface-variant">Cargando catálogo...</div>

    <div v-else class="bg-surface border border-outline-variant rounded-xl overflow-hidden">
      <table class="w-full text-left text-table-data">
        <thead class="bg-surface-container-low border-b border-outline-variant">
          <tr>
            <th class="px-4 py-3 font-semibold text-on-surface-variant">Código</th>
            <th class="px-4 py-3 font-semibold text-on-surface-variant">Nombre</th>
            <th class="px-4 py-3 font-semibold text-on-surface-variant">Prefijo</th>
            <th class="px-4 py-3 font-semibold text-on-surface-variant">Emisor</th>
            <th class="px-4 py-3 font-semibold text-on-surface-variant">Estado</th>
            <th class="px-4 py-3 font-semibold text-on-surface-variant text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/40">
          <tr v-for="tipo in tipos" :key="tipo.id" class="hover:bg-primary/5">
            <td class="px-4 py-3 font-mono text-primary font-semibold">{{ tipo.codigo }}</td>
            <td class="px-4 py-3">{{ tipo.nombre }}</td>
            <td class="px-4 py-3">{{ tipo.prefijo_numeracion }}</td>
            <td class="px-4 py-3 text-body-sm">{{ tipo.unidad_emisora?.nombre ?? '—' }}</td>
            <td class="px-4 py-3">
              <span :class="tipo.activo ? 'text-primary' : 'text-error'">{{ tipo.activo ? 'Activo' : 'Inactivo' }}</span>
            </td>
            <td class="px-4 py-3 text-right">
              <button type="button" class="text-label-md text-primary font-semibold" @click="editar(tipo)">Editar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="modalAbierto" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @click.self="cerrarModal">
      <div class="bg-surface rounded-xl border border-outline-variant w-full max-w-lg p-6 space-y-4 shadow-xl">
        <h2 class="text-headline-md font-semibold">{{ editando ? 'Editar tipo' : 'Nuevo tipo documental' }}</h2>
        <form class="space-y-3" @submit.prevent="guardar">
          <div v-if="!editando" class="grid grid-cols-2 gap-3">
            <label class="block">
              <span class="text-label-md text-on-surface-variant">Código</span>
              <input v-model="form.codigo" required maxlength="20" class="w-full border rounded-lg px-3 py-2 mt-1" />
            </label>
            <label class="block">
              <span class="text-label-md text-on-surface-variant">Prefijo</span>
              <input v-model="form.prefijo_numeracion" required maxlength="20" class="w-full border rounded-lg px-3 py-2 mt-1" />
            </label>
          </div>
          <label class="block">
            <span class="text-label-md text-on-surface-variant">Nombre</span>
            <input v-model="form.nombre" required class="w-full border rounded-lg px-3 py-2 mt-1" />
          </label>
          <div class="flex flex-wrap gap-4 text-body-sm">
            <label class="flex items-center gap-2">
              <input v-model="form.activo" type="checkbox" />
              Activo
            </label>
            <label class="flex items-center gap-2">
              <input v-model="form.requiere_firma_antes_derivar" type="checkbox" />
              Firma antes de derivar
            </label>
            <label class="flex items-center gap-2">
              <input v-model="form.requiere_recepcion" type="checkbox" />
              Requiere recepción
            </label>
            <label class="flex items-center gap-2">
              <input v-model="form.registro_por_secretaria" type="checkbox" />
              Registro por Secretaría
            </label>
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="px-4 py-2 border rounded-lg" @click="cerrarModal">Cancelar</button>
            <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-semibold" :disabled="guardando">
              {{ guardando ? 'Guardando…' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

const tipos = ref([]);
const loading = ref(true);
const error = ref('');
const modalAbierto = ref(false);
const editando = ref(null);
const guardando = ref(false);

const form = ref({
    codigo: '',
    nombre: '',
    prefijo_numeracion: '',
    clase_norma: 'gestion_interna',
    ambito_emision: 'unidad',
    activo: true,
    requiere_firma_antes_derivar: false,
    requiere_recepcion: true,
    registro_por_secretaria: false,
});

onMounted(cargar);

async function cargar() {
    loading.value = true;
    error.value = '';
    try {
        const { data } = await axios.get('/api/tipos-documentales', { params: { gestion: 1 } });
        tipos.value = data;
    } catch (e) {
        error.value = e.response?.data?.message ?? 'No se pudo cargar el catálogo.';
    } finally {
        loading.value = false;
    }
}

function abrirCrear() {
    editando.value = null;
    form.value = {
        codigo: '',
        nombre: '',
        prefijo_numeracion: '',
        clase_norma: 'gestion_interna',
        ambito_emision: 'unidad',
        activo: true,
        requiere_firma_antes_derivar: false,
        requiere_recepcion: true,
        registro_por_secretaria: false,
    };
    modalAbierto.value = true;
}

function editar(tipo) {
    editando.value = tipo;
    form.value = {
        codigo: tipo.codigo,
        nombre: tipo.nombre,
        prefijo_numeracion: tipo.prefijo_numeracion,
        clase_norma: tipo.clase_norma,
        ambito_emision: tipo.ambito_emision,
        activo: tipo.activo,
        requiere_firma_antes_derivar: tipo.requiere_firma_antes_derivar,
        requiere_recepcion: tipo.requiere_recepcion,
        registro_por_secretaria: tipo.registro_por_secretaria,
    };
    modalAbierto.value = true;
}

function cerrarModal() {
    modalAbierto.value = false;
}

async function guardar() {
    guardando.value = true;
    error.value = '';
    try {
        if (editando.value) {
            await axios.put(`/api/tipos-documentales/${editando.value.id}`, {
                nombre: form.value.nombre,
                prefijo_numeracion: form.value.prefijo_numeracion,
                activo: form.value.activo,
                requiere_firma_antes_derivar: form.value.requiere_firma_antes_derivar,
                requiere_recepcion: form.value.requiere_recepcion,
                registro_por_secretaria: form.value.registro_por_secretaria,
            });
        } else {
            await axios.post('/api/tipos-documentales', form.value);
        }
        cerrarModal();
        await cargar();
    } catch (e) {
        error.value = e.response?.data?.message
            ?? e.response?.data?.errors?.codigo?.[0]
            ?? 'No se pudo guardar el tipo documental.';
    } finally {
        guardando.value = false;
    }
}
</script>
