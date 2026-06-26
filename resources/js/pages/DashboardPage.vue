<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h1 class="text-headline-xl text-on-surface font-bold">Panel de Control</h1>
        <p class="text-body-lg text-on-surface-variant">
          Vista general del estado operativo de la Municipalidad de Acobamba.
          <span v-if="alcanceLabel" class="block text-label-md mt-1 text-primary font-semibold">
            Alcance: {{ alcanceLabel }}
          </span>
        </p>
      </div>
      <div class="flex gap-2">
        <select
          v-model="periodo"
          class="px-4 py-2 bg-surface border border-outline-variant rounded text-label-md font-semibold"
          @change="recargar"
        >
          <option :value="7">Últimos 7 días</option>
          <option :value="30">Últimos 30 días</option>
          <option :value="90">Últimos 90 días</option>
        </select>
      </div>
    </header>

    <div v-if="dashStore.error" class="bg-error-container/30 border border-error rounded-lg p-4 text-body-sm text-on-error-container">
      {{ dashStore.error }}
    </div>

    <div v-if="dashStore.loading" class="text-on-surface-variant">Cargando indicadores...</div>

    <template v-else-if="dashStore.operativo">
      <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter" aria-label="Indicadores operativos">
        <KpiCard
          v-for="kpi in kpiCards"
          :key="kpi.label"
          :label="kpi.label"
          :value="kpi.value"
          :icon="kpi.icon"
          :icon-class="kpi.iconClass"
          :trend="kpi.trend"
        />
      </section>

      <section
        v-if="tramitacion?.por_unidad?.length"
        class="grid grid-cols-1 lg:grid-cols-2 gap-gutter"
        aria-label="Tramitación por unidad"
      >
        <UnidadTramitacionChart :unidades="tramitacion.por_unidad" />
        <GerenciaBarChart
          v-if="tramitacion?.por_gerencia?.length"
          :gerencias="tramitacion.por_gerencia"
        />
      </section>

      <section class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
        <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant overflow-hidden flex flex-col">
          <div class="px-6 py-4 border-b border-outline-variant bg-surface-container">
            <h2 class="text-headline-md text-on-surface font-semibold">Actividad Reciente</h2>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                  <th class="px-6 py-3 text-label-md text-on-surface-variant font-semibold">DOCUMENTO</th>
                  <th class="px-6 py-3 text-label-md text-on-surface-variant font-semibold">UNIDAD</th>
                  <th class="px-6 py-3 text-label-md text-on-surface-variant font-semibold">ESTADO</th>
                  <th class="px-6 py-3 text-label-md text-on-surface-variant font-semibold">FECHA</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant">
                <tr
                  v-for="exp in actividadReciente"
                  :key="exp.codigo"
                  class="hover:bg-primary/5 transition-colors h-10 cursor-pointer"
                  @click="verTrazabilidad(exp.codigo)"
                >
                  <td class="px-6 py-3 text-table-data font-medium text-primary">{{ exp.codigo }}</td>
                  <td class="px-6 py-3 text-table-data">{{ abreviarUnidad(exp.unidad_origen) }}</td>
                  <td class="px-6 py-3">
                    <EstadoBadge :estado="exp.estado" />
                  </td>
                  <td class="px-6 py-3 text-table-data">{{ formatFecha(exp.updated_at) }}</td>
                </tr>
                <tr v-if="actividadReciente.length === 0">
                  <td colspan="4" class="px-6 py-8 text-center text-body-sm text-on-surface-variant">
                    No hay actividad documental reciente.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="px-6 py-3 border-t border-outline-variant bg-surface-container-low text-right">
            <router-link
              :to="{ name: 'bandeja-pendientes' }"
              class="text-primary text-label-md font-semibold hover:underline"
            >
              Ver bandeja completa
            </router-link>
          </div>
        </div>

        <aside class="flex flex-col gap-gutter">
          <div
            v-if="siaf"
            class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl"
          >
            <h2 class="text-headline-md font-semibold mb-2">Ejecución presupuestal</h2>
            <p class="text-headline-xl font-bold text-primary">{{ siaf.porcentaje_ejecucion }}%</p>
            <p class="text-body-sm text-on-surface-variant mt-1">
              PIM S/ {{ formatMoney(siaf.pim) }} · Periodo {{ siaf.periodo }}
            </p>
            <p v-if="siaf.es_simulacion" class="text-label-md text-secondary mt-2">Datos de simulación SIAF</p>
          </div>

          <div class="bg-inverse-surface text-primary-fixed p-6 rounded-lg relative overflow-hidden">
            <div class="relative z-10">
              <h2 class="text-headline-md font-semibold mb-2">Ayuda y Soporte</h2>
              <p class="text-body-sm opacity-80 mb-4">
                ¿Necesitas asistencia con el módulo de Gestión Documental?
              </p>
              <a
                href="mailto:utis@mpa.gob.pe"
                class="block w-full py-2 bg-primary text-on-primary rounded text-label-md font-semibold text-center hover:brightness-110 transition-all"
              >
                Contactar Soporte
              </a>
            </div>
            <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-[120px] opacity-10 pointer-events-none">
              help_center
            </span>
          </div>
        </aside>
      </section>
    </template>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useDashboardStore } from '../stores/dashboard';
