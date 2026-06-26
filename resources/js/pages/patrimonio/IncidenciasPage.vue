<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:justify-between gap-4">
      <div>
        <h1 class="text-headline-lg font-bold">
          {{ esUtis ? 'Incidencias de soporte' : 'Mis solicitudes de soporte' }}
        </h1>
        <p class="text-body-sm text-on-surface-variant">
          {{ esUtis ? 'Panel UTIS — fallas, averías y requerimientos.' : 'Historial de reportes a UTIS.' }}
        </p>
      </div>
      <button
        type="button"
        class="bg-primary text-on-primary px-4 py-2 rounded-lg font-semibold"
        @click="abrirModal"
      >
        Nueva incidencia
      </button>
    </div>

    <div v-if="esUtis" class="flex gap-2">
      <button
        type="button"
        :class="filtro === 'abiertas' ? 'bg-primary text-on-primary' : 'bg-surface-container'"
        class="px-3 py-1.5 rounded-lg text-label-md font-semibold"
        @click="cambiarFiltro('abiertas')"
      >
        Abiertas
      </button>
      <button
        type="button"
        :class="filtro === 'todas' ? 'bg-primary text-on-primary' : 'bg-surface-container'"
        class="px-3 py-1.5 rounded-lg text-label-md font-semibold"
        @click="cambiarFiltro('todas')"
      >
        Todas
      </button>
    </div>

    <div v-if="store.loading" class="text-on-surface-variant">Cargando...</div>
    <div v-else class="space-y-3">
      <article
        v-for="inc in store.incidencias"
        :key="inc.id"
        class="bg-surface border border-outline-variant rounded-xl p-4 flex flex-col sm:flex-row sm:items-start justify-between gap-4"
      >
        <div class="min-w-0">
          <div class="flex flex-wrap items-center gap-2">
            <p class="font-mono text-primary font-semibold">{{ inc.codigo_patrimonial }}</p>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold uppercase bg-surface-container">{{ inc.estado }}</span>
            <span class="text-label-md text-on-surface-variant">{{ inc.tipo }}</span>
          </div>
          <p class="text-body-sm text-on-surface mt-1">{{ inc.descripcion }}</p>
          <p class="text-body-sm text-on-surface-variant mt-1">
            {{ inc.unidad }} · reportado por {{ inc.reportado_por }}
            <span v-if="inc.asignado"> · atendido por {{ inc.asignado }}</span>
          </p>
          <p v-if="inc.solucion" class="text-body-sm text-on-surface mt-2 bg-surface-container-low rounded-lg px-3 py-2">
            <span class="font-semibold">Solución:</span> {{ inc.solucion }}
          </p>
        </div>
        <div v-if="esUtis" class="flex items-center gap-2 shrink-0">
          <button
            v-if="inc.estado === 'abierta'"
            type="button"
            class="text-label-md text-secondary font-semibold"
            @click="cambiarEstado(inc.id, 'en_atencion')"
          >
            Atender
          </button>
          <button
            v-if="inc.estado !== 'cerrada'"
            type="button"
            class="text-label-md text-primary font-semibold"
            @click="cerrar(inc.id)"
          >
            Cerrar
          </button>
        </div>
      </article>
      <p v-if="!store.incidencias.length" class="text-center text-on-surface-variant py-8">
        No hay incidencias para mostrar.
      </p>
    </div>

    <div v-if="modalOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="modalOpen = false">
      <div class="bg-surface rounded-xl p-6 w-full max-w-md space-y-3">
        <h2 class="font-semibold text-headline-sm">Reportar incidencia</h2>
        <div>
          <input
            v-model="busquedaEquipo"
            type="search"
            placeholder="Buscar equipo por código o marca..."
            class="w-full border rounded-lg px-3 py-2"
            @input="buscarEquipos"
          />
          <ul v-if="equiposSugeridos.length" class="mt-1 border rounded-lg divide-y max-h-40 overflow-y-auto">
            <li
              v-for="eq in equiposSugeridos"
              :key="eq.id"
              class="px-3 py-2 text-body-sm cursor-pointer hover:bg-surface-container-low"
              @click="seleccionarEquipo(eq)"
            >
              {{ eq.label }}
            </li>
          </ul>
          <p v-if="equipoSeleccionado" class="text-body-sm text-primary mt-1 font-semibold">
            Seleccionado: {{ equipoSeleccionado.label }}
          </p>
        </div>
        <select v-model="nueva.tipo" class="w-full border rounded-lg px-3 py-2">
          <option value="falla">Falla</option>
          <option value="averia">Avería</option>
          <option value="requerimiento">Requerimiento</option>
        </select>
        <textarea v-model="nueva.descripcion" rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="Descripción (mín. 10 caracteres)" />
        <p v-if="errorForm" class="text-error text-body-sm">{{ errorForm }}</p>
        <button
          type="button"
          class="w-full bg-primary text-on-primary py-2 rounded-lg font-semibold disabled:opacity-50"
          :disabled="!equipoSeleccionado || nueva.descripcion.length < 10"
          @click="crearIncidencia"
        >
          Registrar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePatrimonioStore } from '../../stores/patrimonio';
