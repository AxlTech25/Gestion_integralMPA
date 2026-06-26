<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <header class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
      <div>
        <h1 class="text-headline-xl text-on-surface font-bold">Dashboard Estratégico</h1>
        <p class="text-body-lg text-on-surface-variant">
          Indicadores consolidados de gestión documental e infraestructura tecnológica.
          <span v-if="alcanceLabel" class="block text-label-md mt-1 text-primary font-semibold">
            Alcance: {{ alcanceLabel }}
          </span>
        </p>
      </div>
      <select
        v-model="periodo"
        class="px-4 py-2 border border-outline-variant rounded-lg text-label-md bg-surface"
        @change="recargar"
      >
        <option :value="7">7 días</option>
        <option :value="30">30 días</option>
        <option :value="90">90 días</option>
      </select>
    </header>

    <div v-if="dashStore.error" class="bg-error-container/30 border border-error rounded-lg p-4 text-body-sm">
      {{ dashStore.error }}
    </div>

    <div v-if="dashStore.loading" class="text-on-surface-variant">Cargando dashboard...</div>

    <template v-else-if="dashStore.estrategico">
      <section class="grid grid-cols-1 md:grid-cols-3 gap-6" aria-label="Indicadores estratégicos">
        <StrategicKpiCard
          label="Expedientes Pendientes"
          :value="String(kpis.expedientes_pendientes ?? 0)"
          icon="pending_actions"
          :trend="{ text: kpis.tendencia_pendientes ?? '', icon: 'trending_up', class: 'text-error' }"
        />
        <StrategicKpiCard
          label="Tramitados Hoy"
          :value="String(kpis.tramitados_hoy ?? 0)"
          icon="done_all"
          :trend="{ text: `Meta diaria al ${kpis.meta_diaria_pct ?? 0}%`, icon: 'check_circle', class: 'text-primary' }"
        />
        <SemaphoreChart
          :critico="semaforo.rojo"
          :alerta="semaforo.amarillo"
          :estable="semaforo.verde"
        />
      </section>

      <section class="grid grid-cols-1 lg:grid-cols-2 gap-6" aria-label="Análisis operativo">
        <GerenciaBarChart :gerencias="gerencias" />
        <AlertasTiTable :alertas="alertasTi" @accion="onAlertaAccion" />
      </section>

      <aside
        v-if="sugerencia"
        class="bg-surface-container-highest/20 border-l-4 border-secondary p-4 rounded-r-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
        aria-label="Sugerencia estratégica"
      >
        <div class="flex items-center gap-4">
          <div class="w-10 h-10 bg-secondary/10 rounded-full flex items-center justify-center text-secondary shrink-0">
            <span class="material-symbols-outlined">priority_high</span>
          </div>
          <div>
            <h2 class="font-bold text-on-surface text-body-md">{{ sugerencia.titulo }}</h2>
            <p class="text-on-surface-variant text-body-sm">{{ sugerencia.texto }}</p>
          </div>
        </div>
        <button
          v-if="sugerencia.equipo_id"
          type="button"
          class="px-4 py-2 bg-inverse-surface text-inverse-on-surface rounded-lg text-body-sm font-bold shrink-0"
          @click="verEquipo(sugerencia.equipo_id)"
        >
          Ver equipo
        </button>
      </aside>

      <div
        v-if="siaf"
        class="bg-surface border border-outline-variant rounded-xl p-6"
      >
        <h2 class="text-headline-md font-semibold mb-2">Ejecución presupuestal (SIAF)</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-body-sm">
          <div>
            <p class="text-on-surface-variant">PIM</p>
            <p class="text-headline-md font-bold">S/ {{ formatMoney(siaf.pim) }}</p>
          </div>
          <div>
            <p class="text-on-surface-variant">Ejecutado</p>
            <p class="text-headline-md font-bold">S/ {{ formatMoney(siaf.ejecucion_total) }}</p>
          </div>
          <div>
            <p class="text-on-surface-variant">% Ejecución</p>
            <p class="text-headline-md font-bold text-primary">{{ siaf.porcentaje_ejecucion }}%</p>
          </div>
        </div>
        <p v-if="siaf.es_simulacion" class="text-label-md text-secondary mt-3">Datos de simulación — PA-19</p>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useDashboardStore } from '../stores/dashboard';
import StrategicKpiCard from '../components/dashboard/StrategicKpiCard.vue';
import SemaphoreChart from '../components/dashboard/SemaphoreChart.vue';
import GerenciaBarChart from '../components/dashboard/GerenciaBarChart.vue';
import AlertasTiTable from '../components/dashboard/AlertasTiTable.vue';

const dashStore = useDashboardStore();
const router = useRouter();
const periodo = ref(30);

onMounted(() => recargar());

function recargar() {
    dashStore.cargarEstrategico(periodo.value).catch(() => {});
}

const kpis = computed(() => dashStore.estrategico?.kpis ?? {});
const alcanceLabel = computed(() => {
    const a = dashStore.estrategico?.alcance;
    if (!a) return '';
    if (a === 'institucional') return 'Institucional';
    return a.length > 48 ? a.slice(0, 46) + '…' : a;
});
const semaforo = computed(() => dashStore.estrategico?.semaforo_ti ?? { verde: 0, amarillo: 0, rojo: 0 });
const gerencias = computed(() => dashStore.estrategico?.tramitacion_gerencias ?? []);
const alertasTi = computed(() => dashStore.estrategico?.alertas_ti ?? []);
const sugerencia = computed(() => dashStore.estrategico?.sugerencia);
const siaf = computed(() => dashStore.estrategico?.siaf);

function onAlertaAccion(alerta) {
    if (alerta.equipo_id) {
        router.push({ name: 'patrimonio-equipo', params: { id: alerta.equipo_id } });
    }
}

function verEquipo(id) {
    router.push({ name: 'patrimonio-equipo', params: { id } });
}

function formatMoney(val) {
    return Number(val).toLocaleString('es-PE', { minimumFractionDigits: 0 });
}
</script>
