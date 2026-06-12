<template>
  <div class="space-y-6">
    <!-- KPI Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- KPI Card 1 -->
      <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl flex flex-col justify-between hover:border-primary transition-all duration-200 cursor-default">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-label-md text-label-md text-on-surface-variant mb-1 uppercase tracking-wider font-semibold">Expedientes Pendientes</p>
            <h3 class="font-headline-xl text-headline-xl text-on-surface font-bold">1,248</h3>
          </div>
          <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-[32px]">pending_actions</span>
          </div>
        </div>
        <div class="mt-4 flex items-center text-error text-body-sm font-semibold">
          <span class="material-symbols-outlined text-[18px] mr-1">trending_up</span>
          <span>+4.2% desde ayer</span>
        </div>
      </div>

      <!-- KPI Card 2 -->
      <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl flex flex-col justify-between hover:border-primary transition-all duration-200 cursor-default">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-label-md text-label-md text-on-surface-variant mb-1 uppercase tracking-wider font-semibold">Tramitados Hoy</p>
            <h3 class="font-headline-xl text-headline-xl text-on-surface font-bold">312</h3>
          </div>
          <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-[32px]">done_all</span>
          </div>
        </div>
        <div class="mt-4 flex items-center text-primary text-body-sm font-semibold">
          <span class="material-symbols-outlined text-[18px] mr-1">check_circle</span>
          <span>Meta diaria al 85%</span>
        </div>
      </div>

      <!-- KPI Card 3: Semaphore -->
      <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl hover:border-primary transition-all duration-200 cursor-default">
        <div class="flex justify-between items-center mb-4">
          <p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Equipos en Riesgo TI</p>
          <span class="material-symbols-outlined text-on-surface-variant">monitor_heart</span>
        </div>
        <div class="flex items-end justify-between h-20 px-2">
          <div class="flex flex-col items-center">
            <div class="w-12 bg-error rounded-t-lg flex items-center justify-center text-white font-bold text-sm transition-all duration-500" :style="{ height: animateActive ? '20%' : '0%' }">
              <span class="text-[10px] text-white">3</span>
            </div>
            <span class="text-[10px] mt-1 text-on-surface-variant font-bold">CRÍTICO</span>
          </div>
          <div class="flex flex-col items-center">
            <div class="w-12 bg-secondary rounded-t-lg flex items-center justify-center text-on-secondary-fixed font-bold text-sm transition-all duration-500" :style="{ height: animateActive ? '50%' : '0%' }">
              <span class="text-[10px] text-on-secondary-fixed">8</span>
            </div>
            <span class="text-[10px] mt-1 text-on-surface-variant font-bold">ALERTA</span>
          </div>
          <div class="flex flex-col items-center">
            <div class="w-12 bg-primary rounded-t-lg flex items-center justify-center text-white font-bold text-sm transition-all duration-500" :style="{ height: animateActive ? '100%' : '0%' }">
              <span class="text-[10px] text-white">45</span>
            </div>
            <span class="text-[10px] mt-1 text-on-surface-variant font-bold">ESTABLE</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Chart column -->
      <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl">
        <div class="flex justify-between items-center mb-8">
          <h4 class="font-headline-md text-headline-md text-on-surface font-bold">Tramitación por Gerencia</h4>
          <span class="material-symbols-outlined text-outline cursor-pointer">more_vert</span>
        </div>
        <div class="space-y-6 h-64 flex flex-col justify-end">
          <div class="grid grid-cols-4 gap-4 items-end flex-1 h-full pt-4">
            <!-- Infraestructura -->
            <div class="flex flex-col items-center group h-full justify-end">
              <div class="relative w-full">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity">145</div>
                <div class="bg-primary chart-bar w-full rounded-t-md hover:brightness-110 transition-all duration-500 cursor-pointer" :style="{ height: animateActive ? '85%' : '0%' }"></div>
              </div>
              <span class="text-[11px] mt-2 font-semibold text-on-surface-variant text-center leading-tight">Infraestructura</span>
            </div>
            <!-- Salud -->
            <div class="flex flex-col items-center group h-full justify-end">
              <div class="relative w-full">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity">98</div>
                <div class="bg-secondary chart-bar w-full rounded-t-md hover:brightness-110 transition-all duration-500 cursor-pointer" :style="{ height: animateActive ? '60%' : '0%' }"></div>
              </div>
              <span class="text-[11px] mt-2 font-semibold text-on-surface-variant text-center leading-tight">Salud</span>
            </div>
            <!-- Educación -->
            <div class="flex flex-col items-center group h-full justify-end">
              <div class="relative w-full">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity">112</div>
                <div class="bg-primary/60 chart-bar w-full rounded-t-md hover:brightness-110 transition-all duration-500 cursor-pointer" :style="{ height: animateActive ? '70%' : '0%' }"></div>
              </div>
              <span class="text-[11px] mt-2 font-semibold text-on-surface-variant text-center leading-tight">Educación</span>
            </div>
            <!-- Rentas -->
            <div class="flex flex-col items-center group h-full justify-end">
              <div class="relative w-full">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity">168</div>
                <div class="bg-secondary/70 chart-bar w-full rounded-t-md hover:brightness-110 transition-all duration-500 cursor-pointer" :style="{ height: animateActive ? '95%' : '0%' }"></div>
              </div>
              <span class="text-[11px] mt-2 font-semibold text-on-surface-variant text-center leading-tight">Rentas</span>
            </div>
          </div>
        </div>
      </div>

      <!-- IT Alertas table -->
      <div class="bg-surface-container-lowest border border-outline-variant p-0 rounded-xl overflow-hidden flex flex-col">
        <div class="p-6 border-b border-outline-variant bg-surface-container-low">
          <h4 class="font-headline-md text-headline-md text-on-surface font-bold">Alertas de Inventario TI</h4>
        </div>
        <div class="flex-1 overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container-high/50 border-b border-outline-variant">
                <th class="px-6 py-3 font-label-md text-label-md text-on-surface-variant font-bold">EQUIPO</th>
                <th class="px-6 py-3 font-label-md text-label-md text-on-surface-variant font-bold">ESTADO</th>
                <th class="px-6 py-3 font-label-md text-label-md text-on-surface-variant font-bold">GERENCIA</th>
                <th class="px-6 py-3 font-label-md text-label-md text-on-surface-variant font-bold">ACCIÓN</th>
              </tr>
            </thead>
            <tbody class="font-table-data text-table-data">
              <tr
                v-for="alerta in docsStore.alertasTI"
                :key="alerta.id"
                class="border-b border-outline-variant hover:bg-surface-container-low transition-colors h-[40px]"
              >
                <td class="px-6 py-3 font-semibold">{{ alerta.equipo }}</td>
                <td class="px-6 py-3">
                  <span
                    :class="[
                      alerta.color === 'error' ? 'bg-error-container text-on-error-container' :
                      alerta.color === 'tertiary' ? 'bg-tertiary-fixed text-on-tertiary-fixed' :
                      'bg-secondary-container text-on-secondary-container',
                      'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase'
                    ]"
                  >
                    {{ alerta.estado }}
                  </span>
                </td>
                <td class="px-6 py-3 text-on-surface-variant">{{ alerta.gerencia }}</td>
                <td class="px-6 py-3 text-primary font-bold cursor-pointer hover:underline">{{ alerta.accion }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Actionable Alerts -->
    <div class="bg-surface-container-highest/20 border-l-4 border-secondary p-4 rounded-r-xl flex items-center justify-between shadow-sm">
      <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-secondary/10 rounded-full flex items-center justify-center text-secondary shrink-0">
          <span class="material-symbols-outlined">priority_high</span>
        </div>
        <div>
          <h5 class="font-bold text-on-surface text-body-md">Sugerencia Estratégica</h5>
          <p class="text-on-surface-variant text-body-sm leading-normal">Se recomienda priorizar el mantenimiento preventivo en la Gerencia de Rentas para evitar retrasos en la recaudación trimestral.</p>
        </div>
      </div>
      <button class="px-4 py-2 bg-inverse-surface text-white rounded-lg text-body-sm font-bold hover:bg-on-surface transition-all cursor-pointer whitespace-nowrap">Ver Detalle</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useDocumentosStore } from '../stores/documentos';

const docsStore = useDocumentosStore();
const animateActive = ref(false);

onMounted(() => {
  setTimeout(() => {
    animateActive.value = true;
  }, 100);
});
</script>
