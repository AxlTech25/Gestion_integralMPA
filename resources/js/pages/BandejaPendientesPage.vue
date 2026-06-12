<template>
  <div class="space-y-6">
    <!-- Sub-navigation Tabs (Bandeja / Archivados / Reportes) -->
    <div class="bg-surface border-b border-outline-variant flex items-center px-4 -mx-container-padding -mt-6 h-12 shrink-0">
      <nav class="flex gap-6">
        <a
          @click.prevent="activeTab = 'bandeja'"
          :class="[
            activeTab === 'bandeja' ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary',
            'h-12 flex items-center px-1 cursor-pointer transition-colors text-body-md'
          ]"
        >
          Bandeja
        </a>
        <a
          @click.prevent="activeTab = 'archivados'"
          :class="[
            activeTab === 'archivados' ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary',
            'h-12 flex items-center px-1 cursor-pointer transition-colors text-body-md'
          ]"
        >
          Archivados (0)
        </a>
        <a
          @click.prevent="activeTab = 'reportes'"
          :class="[
            activeTab === 'reportes' ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary',
            'h-12 flex items-center px-1 cursor-pointer transition-colors text-body-md'
          ]"
        >
          Reportes
        </a>
      </nav>
    </div>

    <!-- Page Header & Action Bar -->
    <div class="bg-white p-4 rounded-xl border border-outline-variant shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface font-bold">Bandeja de Pendientes</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Documentos en espera de revisión o derivación</p>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <!-- Priority Filter -->
        <div class="flex items-center bg-surface-container px-3 py-1.5 rounded-lg gap-2">
          <span class="text-label-md font-label-md text-on-surface-variant font-bold">Prioridad:</span>
          <select v-model="filterPriority" class="bg-transparent border-none p-0 text-body-sm font-semibold focus:ring-0 cursor-pointer text-on-surface focus:outline-none">
            <option value="todas">Todas</option>
            <option value="alta">Alta</option>
            <option value="media">Media</option>
            <option value="baja">Baja</option>
          </select>
        </div>
        <!-- Type Filter -->
        <div class="flex items-center bg-surface-container px-3 py-1.5 rounded-lg gap-2">
          <span class="text-label-md font-label-md text-on-surface-variant font-bold">Tipo:</span>
          <select v-model="filterType" class="bg-transparent border-none p-0 text-body-sm font-semibold focus:ring-0 cursor-pointer text-on-surface focus:outline-none">
            <option value="todos">Todos</option>
            <option value="Informe Técnico">Informe Técnico</option>
            <option value="Memorándum">Memorándum</option>
            <option value="Resolución">Resolución</option>
            <option value="Oficio Circular">Oficio Circular</option>
          </select>
        </div>
        <button
          @click="aplicarFiltros"
          class="bg-primary text-on-primary px-4 py-2 rounded-lg font-label-md text-label-md flex items-center gap-2 shadow-sm hover:brightness-110 active:scale-95 transition-all cursor-pointer font-bold"
        >
          <span class="material-symbols-outlined text-sm">filter_alt</span>
          Aplicar Filtros
        </button>
      </div>
    </div>

    <!-- KPI Summary (Compact) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white p-3 rounded-lg border border-outline-variant flex items-center gap-3 shadow-sm">
        <div class="w-10 h-10 bg-error-container rounded flex items-center justify-center text-on-error-container">
          <span class="material-symbols-outlined">priority_high</span>
        </div>
        <div>
          <p class="text-label-md font-label-md text-on-surface-variant uppercase font-bold text-[10px]">Urgentes</p>
          <p class="text-headline-md font-headline-md text-error font-bold">{{ urgentesCount }}</p>
        </div>
      </div>
      <div class="bg-white p-3 rounded-lg border border-outline-variant flex items-center gap-3 shadow-sm">
        <div class="w-10 h-10 bg-secondary-container rounded flex items-center justify-center text-on-secondary-container">
          <span class="material-symbols-outlined">schedule</span>
        </div>
        <div>
          <p class="text-label-md font-label-md text-on-surface-variant uppercase font-bold text-[10px]">Promedio Días</p>
          <p class="text-headline-md font-headline-md text-on-surface font-bold">3.5</p>
        </div>
      </div>
      <div class="bg-white p-3 rounded-lg border border-outline-variant flex items-center gap-3 shadow-sm">
        <div class="w-10 h-10 bg-surface-container-high rounded flex items-center justify-center text-primary">
          <span class="material-symbols-outlined">description</span>
        </div>
        <div>
          <p class="text-label-md font-label-md text-on-surface-variant uppercase font-bold text-[10px]">Total Pendientes</p>
          <p class="text-headline-md font-headline-md text-on-surface font-bold">{{ filtrados.length }}</p>
        </div>
      </div>
      <div class="bg-white p-3 rounded-lg border border-outline-variant flex items-center gap-3 shadow-sm">
        <div class="w-10 h-10 bg-primary-fixed rounded flex items-center justify-center text-on-primary-fixed">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div>
          <p class="text-label-md font-label-md text-on-surface-variant uppercase font-bold text-[10px]">Atendidos Hoy</p>
          <p class="text-headline-md font-headline-md text-primary font-bold">24</p>
        </div>
      </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white border border-outline-variant rounded-xl overflow-hidden shadow-sm flex flex-col">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse font-table-data text-table-data">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant">
              <th class="px-4 py-3 font-bold text-on-surface-variant uppercase tracking-wider">Código Expediente</th>
              <th class="px-4 py-3 font-bold text-on-surface-variant uppercase tracking-wider">Tipo Documental</th>
              <th class="px-4 py-3 font-bold text-on-surface-variant uppercase tracking-wider">Asunto</th>
              <th class="px-4 py-3 font-bold text-on-surface-variant uppercase tracking-wider">Prioridad</th>
              <th class="px-4 py-3 font-bold text-on-surface-variant uppercase tracking-wider">Estado</th>
              <th class="px-4 py-3 font-bold text-on-surface-variant uppercase tracking-wider">Unidad Origen</th>
              <th class="px-4 py-3 font-bold text-on-surface-variant uppercase tracking-wider text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30">
            <tr v-if="filtrados.length === 0">
              <td colspan="7" class="px-4 py-8 text-center text-on-surface-variant font-medium">
                No hay expedientes pendientes que coincidan con los filtros aplicados.
              </td>
            </tr>
            <tr
              v-for="exp in filtrados"
              :key="exp.id"
              class="hover:bg-primary/5 transition-colors group"
            >
              <td
                @click="verTrazabilidad(exp.id)"
                class="px-4 py-3 font-bold text-primary cursor-pointer hover:underline"
              >
                {{ exp.id }}
              </td>
              <td class="px-4 py-3 text-on-surface">{{ exp.tipo }}</td>
              <td class="px-4 py-3 truncate max-w-xs text-on-surface" :title="exp.asunto">{{ exp.asunto }}</td>
              <td class="px-4 py-3">
                <span
                  :class="[
                    exp.prioridad === 'alta' ? 'bg-error-container text-on-error-container' :
                    exp.prioridad === 'media' ? 'bg-secondary-container text-on-secondary-container' :
                    'bg-surface-variant text-on-surface-variant',
                    'px-2 py-0.5 rounded-full text-[11px] font-bold uppercase'
                  ]"
                >
                  {{ exp.prioridad }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span
                  :class="[
                    exp.estado === 'REVISIÓN' ? 'bg-secondary-container text-on-secondary-container' :
                    exp.estado === 'APROBADO' ? 'bg-primary-container text-on-primary-container' :
                    exp.estado === 'RECIBIDO' ? 'bg-surface-variant text-on-surface-variant' :
                    'bg-secondary/15 text-secondary',
                    'px-2 py-0.5 rounded text-[10px] font-bold'
                  ]"
                >
                  {{ exp.estado }}
                </span>
              </td>
              <td class="px-4 py-3 text-on-surface-variant">{{ exp.unidad_origen }}</td>
              <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                <button
                  @click="verTrazabilidad(exp.id)"
                  class="text-primary hover:bg-primary-fixed p-1.5 rounded transition-all cursor-pointer inline-flex items-center justify-center"
                  title="Ver Detalle"
                >
                  <span class="material-symbols-outlined text-[20px]">visibility</span>
                </button>
                <button
                  @click="devolver(exp.id)"
                  class="text-secondary hover:bg-secondary-fixed p-1.5 rounded transition-all cursor-pointer inline-flex items-center justify-center"
                  title="Devolver"
                >
                  <span class="material-symbols-outlined text-[20px]">undo</span>
                </button>
                <button
                  @click="abrirDerivar(exp)"
                  class="text-on-primary bg-primary hover:brightness-110 px-3 py-1.5 rounded font-label-md text-label-md font-bold transition-all cursor-pointer"
                >
                  Derivar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Simple Derivation Modal Dialog -->
    <div v-if="derivarModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
      <div class="bg-white border border-outline-variant rounded-xl p-6 w-full max-w-md shadow-2xl relative">
        <h3 class="font-headline-md text-headline-md text-on-surface font-bold mb-4">Derivar Expediente {{ selectedExpediente?.id }}</h3>
        <p class="text-body-sm text-on-surface-variant mb-6">Seleccione la oficina destino para este trámite documentario:</p>

        <div class="space-y-4">
          <div class="space-y-1">
            <label class="block font-label-md text-label-md text-on-surface font-semibold" for="destino">Oficina Destino</label>
            <select
              v-model="destinoOficina"
              class="w-full bg-surface border border-outline-variant rounded-lg px-4 py-2 text-body-sm focus:outline-none focus:border-primary text-on-surface"
              id="destino"
            >
              <option value="Secretaría General">Secretaría General</option>
              <option value="Gerencia Municipal">Gerencia Municipal</option>
              <option value="Gerencia de Administración">Gerencia de Administración</option>
              <option value="Gerencia de Infraestructura">Gerencia de Infraestructura</option>
              <option value="Oficina de Presupuesto">Oficina de Presupuesto</option>
              <option value="Asesoría Jurídica">Asesoría Jurídica</option>
            </select>
          </div>
        </div>

        <div class="flex justify-end gap-3 mt-8">
          <button
            @click="derivarModalOpen = false"
            class="px-4 py-2 border border-outline-variant text-on-surface rounded font-label-md font-semibold hover:bg-surface-container-low transition-all cursor-pointer"
          >
            Cancelar
          </button>
          <button
            @click="confirmarDerivar"
            class="px-4 py-2 bg-primary text-on-primary rounded font-label-md font-bold hover:bg-primary-container transition-all cursor-pointer"
          >
            Confirmar Derivación
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useDocumentosStore } from '../stores/documentos';

