<template>
  <div class="space-y-6">
    <!-- Breadcrumbs & Title -->
    <div>
      <div class="flex items-center gap-2 text-on-surface-variant mb-2">
        <router-link :to="{ name: 'bandeja-pendientes' }" class="font-label-md text-label-md hover:text-primary transition-colors">
          Documentos
        </router-link>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="font-label-md text-label-md">Trazabilidad</span>
      </div>
      <h3 class="font-headline-xl text-headline-xl text-on-surface font-bold">
        Historial de Trazabilidad - Expediente Nro. {{ targetId }}
      </h3>
    </div>

    <!-- Error State -->
    <div v-if="!expediente" class="bg-white border border-outline-variant rounded-xl p-8 text-center space-y-4 shadow-sm">
      <span class="material-symbols-outlined text-error text-[48px]">warning</span>
      <h4 class="font-headline-md text-headline-md text-on-surface font-bold">Expediente no encontrado</h4>
      <p class="text-on-surface-variant max-w-md mx-auto">
        El código de expediente <strong>{{ targetId }}</strong> no existe en el registro institucional. Verifique el código e inténtelo de nuevo.
      </p>
      <router-link
        :to="{ name: 'bandeja-pendientes' }"
        class="inline-block bg-primary text-on-primary px-6 py-2.5 rounded-lg font-label-md font-bold hover:bg-primary-container transition-all active:scale-95 shadow"
      >
        Volver a la Bandeja
      </router-link>
    </div>

    <!-- Content Layout (Bento Grid) -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
      <!-- Main Timeline Column (Left 2/3) -->
      <div class="lg:col-span-8 space-y-gutter">
        <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl relative overflow-hidden shadow-sm">
          <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-8">
            <h4 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2 font-bold">
              <span class="material-symbols-outlined text-primary">route</span>
              Línea de Tiempo del Proceso
            </h4>
            <div class="flex gap-2">
              <button @click="imprimir" class="px-3 py-1.5 border border-outline-variant rounded text-on-surface-variant font-label-md font-bold hover:bg-surface-container-low flex items-center gap-1 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-sm">print</span> Imprimir
              </button>
              <button @click="exportar" class="px-3 py-1.5 bg-primary-container text-on-primary-container rounded font-label-md font-bold hover:brightness-95 flex items-center gap-1 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-sm">share</span> Exportar
              </button>
            </div>
          </div>

          <!-- Vertical Timeline -->
          <div class="relative space-y-12 pb-4 pt-2">
            <!-- Dynamic Nodes -->
            <div
              v-for="(node, index) in expediente.historial"
              :key="index"
              class="timeline-item flex gap-6"
            >
              <!-- Icon Node Indicator -->
              <div class="relative z-10">
                <div
                  :class="[
                    node.estado === 'ESTADO ACTUAL'
                      ? (node.color === 'secondary' ? 'bg-secondary-container text-on-secondary-container border-secondary' : 'bg-primary-container text-on-primary-container border-primary')
                      : 'bg-surface-container-highest text-on-surface border-outline-variant',
                    'w-12 h-12 rounded-full flex items-center justify-center shadow-md border-4 bg-white shrink-0'
                  ]"
                >
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">
                    {{ node.icono }}
                  </span>
                </div>
              </div>

              <!-- Content Card -->
              <div
                :class="[
                  node.estado === 'ESTADO ACTUAL' ? 'bg-surface-container-low border border-secondary/30' : 'bg-white border border-outline-variant hover:border-primary/40',
                  'flex-1 p-4 rounded-lg transition-colors'
                ]"
              >
                <div class="flex flex-col sm:flex-row justify-between sm:items-start gap-1 mb-2">
                  <h5 class="font-headline-md text-headline-md text-on-surface font-bold leading-tight">{{ node.titulo }}</h5>
                  <span
                    v-if="node.estado === 'ESTADO ACTUAL'"
                    class="w-fit text-body-sm text-secondary font-bold px-2 py-0.5 bg-secondary/10 rounded text-[10px]"
                  >
                    ESTADO ACTUAL
                  </span>
                  <span
                    v-else
                    class="text-body-sm text-on-surface-variant bg-surface-container px-2 py-0.5 rounded text-[10px]"
                  >
                    {{ node.fecha }}
                  </span>
                </div>
                
                <p class="text-body-md text-on-surface-variant leading-relaxed mb-3">
                  {{ node.descripcion }}
                </p>

                <!-- Additional fields for the active state/nodes -->
                <div v-if="node.estado === 'ESTADO ACTUAL' && index === 0" class="grid grid-cols-2 gap-4 text-sm mt-3 pt-3 border-t border-outline-variant/30">
                  <div>
                    <p class="text-label-md text-on-surface-variant uppercase mb-1 font-bold text-[10px]">Fecha Actualización</p>
                    <p class="text-body-md font-semibold text-on-surface">{{ node.fecha.split(' - ')[0] }}</p>
                  </div>
                  <div>
                    <p class="text-label-md text-on-surface-variant uppercase mb-1 font-bold text-[10px]">Oficina Actual</p>
                    <p class="text-body-md font-semibold text-on-surface">{{ expediente.oficina_actual }}</p>
                  </div>
                </div>

                <div v-if="node.extra" class="flex items-center gap-2 mt-2 text-sm text-on-surface-variant">
                  <span class="material-symbols-outlined text-primary text-sm">verified_user</span>
                  <span class="font-medium text-[12px] italic">{{ node.extra }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Metadata & Sidebar Column (Right 1/3) -->
      <div class="lg:col-span-4 space-y-gutter">
        <!-- KPI Card: Efficiency -->
        <div class="bg-primary text-on-primary p-6 rounded-xl shadow relative overflow-hidden">
          <div class="absolute top-0 right-0 p-4 opacity-20">
            <span class="material-symbols-outlined text-[80px]">schedule</span>
          </div>
          <p class="font-label-md text-label-md uppercase tracking-wider mb-2 opacity-80 font-bold text-[10px] text-white">Tiempo en Gestión</p>
          <h4 class="font-headline-xl text-headline-xl mb-4 font-bold text-white">3 días 4 horas</h4>
          <div class="flex items-center gap-2 bg-white/20 px-3 py-1.5 rounded-full w-fit">
            <span class="material-symbols-outlined text-sm text-white">trending_down</span>
            <span class="font-label-md text-label-md text-white font-bold text-[11px]">15% más rápido que el promedio</span>
          </div>
        </div>

        <!-- Details Card -->
        <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl shadow-sm">
          <h4 class="font-headline-md text-headline-md text-on-surface mb-4 font-bold">Detalles del Expediente</h4>
          <div class="space-y-4">
            <div class="border-b border-outline-variant pb-3">
              <p class="text-label-md text-on-surface-variant uppercase mb-1 font-bold text-[10px]">Prioridad</p>
              <div class="flex items-center gap-2">
                <span
                  :class="[
                    expediente.prioridad === 'alta' ? 'bg-error' :
                    expediente.prioridad === 'media' ? 'bg-secondary' :
                    'bg-outline',
                    'w-3 h-3 rounded-full'
                  ]"
                ></span>
                <p class="text-body-md font-bold text-on-surface uppercase">{{ expediente.prioridad }}</p>
              </div>
            </div>

            <div class="border-b border-outline-variant pb-3">
              <p class="text-label-md text-on-surface-variant uppercase mb-1 font-bold text-[10px]">Tipo de Documento</p>
              <p class="text-body-md font-bold text-on-surface">{{ expediente.tipo }}</p>
            </div>

            <div class="border-b border-outline-variant pb-3">
              <p class="text-label-md text-on-surface-variant uppercase mb-1 font-bold text-[10px]">Unidad de Origen</p>
              <p class="text-body-md font-semibold text-on-surface-variant">{{ expediente.unidad_origen }}</p>
            </div>

            <div class="border-b border-outline-variant pb-3">
              <p class="text-label-md text-on-surface-variant uppercase mb-1 font-bold text-[10px]">Asunto / Referencia</p>
              <p class="text-body-md text-on-surface leading-relaxed">{{ expediente.asunto }}</p>
            </div>

            <div v-if="expediente.detalles" class="border-b border-outline-variant pb-3">
              <p class="text-label-md text-on-surface-variant uppercase mb-1 font-bold text-[10px]">Notas / Observaciones</p>
              <p class="text-body-sm text-on-surface-variant leading-relaxed">{{ expediente.detalles }}</p>
            </div>

            <!-- Attachments -->
            <div>
              <p class="text-label-md text-on-surface-variant uppercase mb-1 font-bold text-[10px]">Anexos</p>
              <div class="flex flex-col gap-2 mt-2">
                <div class="flex items-center justify-between bg-surface p-2 rounded text-on-surface cursor-pointer hover:bg-surface-container transition-colors border border-outline-variant">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-error">picture_as_pdf</span>
                    <span class="text-body-sm font-semibold">informe_tecnico.pdf</span>
                  </div>
                  <span class="material-symbols-outlined text-sm text-outline">download</span>
                </div>
                <div class="flex items-center justify-between bg-surface p-2 rounded text-on-surface cursor-pointer hover:bg-surface-container transition-colors border border-outline-variant">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">table_chart</span>
                    <span class="text-body-sm font-semibold">presupuesto_det.xlsx</span>
                  </div>
                  <span class="material-symbols-outlined text-sm text-outline">download</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Auditoría card -->
        <div class="bg-inverse-surface h-48 rounded-xl relative overflow-hidden p-6 flex flex-col justify-end group shadow-sm border border-outline/20">
          <div class="relative z-10">
            <h5 class="text-primary-fixed font-headline-md text-headline-md mb-1 font-bold text-white">Reporte de Auditoría</h5>
            <p class="text-surface-variant text-body-sm text-white/80">Generar trazabilidad completa para fines legales.</p>
          </div>
          <div class="absolute inset-0 bg-primary/30 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer flex items-center justify-center backdrop-blur-[2px]">
            <span class="text-on-primary font-label-md bg-primary px-4 py-2 rounded-full shadow-lg font-bold">Descargar Reporte Legal</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useDocumentosStore } from '../stores/documentos';

const route = useRoute();
const docsStore = useDocumentosStore();

const targetId = computed(() => {
  return route.params.id || 'EXP-2026-0045';
});

const expediente = computed(() => {
  return docsStore.obtenerExpedientePorId(targetId.value);
});

function imprimir() {
  window.print();
}

function exportar() {
  alert(`Exportando trazabilidad completa del expediente ${targetId.value} en formato PDF.`);
}
</script>