import { useAuthStore } from '../stores/auth';
import { abreviarUnidad } from '../utils/estadoDocumento';
import KpiCard from '../components/dashboard/KpiCard.vue';
import EstadoBadge from '../components/dashboard/EstadoBadge.vue';
import UnidadTramitacionChart from '../components/dashboard/UnidadTramitacionChart.vue';
import GerenciaBarChart from '../components/dashboard/GerenciaBarChart.vue';

const dashStore = useDashboardStore();
const auth = useAuthStore();
const router = useRouter();
const periodo = ref(30);

onMounted(() => {
    if (auth.can('doc.expediente.consultar') || auth.can('dash.tramitacion.ver')) {
        recargar();
    }
});

function recargar() {
    dashStore.cargarOperativo(periodo.value).catch(() => {});
}

const kpis = computed(() => dashStore.operativo?.kpis ?? {});
const actividadReciente = computed(() => dashStore.operativo?.actividad_reciente ?? []);
const siaf = computed(() => dashStore.operativo?.siaf);
const tramitacion = computed(() => dashStore.operativo?.tramitacion);
const alcanceLabel = computed(() => {
    const a = dashStore.operativo?.alcance;
    if (!a) return '';
    if (a === 'institucional') return 'Institucional';
    return a.length > 48 ? a.slice(0, 46) + '…' : a;
});

const kpiCards = computed(() => [
    {
        label: 'Trámites Pendientes',
        value: String(kpis.value.pendientes ?? 0),
        icon: 'pending_actions',
        iconClass: 'text-primary',
        trend: { text: `${kpis.value.promedio_dias ?? 0} días prom.`, icon: 'schedule', class: 'text-on-surface-variant' },
    },
    {
        label: 'Presupuesto Ejecutado',
        value: siaf.value ? `${siaf.value.porcentaje_ejecucion}%` : '—',
        icon: 'payments',
        iconClass: 'text-secondary',
        trend: siaf.value?.es_simulacion
            ? { text: 'Simulación SIAF', icon: 'info', class: 'text-secondary' }
            : { text: 'SIAF', icon: 'check_circle', class: 'text-primary' },
    },
    {
        label: 'Por recepcionar',
        value: String(kpis.value.por_recepcionar ?? 0),
        icon: 'mail',
        iconClass: 'text-on-tertiary-container',
        trend: { text: 'Bandeja', icon: 'inbox', class: 'text-primary' },
    },
    {
        label: 'Urgentes',
        value: String(kpis.value.urgentes ?? 0),
        icon: 'priority_high',
        iconClass: 'text-error',
        trend: { text: 'Prioridad alta', class: 'text-error' },
    },
]);

function verTrazabilidad(codigo) {
    router.push({ name: 'trazabilidad-expediente', params: { id: codigo } });
}

function formatFecha(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('es-PE');
}

function formatMoney(val) {
    return Number(val).toLocaleString('es-PE', { minimumFractionDigits: 0 });
}
</script>
