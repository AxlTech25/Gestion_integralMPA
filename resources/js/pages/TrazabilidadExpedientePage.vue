<template>
  <div class="space-y-6">
    <header>
      <nav class="flex items-center gap-2 text-on-surface-variant mb-2" aria-label="Breadcrumb">
        <router-link :to="{ name: 'bandeja-pendientes' }" class="text-label-md font-semibold hover:text-primary transition-colors">
          Documentos
        </router-link>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-label-md font-semibold">Trazabilidad</span>
      </nav>
      <h1 class="text-headline-xl text-on-surface font-bold">
        Historial de Trazabilidad - Expediente {{ targetCodigo }}
      </h1>
    </header>

    <div v-if="loading" class="text-on-surface-variant">Cargando expediente...</div>

    <div
      v-else-if="!expediente"
      class="bg-surface-container-lowest border border-outline-variant rounded-xl p-8 text-center space-y-4 shadow-sm"
    >
      <span class="material-symbols-outlined text-error text-[48px]">warning</span>
      <h2 class="text-headline-md text-on-surface font-semibold">Expediente no encontrado</h2>
      <p class="text-on-surface-variant max-w-md mx-auto text-body-md">
        El código <strong>{{ targetCodigo }}</strong> no existe en el registro institucional.
      </p>
      <router-link
        :to="{ name: 'bandeja-pendientes' }"
        class="inline-block bg-primary text-on-primary px-6 py-2.5 rounded-lg text-label-md font-semibold hover:bg-primary-container transition-all active:scale-95"
      >
        Volver a la Bandeja
      </router-link>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
      <section class="lg:col-span-8 space-y-gutter" aria-label="Línea de tiempo">
        <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl relative overflow-hidden">
          <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-8">
            <h2 class="text-headline-md text-on-surface flex items-center gap-2 font-semibold">
              <span class="material-symbols-outlined text-primary">route</span>
              Línea de Tiempo del Proceso
            </h2>
            <div class="flex flex-wrap gap-2">
              <button
                v-if="expediente.puede_firmar"
                type="button"
                class="px-3 py-1.5 bg-secondary text-on-secondary rounded text-label-md font-semibold flex items-center gap-1"
                :disabled="firmando"
                @click="firmar"
              >
                <span class="material-symbols-outlined text-sm">draw</span>
                {{ firmando ? 'Firmando…' : 'Firmar y sellar' }}
              </button>
              <button
                v-if="expediente.puede_recepcionar"
                type="button"
                class="px-3 py-1.5 bg-primary text-on-primary rounded text-label-md font-semibold"
                @click="recepcionar"
              >
                Recepcionar
              </button>
              <button
                v-if="expediente.puede_archivar"
                type="button"
                class="px-3 py-1.5 border border-outline-variant rounded text-on-surface-variant text-label-md hover:bg-surface-container-low font-semibold"
                @click="archivar"
              >
                Archivar
              </button>
              <button
                type="button"
                class="px-3 py-1.5 border border-outline-variant rounded text-on-surface-variant text-label-md hover:bg-surface-container-low flex items-center gap-1 transition-all cursor-pointer font-semibold"
                @click="imprimir"
              >
                <span class="material-symbols-outlined text-sm">print</span>
                Imprimir
              </button>
            </div>
          </div>

          <div class="relative space-y-12 pb-4">
            <TimelineNode
              v-for="(node, index) in expediente.historial"
              :key="`${node.titulo}-${index}`"
              :node="node"
              :oficina-actual="expediente.oficina_actual"
            />
          </div>
        </div>
      </section>

      <aside class="lg:col-span-4 space-y-gutter" aria-label="Detalles del expediente">
        <div class="bg-primary text-on-primary p-6 rounded-xl shadow-lg relative overflow-hidden">
          <p class="text-label-md uppercase tracking-wider mb-2 opacity-80 font-semibold">Estado</p>
          <p class="text-headline-md font-bold mb-2">{{ estadoLabel }}</p>
          <p class="text-body-sm opacity-90">{{ expediente.antiguedad_dias }} días en trámite</p>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl">
          <h2 class="text-headline-md text-on-surface mb-4 font-semibold">Detalles del Expediente</h2>
          <div class="space-y-4">
            <div class="border-b border-outline-variant pb-3">
              <p class="text-label-md text-on-surface-variant uppercase mb-1 font-semibold">Prioridad</p>
              <div class="flex items-center gap-2">
                <span :class="[prioridadDot, 'w-3 h-3 rounded-full']" />
                <p class="text-body-md font-bold text-on-surface">{{ prioridadLabel }}</p>
              </div>
            </div>
            <div class="border-b border-outline-variant pb-3">
              <p class="text-label-md text-on-surface-variant uppercase mb-1 font-semibold">Tipo de Documento</p>
              <p class="text-body-md font-medium text-on-surface">{{ expediente.tipo }}</p>
            </div>
            <div class="border-b border-outline-variant pb-3">
              <p class="text-label-md text-on-surface-variant uppercase mb-1 font-semibold">Asunto</p>
              <p class="text-body-md text-on-surface">{{ expediente.asunto }}</p>
            </div>
            <div v-if="expediente.documento_principal" class="border-b border-outline-variant pb-3">
              <p class="text-label-md text-on-surface-variant uppercase mb-1 font-semibold">Documento principal</p>
              <p class="text-body-md text-on-surface">{{ expediente.documento_principal.titulo }}</p>
              <p class="text-body-sm text-on-surface-variant mt-1">
                Estado: {{ expediente.documento_principal.firmado ? 'Firmado y sellado' : 'Pendiente de firma' }}
              </p>
            </div>
            <AnexoList :anexos="expediente.anexos ?? []" @download="descargarAnexo" />
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useDocumentosStore } from '../stores/documentos';
import TimelineNode from '../components/documentos/TimelineNode.vue';
import AnexoList from '../components/documentos/AnexoList.vue';
import { PRIORIDAD_LABELS, PRIORIDAD_DOT } from '../utils/expediente';