const docsStore = useDocumentosStore();
const router = useRouter();

const activeTab = ref('bandeja');

// Filters
const filterPriority = ref('todas');
const filterType = ref('todos');

const appliedPriority = ref('todas');
const appliedType = ref('todos');

// Modal State
const derivarModalOpen = ref(false);
const selectedExpediente = ref(null);
const destinoOficina = ref('Secretaría General');

// Computed list of filtered items
const filtrados = computed(() => {
  return docsStore.expedientes.filter(exp => {
    // If not in bandeja tab, show nothing for now
    if (activeTab.value !== 'bandeja') return false;

    const matchPriority = appliedPriority.value === 'todas' || exp.prioridad === appliedPriority.value;
    const matchType = appliedType.value === 'todos' || exp.tipo === appliedType.value;

    return matchPriority && matchType;
  });
});

const urgentesCount = computed(() => {
  return filtrados.value.filter(exp => exp.prioridad === 'alta').length;
});

function aplicarFiltros() {
  appliedPriority.value = filterPriority.value;
  appliedType.value = filterType.value;
}

function verTrazabilidad(id) {
  router.push({ name: 'trazabilidad-expediente', params: { id } });
}

function devolver(id) {
  if (confirm(`¿Está seguro de devolver el expediente ${id}?`)) {
    docsStore.devolverExpediente(id);
    alert(`Expediente ${id} devuelto correctamente.`);
  }
}

function abrirDerivar(exp) {
  selectedExpediente.value = exp;
  destinoOficina.value = 'Secretaría General';
  derivarModalOpen.value = true;
}

function confirmarDerivar() {
  if (selectedExpediente.value) {
    docsStore.derivarExpediente(selectedExpediente.value.id, destinoOficina.value);
    derivarModalOpen.value = false;
    alert(`Expediente ${selectedExpediente.value.id} derivado exitosamente.`);
    router.push({ name: 'trazabilidad-expediente', params: { id: selectedExpediente.value.id } });
  }
}
</script>
