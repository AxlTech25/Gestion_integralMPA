<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-headline-lg font-bold text-on-surface">Inventario IT</h1>
        <p class="text-body-sm text-on-surface-variant">Equipos informáticos municipales (Patrimonio / UTIS).</p>
      </div>
      <button
        v-if="auth.can('pat.equipo.registrar')"
        type="button"
        class="bg-primary text-on-primary px-4 py-2 rounded-lg text-label-md font-semibold"
        @click="modalOpen = true"
      >
        Registrar equipo
      </button>
    </div>

    <div class="flex flex-wrap gap-3">
      <input
        v-model="busqueda"
        type="search"
        placeholder="Código, marca, custodio..."
        class="border border-outline-variant rounded-lg px-3 py-2 text-body-sm flex-1 max-w-md bg-surface"
        @keyup.enter="buscar"
      />
      <select v-model="filtroRiesgo" class="border border-outline-variant rounded-lg px-3 py-2 text-body-sm bg-surface" @change="buscar">
        <option value="">Todos los riesgos</option>
        <option value="rojo">Rojo</option>
        <option value="amarillo">Amarillo</option>
        <option value="verde">Verde</option>
      </select>
      <button type="button" class="px-4 py-2 border border-outline-variant rounded-lg text-label-md" @click="buscar">Buscar</button>
    </div>

    <div
      v-if="store.error"
      class="bg-error-container/30 border border-error text-on-error-container rounded-xl p-4 text-body-sm"
    >
      {{ store.error }}
      <button type="button" class="ml-3 underline font-semibold" @click="recargar">Reintentar</button>
    </div>

    <div v-if="store.loading" class="text-on-surface-variant">Cargando inventario...</div>
    <div v-else class="bg-surface border border-outline-variant rounded-xl overflow-hidden">
      <table class="w-full text-left text-body-sm">
        <thead class="bg-surface-container-low border-b border-outline-variant">
          <tr>
            <th class="px-4 py-3 font-semibold">Código</th>
            <th class="px-4 py-3 font-semibold">Equipo</th>
            <th class="px-4 py-3 font-semibold">Unidad</th>
            <th class="px-4 py-3 font-semibold">Custodio</th>
            <th class="px-4 py-3 font-semibold">Estado</th>
            <th class="px-4 py-3 font-semibold">Riesgo</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="eq in store.equipos"
            :key="eq.id"
            class="border-b border-outline-variant/50 hover:bg-surface-container-low/50 cursor-pointer"
            @click="irDetalle(eq.id)"
          >
            <td class="px-4 py-3 font-mono text-primary">{{ eq.codigo_patrimonial }}</td>
            <td class="px-4 py-3">{{ eq.marca }} {{ eq.modelo }}</td>
            <td class="px-4 py-3">{{ eq.unidad }}</td>
            <td class="px-4 py-3">{{ eq.custodio_nombre ?? '—' }}</td>
            <td class="px-4 py-3">{{ eq.estado_operativo }}</td>
            <td class="px-4 py-3">
              <span v-if="eq.riesgo" :class="riesgoClass(eq.riesgo.nivel)" class="px-2 py-0.5 rounded-full text-xs font-bold uppercase">
                {{ eq.riesgo.nivel }}
              </span>
              <span v-else class="text-on-surface-variant">—</span>
            </td>
          </tr>
          <tr v-if="!store.equipos.length">
            <td colspan="6" class="px-4 py-8 text-center text-on-surface-variant">Sin equipos registrados.</td>
          </tr>
        </tbody>
      </table>
    </div>

  <!-- Modal registro -->
    <div v-if="modalOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="modalOpen = false">
      <div class="bg-surface rounded-xl border border-outline-variant p-6 w-full max-w-lg space-y-4">
        <h2 class="text-headline-md font-semibold">Nuevo equipo municipal</h2>
        <form class="space-y-3" @submit.prevent="submitEquipo">
          <input v-model="form.codigo_patrimonial" required placeholder="Código patrimonial" class="w-full border rounded-lg px-3 py-2" />
          <div class="grid grid-cols-2 gap-3">
            <select v-model="form.tipo_equipo" class="border rounded-lg px-3 py-2">
              <option value="pc">PC</option>
              <option value="servidor">Servidor</option>
              <option value="impresora">Impresora</option>
              <option value="red">Red</option>
              <option value="otro">Otro</option>
            </select>
            <input v-model.number="form.unidad_id" type="number" required placeholder="ID unidad" class="border rounded-lg px-3 py-2" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <input v-model="form.marca" required placeholder="Marca" class="border rounded-lg px-3 py-2" />
            <input v-model="form.modelo" required placeholder="Modelo" class="border rounded-lg px-3 py-2" />
          </div>
          <input v-model="form.custodio_nombre" placeholder="Custodio" class="w-full border rounded-lg px-3 py-2" />
          <input v-model.number="form.valor_patrimonial" type="number" step="0.01" placeholder="Valor patrimonial" class="w-full border rounded-lg px-3 py-2" />
          <p v-if="formError" class="text-error text-body-sm">{{ formError }}</p>
          <div class="flex justify-end gap-2">
            <button type="button" class="px-4 py-2 border rounded-lg" @click="modalOpen = false">Cancelar</button>
            <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-semibold">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { usePatrimonioStore } from '../../stores/patrimonio';
import { useAuthStore } from '../../stores/auth';

const store = usePatrimonioStore();
const auth = useAuthStore();
const router = useRouter();

const busqueda = ref('');
const filtroRiesgo = ref('');
const modalOpen = ref(false);
const formError = ref('');
const form = ref({
    codigo_patrimonial: '',
    tipo_equipo: 'pc',
    marca: '',
    modelo: '',
    unidad_id: auth.user?.unidad_activa_id ?? '',
    custodio_nombre: '',
    valor_patrimonial: null,
});

onMounted(() => recargar());

function recargar() {
    store.cargarEquipos().catch(() => {});
}

function buscar() {
    store.cargarEquipos({
        q: busqueda.value || undefined,
        nivel_riesgo: filtroRiesgo.value || undefined,
    });
}

function irDetalle(id) {
    router.push({ name: 'patrimonio-equipo', params: { id } });
}

function riesgoClass(nivel) {
    if (nivel === 'rojo') return 'bg-error-container text-on-error-container';
    if (nivel === 'amarillo') return 'bg-secondary-container text-on-secondary-container';
    return 'bg-primary-container text-on-primary-container';
}

async function submitEquipo() {
    formError.value = '';
    try {
        await store.registrarEquipo(form.value);
        modalOpen.value = false;
    } catch (e) {
        formError.value = e.response?.data?.message ?? 'No se pudo registrar el equipo.';
    }
}
</script>
