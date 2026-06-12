<template>
  <div class="space-y-6">
    <nav class="text-body-sm text-on-surface-variant">
      <router-link :to="{ name: 'patrimonio-inventario' }" class="text-primary font-semibold hover:underline">Inventario</router-link>
      <span class="mx-2">/</span>
      <span>{{ equipo?.codigo_patrimonial ?? 'Equipo' }}</span>
    </nav>

    <div v-if="store.loading" class="text-on-surface-variant">Cargando...</div>
    <div v-else-if="equipo" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <section class="lg:col-span-2 space-y-4">
        <div class="bg-surface border border-outline-variant rounded-xl p-6">
          <h1 class="text-headline-md font-bold">{{ equipo.codigo_patrimonial }}</h1>
          <p class="text-on-surface-variant">{{ equipo.marca }} {{ equipo.modelo }} · {{ equipo.tipo_equipo }}</p>
          <div class="mt-4 grid grid-cols-2 gap-4 text-body-sm">
            <div><span class="text-on-surface-variant">Unidad:</span> {{ equipo.unidad }}</div>
            <div><span class="text-on-surface-variant">Estado:</span> {{ equipo.estado_operativo }}</div>
            <div><span class="text-on-surface-variant">Custodio:</span> {{ equipo.custodio_nombre ?? '—' }}</div>
            <div v-if="equipo.valor_patrimonial"><span class="text-on-surface-variant">Valor:</span> S/ {{ equipo.valor_patrimonial }}</div>
          </div>
        </div>

        <div v-if="auth.can('pat.ficha.gestionar')" class="bg-surface border border-outline-variant rounded-xl p-6 space-y-4">
          <h2 class="font-semibold text-headline-sm">Ficha técnica (UTIS)</h2>
          <form class="grid grid-cols-2 gap-3" @submit.prevent="guardarFicha">
            <input v-model="ficha.cpu" placeholder="CPU" class="border rounded-lg px-3 py-2" />
            <input v-model.number="ficha.ram_gb" type="number" placeholder="RAM GB" class="border rounded-lg px-3 py-2" />
            <input v-model.number="ficha.almacenamiento_gb" type="number" placeholder="Almacenamiento GB" class="border rounded-lg px-3 py-2" />
            <input v-model="ficha.sistema_operativo" placeholder="Sistema operativo" class="border rounded-lg px-3 py-2" />
            <input v-model.number="ficha.antiguedad_anios" type="number" step="0.1" placeholder="Antigüedad años" class="border rounded-lg px-3 py-2" />
            <button type="submit" class="col-span-2 bg-secondary text-on-secondary py-2 rounded-lg font-semibold">Guardar ficha técnica</button>
          </form>
        </div>

        <div v-if="auth.can('pat.ficha.gestionar')" class="bg-surface border border-outline-variant rounded-xl p-6 space-y-3">
          <h2 class="font-semibold text-headline-sm">Registrar mantenimiento</h2>
          <form class="space-y-3" @submit.prevent="guardarMantenimiento">
            <select v-model="mantenimiento.tipo" class="w-full border rounded-lg px-3 py-2">
              <option value="preventivo">Preventivo</option>
              <option value="correctivo">Correctivo</option>
            </select>
            <input v-model="mantenimiento.fecha" type="date" required class="w-full border rounded-lg px-3 py-2" />
            <textarea v-model="mantenimiento.descripcion" required rows="2" placeholder="Descripción" class="w-full border rounded-lg px-3 py-2" />
            <button type="submit" class="bg-primary text-on-primary py-2 rounded-lg font-semibold w-full">Registrar</button>
          </form>
        </div>
      </section>

      <aside class="space-y-4">
        <div v-if="equipo.riesgo" class="bg-surface border border-outline-variant rounded-xl p-4">
          <p class="text-label-md text-on-surface-variant">Riesgo ML</p>
          <p class="text-headline-md font-bold capitalize">{{ equipo.riesgo.nivel }}</p>
          <p class="text-body-sm">{{ (equipo.riesgo.probabilidad * 100).toFixed(1) }}% probabilidad de falla</p>
        </div>
        <div class="bg-surface border border-outline-variant rounded-xl p-4">
          <h3 class="font-semibold mb-2">Mantenimientos</h3>
          <ul class="text-body-sm space-y-2">
            <li v-for="m in equipo.mantenimientos" :key="m.id">{{ m.fecha }} — {{ m.tipo }}</li>
            <li v-if="!equipo.mantenimientos?.length" class="text-on-surface-variant">Sin registros</li>
          </ul>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { usePatrimonioStore } from '../../stores/patrimonio';
import { useAuthStore } from '../../stores/auth';

const store = usePatrimonioStore();
const auth = useAuthStore();
const route = useRoute();

const equipo = computed(() => store.equipoActual);
const ficha = ref({ cpu: '', ram_gb: null, almacenamiento_gb: null, sistema_operativo: '', antiguedad_anios: null });
const mantenimiento = ref({ tipo: 'preventivo', fecha: new Date().toISOString().slice(0, 10), descripcion: '' });

async function load() {
    await store.cargarEquipo(route.params.id);
    if (store.equipoActual?.ficha_tecnica) {
        ficha.value = { ...store.equipoActual.ficha_tecnica };
    }
}

onMounted(load);
watch(() => route.params.id, load);

async function guardarFicha() {
    await store.guardarFichaTecnica(route.params.id, ficha.value);
}

async function guardarMantenimiento() {
    await store.registrarMantenimiento(route.params.id, mantenimiento.value);
    mantenimiento.value.descripcion = '';
}
</script>