import { useAuthStore } from '../../stores/auth';

const store = usePatrimonioStore();
const auth = useAuthStore();
const esUtis = computed(() => auth.can('pat.incidencia.gestionar'));
const modalOpen = ref(false);
const filtro = ref('abiertas');
const busquedaEquipo = ref('');
const equiposSugeridos = ref([]);
const equipoSeleccionado = ref(null);
const errorForm = ref('');
const nueva = ref({ tipo: 'falla', descripcion: '' });

onMounted(() => cargarLista());

async function cargarLista() {
    const params = esUtis.value && filtro.value === 'abiertas'
        ? { solo_abiertas: true }
        : {};
    await store.cargarIncidencias(params);
}

async function cambiarFiltro(nuevo) {
    filtro.value = nuevo;
    await cargarLista();
}

function abrirModal() {
    modalOpen.value = true;
    busquedaEquipo.value = '';
    equiposSugeridos.value = [];
    equipoSeleccionado.value = null;
    nueva.value = { tipo: 'falla', descripcion: '' };
    errorForm.value = '';
}

let busquedaTimer = null;
function buscarEquipos() {
    clearTimeout(busquedaTimer);
    busquedaTimer = setTimeout(async () => {
        const q = busquedaEquipo.value.trim();
        if (q.length < 2) {
            equiposSugeridos.value = [];
            return;
        }
        equiposSugeridos.value = await store.buscarEquiposSoporte(q);
    }, 300);
}

function seleccionarEquipo(eq) {
    equipoSeleccionado.value = eq;
    busquedaEquipo.value = eq.label;
    equiposSugeridos.value = [];
}

async function cambiarEstado(id, estado) {
    await store.actualizarIncidencia(id, { estado });
    await cargarLista();
}

async function cerrar(id) {
    const solucion = prompt('Solución aplicada:');
    if (!solucion?.trim()) return;
    const estadoOperativo = prompt('Estado operativo del equipo (operativo/reparacion/almacen):', 'operativo');
    await store.actualizarIncidencia(id, {
        estado: 'cerrada',
        solucion: solucion.trim(),
        estado_operativo_equipo: estadoOperativo?.trim() || 'operativo',
    });
    await cargarLista();
}

async function crearIncidencia() {
    errorForm.value = '';
    try {
        await store.reportarIncidencia({
            equipo_id: equipoSeleccionado.value.id,
            tipo: nueva.value.tipo,
            descripcion: nueva.value.descripcion,
        });
        modalOpen.value = false;
        await cargarLista();
    } catch (e) {
        errorForm.value = e.response?.data?.message
            ?? e.response?.data?.errors?.equipo_id?.[0]
            ?? 'No se pudo registrar la incidencia.';
    }
}
</script>
