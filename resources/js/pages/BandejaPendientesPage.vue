<template>
  <div class="space-y-4">
    <!-- Page Header & Action Bar -->
    <div class="bg-white p-4 rounded-xl border border-outline-variant shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h2 class="text-headline-md text-on-surface font-semibold">Bandeja de Pendientes</h2>
        <p class="text-body-sm text-on-surface-variant">Documentos en espera de revisión o derivación</p>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center bg-surface-container px-3 py-1.5 rounded-lg gap-2">
          <span class="text-label-md text-on-surface-variant font-semibold">Prioridad:</span>
          <select
            v-model="filterPriority"
            class="bg-transparent border-none p-0 text-body-sm font-semibold focus:ring-0 cursor-pointer text-on-surface focus:outline-none"
          >
            <option value="todas">Todas</option>
            <option value="alta">Alta</option>
            <option value="media">Media</option>
            <option value="baja">Baja</option>
          </select>
        </div>
        <div class="flex items-center bg-surface-container px-3 py-1.5 rounded-lg gap-2">
          <span class="text-label-md text-on-surface-variant font-semibold">Tipo:</span>
          <select
            v-model="filterType"
            class="bg-transparent border-none p-0 text-body-sm font-semibold focus:ring-0 cursor-pointer text-on-surface focus:outline-none"
          >
            <option value="todos">Cualquier documento</option>
            <option v-for="t in docsStore.tiposDocumentales" :key="t.id" :value="t.id">
              {{ t.nombre }}
            </option>
          </select>
        </div>
        <button
          type="button"
          class="bg-primary text-on-primary px-4 py-2 rounded-lg text-label-md flex items-center gap-2 shadow-sm hover:brightness-110 active:scale-95 transition-all cursor-pointer font-semibold"
          @click="aplicarFiltros"
        >
          <span class="material-symbols-outlined text-sm">filter_alt</span>
          Aplicar Filtros
        </button>
      </div>
    </div>

    <!-- KPI Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white p-3 rounded-lg border border-outline-variant flex items-center gap-3">
        <div class="w-10 h-10 bg-error-container rounded flex items-center justify-center text-on-error-container">
          <span class="material-symbols-outlined">priority_high</span>
        </div>
        <div>
          <p class="text-label-md text-on-surface-variant uppercase font-semibold">Urgentes</p>
          <p class="text-headline-md text-error font-semibold">{{ urgentesCount }}</p>
        </div>
      </div>
      <div class="bg-white p-3 rounded-lg border border-outline-variant flex items-center gap-3">
        <div class="w-10 h-10 bg-secondary-container rounded flex items-center justify-center text-on-secondary-container">
          <span class="material-symbols-outlined">schedule</span>
        </div>
        <div>
          <p class="text-label-md text-on-surface-variant uppercase font-semibold">Promedio Días</p>
          <p class="text-headline-md text-on-surface font-semibold">{{ promedioDias }}</p>
        </div>
      </div>
      <div class="bg-white p-3 rounded-lg border border-outline-variant flex items-center gap-3">
        <div class="w-10 h-10 bg-surface-container-high rounded flex items-center justify-center text-primary">
          <span class="material-symbols-outlined">description</span>
        </div>
        <div>
          <p class="text-label-md text-on-surface-variant uppercase font-semibold">Total Pendientes</p>
          <p class="text-headline-md text-on-surface font-semibold">{{ totalPendientes }}</p>
        </div>
      </div>
      <div class="bg-white p-3 rounded-lg border border-outline-variant flex items-center gap-3">
        <div class="w-10 h-10 bg-primary-fixed rounded flex items-center justify-center text-on-primary-fixed">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div>
          <p class="text-label-md text-on-surface-variant uppercase font-semibold">Atendidos Hoy</p>
          <p class="text-headline-md text-primary font-semibold">{{ atendidosHoy }}</p>
        </div>
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white border border-outline-variant rounded-xl overflow-hidden shadow-sm flex flex-col">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-table-data">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant">
              <th class="px-4 py-3 font-semibold text-on-surface-variant uppercase tracking-wider">Código Expediente</th>
              <th class="px-4 py-3 font-semibold text-on-surface-variant uppercase tracking-wider">Tipo Documental</th>
              <th class="px-4 py-3 font-semibold text-on-surface-variant uppercase tracking-wider">Asunto</th>
              <th class="px-4 py-3 font-semibold text-on-surface-variant uppercase tracking-wider">Prioridad</th>
              <th class="px-4 py-3 font-semibold text-on-surface-variant uppercase tracking-wider">Antigüedad</th>
              <th class="px-4 py-3 font-semibold text-on-surface-variant uppercase tracking-wider">Unidad Origen</th>
              <th class="px-4 py-3 font-semibold text-on-surface-variant uppercase tracking-wider text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30">
            <tr v-if="paginados.length === 0">
              <td colspan="7" class="px-4 py-8 text-center text-on-surface-variant">
                No hay expedientes pendientes que coincidan con los filtros aplicados.
              </td>
            </tr>
            <tr
              v-for="exp in paginados"
              :key="exp.id"
              class="hover:bg-primary/5 transition-colors group cursor-default"
              @click="onRowClick($event, exp.codigo)"
            >
              <td class="px-4 py-3 font-bold text-primary">
                {{ exp.codigo }}
                <span v-if="exp.estado === 'por_recepcionar'" class="ml-1 text-xs text-secondary font-semibold">· recepcionar</span>
              </td>
              <td class="px-4 py-3 text-on-surface">{{ exp.tipo }}</td>
              <td class="px-4 py-3 truncate max-w-xs text-on-surface" :title="exp.asunto">{{ exp.asunto }}</td>
              <td class="px-4 py-3">
                <span :class="prioridadClass(exp.prioridad)">
                  {{ prioridadLabel(exp.prioridad) }}
                </span>
              </td>
              <td
                class="px-4 py-3"
                :class="exp.antiguedad_dias > 10 ? 'text-error font-semibold' : 'text-on-surface'"
              >
                {{ exp.antiguedad_dias }} {{ exp.antiguedad_dias === 1 ? 'día' : 'días' }}
              </td>
              <td class="px-4 py-3 text-on-surface-variant">{{ exp.unidad_origen }}</td>
              <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                <button
                  type="button"
                  class="text-primary hover:bg-primary-fixed p-1.5 rounded transition-all cursor-pointer inline-flex items-center justify-center"
                  title="Ver Detalle"
                  @click="verTrazabilidad(exp.codigo)"
                >
                  <span class="material-symbols-outlined text-[20px]">visibility</span>
                </button>
                <button
                  v-if="exp.estado === 'por_recepcionar'"
                  type="button"
                  class="text-primary bg-primary/10 hover:bg-primary/20 px-2 py-1 rounded text-label-md font-semibold"
                  @click="recepcionar(exp)"
                >
                  Recepcionar
                </button>
                <button
                  type="button"
                  class="text-secondary hover:bg-secondary-fixed p-1.5 rounded transition-all cursor-pointer inline-flex items-center justify-center"
                  title="Devolver"
                  @click="devolver(exp)"
                >
                  <span class="material-symbols-outlined text-[20px]">undo</span>
                </button>
                <button
                  v-if="exp.estado !== 'por_recepcionar' && auth.can('doc.expediente.archivar')"
                  type="button"
                  class="text-on-surface-variant hover:bg-surface-container-high p-1.5 rounded transition-all cursor-pointer inline-flex items-center justify-center"
                  title="Archivar"
                  @click="archivar(exp)"
                >
                  <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                </button>
                <button
                  v-if="exp.estado !== 'por_recepcionar'"
                  type="button"
                  class="text-on-primary bg-primary hover:brightness-110 px-3 py-1.5 rounded text-label-md font-semibold transition-all cursor-pointer"
                  @click="abrirDerivar(exp)"
                >
                  Derivar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 border-t border-outline-variant bg-surface-container-lowest flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="text-body-sm text-on-surface-variant">
          Mostrando
          <span class="font-bold">{{ rangoInicio }}-{{ rangoFin }}</span>
          de
          <span class="font-bold">{{ filtrados.length }}</span>
          expedientes
        </div>
        <div class="flex items-center gap-2">
          <button
            type="button"
            class="p-2 rounded hover:bg-surface-container-high transition-colors disabled:opacity-50 cursor-pointer disabled:cursor-not-allowed"
            :disabled="paginaActual === 1"
            @click="paginaActual--"
          >
            <span class="material-symbols-outlined">chevron_left</span>
          </button>
          <button
            v-for="p in paginasVisibles"
            :key="p"
            type="button"
            :class="[
              p === paginaActual
                ? 'bg-primary text-on-primary'
                : 'hover:bg-surface-container-high text-on-surface-variant',
              'w-8 h-8 rounded text-label-md font-semibold cursor-pointer'
            ]"
            @click="paginaActual = p"
          >
            {{ p }}
          </button>
          <span v-if="totalPaginas > 5" class="text-on-surface-variant">...</span>
          <button
            v-if="totalPaginas > 5 && paginaActual < totalPaginas - 2"
            type="button"
            class="w-8 h-8 rounded hover:bg-surface-container-high text-on-surface-variant text-label-md font-semibold cursor-pointer"
            @click="paginaActual = totalPaginas"
          >
            {{ totalPaginas }}
          </button>
          <button
            type="button"
            class="p-2 rounded hover:bg-surface-container-high transition-colors disabled:opacity-50 cursor-pointer disabled:cursor-not-allowed"
            :disabled="paginaActual >= totalPaginas"
            @click="paginaActual++"
          >
            <span class="material-symbols-outlined">chevron_right</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Contextual Help -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="bg-surface-container-low p-4 rounded-xl border border-primary/20 flex gap-4">
        <span class="material-symbols-outlined text-primary text-3xl shrink-0">info</span>
        <div>
          <h4 class="text-label-md text-primary uppercase font-semibold">Nota Operativa</h4>
          <p class="text-body-sm text-on-surface-variant">
            Los expedientes con más de 10 días de antigüedad aparecerán resaltados para su atención inmediata según la directiva N° 004-2026-MPA.
          </p>
        </div>
      </div>
      <div class="bg-secondary-container/20 p-4 rounded-xl border border-secondary/20 flex gap-4">
        <span class="material-symbols-outlined text-secondary text-3xl shrink-0">bolt</span>
        <div>
          <h4 class="text-label-md text-secondary uppercase font-semibold">Sugerencia de Flujo</h4>
          <p class="text-body-sm text-on-surface-variant">
            Utilice el botón "Derivar" para enviar masivamente múltiples documentos que compartan el mismo destino y asunto.
          </p>
        </div>
      </div>
    </div>

    <!-- Derivation Modal -->
    <div
      v-if="derivarModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/50 backdrop-blur-sm p-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby="derivar-titulo"
    >
      <div class="bg-white border border-outline-variant rounded-xl p-6 w-full max-w-md shadow-2xl">
        <h3 id="derivar-titulo" class="text-headline-md text-on-surface font-semibold mb-4">
          Derivar Expediente {{ selectedExpediente?.codigo }}
        </h3>
        <p class="text-body-sm text-on-surface-variant mb-6">
          Seleccione la oficina destino para este trámite documentario:
        </p>
        <div class="space-y-4">
          <div class="space-y-1">
            <label class="block text-label-md text-on-surface font-semibold" for="destino">Oficina Destino</label>
            <select
              id="destino"
              v-model="destinoUnidadId"
              class="w-full bg-surface border border-outline-variant rounded-lg px-4 py-2 text-body-sm focus:outline-none focus:border-primary text-on-surface"
            >
              <option v-for="u in docsStore.unidadesDerivacion" :key="u.id" :value="u.id">
                {{ u.nombre }}
              </option>
            </select>
          </div>
        </div>
        <div class="flex justify-end gap-3 mt-8">
          <button
            type="button"
            class="px-4 py-2 border border-outline-variant text-on-surface rounded text-label-md font-semibold hover:bg-surface-container-low transition-all cursor-pointer"
            @click="derivarModalOpen = false"
          >
            Cancelar
          </button>
          <button
            type="button"
            class="px-4 py-2 bg-primary text-on-primary rounded text-label-md font-semibold hover:bg-primary-container transition-all cursor-pointer"
            @click="confirmarDerivar"
          >
            Confirmar Derivación
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useDocumentosStore } from '../stores/documentos';
import { useAuthStore } from '../stores/auth';