const route = useRoute();
const docsStore = useDocumentosStore();
const loading = ref(true);
const firmando = ref(false);

const targetCodigo = computed(() => route.params.id || '');

const expediente = computed(() => docsStore.expedienteActual);

const prioridadLabel = computed(() => {
    if (!expediente.value) return '';
    return PRIORIDAD_LABELS[expediente.value.prioridad] ?? expediente.value.prioridad?.toUpperCase();
});

const prioridadDot = computed(() => {
    if (!expediente.value) return 'bg-outline';
    return PRIORIDAD_DOT[expediente.value.prioridad] ?? 'bg-outline';
});

const estadoLabel = computed(() => {
    const map = {
        registrado: 'Registrado',
        por_recepcionar: 'Por recepcionar',
        en_tramite: 'En trámite',
        devuelto: 'Devuelto',
        archivado: 'Archivado',
    };
    return map[expediente.value?.estado] ?? expediente.value?.estado;
});

async function load() {
    if (!targetCodigo.value) return;
    loading.value = true;
    try {
        await docsStore.cargarExpediente(targetCodigo.value);
    } catch {
        docsStore.expedienteActual = null;
    } finally {
        loading.value = false;
    }
}

onMounted(load);
watch(targetCodigo, load);

function imprimir() {
    window.print();
}

async function recepcionar() {
    if (!expediente.value?.id) return;
    await docsStore.recepcionarExpediente(expediente.value.id);
}

async function firmar() {
    const docId = expediente.value?.documento_principal?.id;
    if (!docId) return;
    firmando.value = true;
    try {
        await docsStore.firmarDocumento(docId);
    } finally {
        firmando.value = false;
    }
}

async function archivar() {
    if (!expediente.value?.id) return;
    if (!confirm(`¿Archivar el expediente ${expediente.value.codigo}?`)) return;
    await docsStore.archivarExpediente(expediente.value.id);
}

async function descargarAnexo(anexo) {
    if (!anexo.id) return;
    await docsStore.descargarAdjunto(anexo.id, anexo.nombre);
}
</script>
