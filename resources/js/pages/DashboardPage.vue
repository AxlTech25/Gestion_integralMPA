<template>
  <div class="space-y-6">
    <!-- Dashboard Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h3 class="font-headline-xl text-headline-xl text-on-surface font-bold">Panel de Control</h3>
        <p class="font-body-lg text-body-lg text-on-surface-variant">Vista general del estado operativo de la Municipalidad de Acobamba.</p>
      </div>
      <div class="flex gap-2">
        <button class="px-4 py-2 bg-surface border border-outline-variant text-on-surface-variant rounded flex items-center gap-2 hover:bg-surface-container-low transition-colors font-label-md text-label-md font-bold cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">calendar_today</span>
          Últimos 30 días
        </button>
        <button class="px-4 py-2 bg-primary text-on-primary rounded flex items-center gap-2 hover:bg-primary-container transition-colors font-label-md text-label-md font-bold cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">download</span>
          Exportar
        </button>
      </div>
    </div>

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter">
      <!-- KPI 1 -->
      <div class="bg-surface-container-lowest border border-outline-variant p-4 flex flex-col justify-between h-32 hover:border-primary transition-all duration-200 cursor-default rounded-lg">
        <div class="flex justify-between items-start">
          <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Trámites Pendientes</span>
          <span class="material-symbols-outlined text-primary">pending_actions</span>
        </div>
        <div class="flex items-end justify-between">
          <span class="font-headline-xl text-headline-xl text-on-surface font-bold">{{ docsStore.obtenerPendientesCount }}</span>
          <span class="flex items-center text-error font-label-md text-label-md font-bold">
            <span class="material-symbols-outlined text-[16px]">arrow_upward</span>
            12%
          </span>
        </div>
      </div>
      <!-- KPI 2 -->
      <div class="bg-surface-container-lowest border border-outline-variant p-4 flex flex-col justify-between h-32 hover:border-primary transition-all duration-200 cursor-default rounded-lg">
        <div class="flex justify-between items-start">
          <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Presupuesto Ejecutado</span>
          <span class="material-symbols-outlined text-secondary">payments</span>
        </div>
        <div class="flex items-end justify-between">
          <span class="font-headline-xl text-headline-xl text-on-surface font-bold">64.8%</span>
          <span class="flex items-center text-primary font-label-md text-label-md font-bold">
            <span class="material-symbols-outlined text-[16px]">check_circle</span>
            En meta
          </span>
        </div>
      </div>
      <!-- KPI 3 -->
      <div class="bg-surface-container-lowest border border-outline-variant p-4 flex flex-col justify-between h-32 hover:border-primary transition-all duration-200 cursor-default rounded-lg">
        <div class="flex justify-between items-start">
          <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Tickets Soporte IT</span>
          <span class="material-symbols-outlined text-on-tertiary-container">support_agent</span>
        </div>
        <div class="flex items-end justify-between">
          <span class="font-headline-xl text-headline-xl text-on-surface font-bold">08</span>
          <span class="flex items-center text-primary font-label-md text-label-md font-bold">
            <span class="material-symbols-outlined text-[16px]">arrow_downward</span>
            5%
          </span>
        </div>
      </div>
      <!-- KPI 4 -->
      <div class="bg-surface-container-lowest border border-outline-variant p-4 flex flex-col justify-between h-32 hover:border-primary transition-all duration-200 cursor-default rounded-lg">
        <div class="flex justify-between items-start">
          <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Doc. por Firmar</span>
          <span class="material-symbols-outlined text-tertiary">draw</span>
        </div>
        <div class="flex items-end justify-between">
          <span class="font-headline-xl text-headline-xl text-on-surface font-bold">{{ docsStore.obtenerUrgentesCount }}</span>
          <span class="flex items-center text-tertiary font-label-md text-label-md font-bold">Urgente</span>
        </div>
      </div>
    </div>

    <!-- Table Section & Info Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
      <!-- Table (Left 2/3) -->
      <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant overflow-hidden flex flex-col rounded-lg">
        <div class="px-6 py-4 border-b border-outline-variant bg-surface-container">
          <h4 class="font-headline-md text-headline-md text-on-surface font-bold">Actividad Reciente</h4>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-surface-container-low border-b border-outline-variant">
              <tr>
                <th class="px-6 py-3 font-label-md text-label-md text-on-surface-variant uppercase font-bold">DOCUMENTO</th>
                <th class="px-6 py-3 font-label-md text-label-md text-on-surface-variant uppercase font-bold">UNIDAD ORIGEN</th>
                <th class="px-6 py-3 font-label-md text-label-md text-on-surface-variant uppercase font-bold">ESTADO</th>
                <th class="px-6 py-3 font-label-md text-label-md text-on-surface-variant uppercase font-bold">FECHA</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
              <tr
                v-for="exp in recentExpedientes"
                :key="exp.id"
                class="hover:bg-primary/5 transition-colors h-10 cursor-pointer"
                @click="verTrazabilidad(exp.id)"
              >
                <td class="px-6 py-3 font-table-data text-table-data font-bold text-primary">{{ exp.id }}</td>
                <td class="px-6 py-3 font-table-data text-table-data">{{ exp.unidad_origen }}</td>
                <td class="px-6 py-3">
                  <span
                    :class="[
                      obtenerEstadoClass(exp.estado),
                      'px-2 py-0.5 rounded-full font-label-md text-[10px] font-bold uppercase'
                    ]"
                  >
                    {{ exp.estado }}
                  </span>
                </td>
                <td class="px-6 py-3 font-table-data text-table-data">{{ exp.fecha_actualizacion }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-6 py-3 border-t border-outline-variant bg-surface-container-low text-right">
          <router-link :to="{ name: 'bandeja-pendientes' }" class="text-primary font-label-md text-label-md hover:underline font-bold">
            Ver todo el historial
          </router-link>
        </div>
      </div>

      <!-- Info Column (Right 1/3) -->
      <div class="flex flex-col gap-gutter">
        <div class="bg-surface-container-lowest border border-outline-variant p-6 flex flex-col gap-4 rounded-lg">
          <h4 class="font-headline-md text-headline-md text-on-surface border-b border-outline-variant pb-2 font-bold">Noticias Internas</h4>
          <div class="space-y-4">
            <div
              v-for="noticia in docsStore.noticias"
              :key="noticia.id"
              :class="[
                noticia.tipo === 'primary' ? 'border-primary' : 'border-secondary',
                'border-l-4 pl-4'
              ]"
            >
              <p
                :class="[
                  noticia.tipo === 'primary' ? 'text-primary' : 'text-secondary',
                  'font-label-md text-label-md font-bold'
                ]"
              >
                {{ noticia.tag }}
              </p>
              <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">{{ noticia.texto }}</p>
            </div>
          </div>
        </div>

        <div class="bg-inverse-surface text-primary-fixed p-6 rounded-lg relative overflow-hidden">
          <div class="relative z-10">
            <h4 class="font-headline-md text-headline-md mb-2 font-bold text-white">Ayuda y Soporte</h4>
            <p class="font-body-sm text-body-sm opacity-80 mb-4 leading-relaxed text-white/90">¿Necesitas asistencia con el módulo de Gestión Documental?</p>
            <button class="w-full py-2 bg-primary text-on-primary rounded font-label-md text-label-md font-bold hover:brightness-110 transition-all cursor-pointer">
              Contactar Soporte
            </button>
          </div>
          <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-[120px] opacity-10 text-white">help_center</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useDocumentosStore } from '../stores/documentos';

const docsStore = useDocumentosStore();
const router = useRouter();

// Show the 3 most recently updated documents
const recentExpedientes = computed(() => {
  return docsStore.expedientes.slice(0, 3);
});

function verTrazabilidad(id) {
  router.push({ name: 'trazabilidad-expediente', params: { id } });
}

function obtenerEstadoClass(estado) {
  switch (estado) {
    case 'REVISIÓN':
      return 'bg-secondary-container text-on-secondary-container';
    case 'APROBADO':
      return 'bg-primary-container text-on-primary-container';
    case 'RECIBIDO':
      return 'bg-surface-variant text-on-surface-variant';
    case 'EN TRÁNSITO':
      return 'bg-secondary/15 text-secondary';
    default:
      return 'bg-surface-variant text-on-surface-variant';
  }
}
</script>