const docsStore = useDocumentosStore();
const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const filterPriority = ref('todas');
const filterType = ref('todos');
const appliedPriority = ref('todas');
const appliedType = ref('todos');

const derivarModalOpen = ref(false);
const selectedExpediente = ref(null);
const destinoUnidadId = ref(null);

const paginaActual = ref(1);
const porPagina = 5;
const atendidosHoy = 24;

onMounted(async () => {
  await docsStore.cargarTiposDocumentales();
  await docsStore.cargarUnidadesDerivacion();
  if (docsStore.unidadesDerivacion.length) {
    destinoUnidadId.value = docsStore.unidadesDerivacion[0].id;
  }
  const q = route.query.q;
  if (q) {
    filterType.value = 'todos';
    filterPriority.value = 'todas';
    await docsStore.cargarBandeja({ q });
  } else {
    await docsStore.cargarBandeja();
  }
});

const filtrados = computed(() => docsStore.expedientes);

const totalPendientes = computed(() => docsStore.resumen.total);
const urgentesCount = computed(() => docsStore.resumen.urgentes);

const promedioDias = computed(() => docsStore.resumen.promedio_dias ?? '0');

const totalPaginas = computed(() => Math.max(1, Math.ceil(filtrados.value.length / porPagina)));

const paginados = computed(() => {
  const start = (paginaActual.value - 1) * porPagina;
  return filtrados.value.slice(start, start + porPagina);
});

const rangoInicio = computed(() => (filtrados.value.length === 0 ? 0 : (paginaActual.value - 1) * porPagina + 1));
const rangoFin = computed(() => Math.min(paginaActual.value * porPagina, filtrados.value.length));

const paginasVisibles = computed(() => {
  const total = totalPaginas.value;
  if (total <= 5) {
    return Array.from({ length: total }, (_, i) => i + 1);
  }
  const pages = [1];
  if (paginaActual.value > 2) pages.push(paginaActual.value - 1);
  if (paginaActual.value !== 1 && paginaActual.value !== total) pages.push(paginaActual.value);
  if (paginaActual.value < total - 1) pages.push(paginaActual.value + 1);
  return [...new Set(pages)].sort((a, b) => a - b);
});

watch(filtrados, () => {
  if (paginaActual.value > totalPaginas.value) {
    paginaActual.value = totalPaginas.value;
  }
});

function prioridadClass(prioridad) {
  const base = 'px-2 py-0.5 rounded-full text-[11px] font-bold uppercase';
  if (prioridad === 'alta') return `${base} bg-error-container text-on-error-container`;
  if (prioridad === 'media') return `${base} bg-secondary-container text-on-secondary-container`;
  return `${base} bg-tertiary-container text-on-tertiary-container`;
}

function prioridadLabel(prioridad) {
  const labels = { alta: 'Alta', media: 'Media', baja: 'Baja' };
  return labels[prioridad] ?? prioridad;
}

async function aplicarFiltros() {
  appliedPriority.value = filterPriority.value;
  appliedType.value = filterType.value;
  paginaActual.value = 1;
  await docsStore.cargarBandeja({
    prioridad: appliedPriority.value === 'todas' ? undefined : appliedPriority.value,
    tipo_documental_id: appliedType.value === 'todos' ? undefined : appliedType.value,
  });
}

function onRowClick(event, id) {
  const tag = event.target.tagName;
  if (tag === 'BUTTON' || event.target.closest('button')) return;
  verTrazabilidad(id);
}

function verTrazabilidad(codigo) {
  router.push({ name: 'trazabilidad-expediente', params: { id: codigo } });
}

async function devolver(exp) {
  const observacion = prompt(`Observación obligatoria para devolver ${exp.codigo}:`);
  if (!observacion || observacion.trim().length < 3) return;
  await docsStore.devolverExpediente(exp.id, observacion.trim());
}

async function recepcionar(exp) {
  await docsStore.recepcionarExpediente(exp.id);
}

function abrirDerivar(exp) {
  selectedExpediente.value = exp;
  if (docsStore.unidadesDerivacion.length) {
    destinoUnidadId.value = docsStore.unidadesDerivacion[0].id;
  }
  derivarModalOpen.value = true;
}

async function confirmarDerivar() {
  if (!selectedExpediente.value || !destinoUnidadId.value) return;
  await docsStore.derivarExpediente(selectedExpediente.value.id, destinoUnidadId.value);
  derivarModalOpen.value = false;
  router.push({ name: 'trazabilidad-expediente', params: { id: selectedExpediente.value.codigo } });
}

async function archivar(exp) {
  if (!confirm(`¿Archivar ${exp.codigo}?`)) return;
  await docsStore.archivarExpediente(exp.id);
}
</script>
