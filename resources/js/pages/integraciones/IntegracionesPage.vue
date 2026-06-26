<template>
  <div class="max-w-6xl mx-auto space-y-8">
    <header class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
      <div>
        <h1 class="text-headline-lg font-bold text-primary">Integraciones SIGA / SIAF</h1>
        <p class="text-body-md text-on-surface-variant mt-2">
          Sincronización de patrimonio, organigrama y ejecución presupuestal (modo simulador en desarrollo).
        </p>
      </div>
      <span
        v-if="estado?.siga?.es_simulacion || estado?.siaf?.es_simulacion"
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-tertiary-container text-on-tertiary-container text-label-md font-semibold"
      >
        <span class="material-symbols-outlined text-[18px]">science</span>
        Datos simulados — PA-19
      </span>
    </header>

    <p v-if="error" class="text-body-sm text-error bg-error-container/30 border border-error/30 rounded-lg px-4 py-3">
      {{ error }}
    </p>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <section class="bg-surface border border-outline-variant rounded-xl p-6 space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-title-md font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">inventory_2</span>
            SIGA — Patrimonio TI
          </h2>
          <span class="text-label-md text-on-surface-variant uppercase">{{ estado?.siga?.driver ?? '…' }}</span>
        </div>
        <p class="text-body-sm text-on-surface-variant">
          Importa bienes informáticos municipales. Omite categorías no TI.
        </p>
        <div v-if="ultimoSiga('patrimonio')" class="text-body-sm bg-surface-container-low rounded-lg p-3">
          <p>
            Última sync:
            <strong>{{ formatFecha(ultimoSiga('patrimonio').ejecutado_at) }}</strong>
            — {{ ultimoSiga('patrimonio').registros_ok }} ok,
            {{ ultimoSiga('patrimonio').registros_error }} errores
          </p>
        </div>
        <button
          type="button"
          class="btn-primary w-full md:w-auto"
          :disabled="syncing === 'siga-patrimonio'"
          @click="syncPatrimonio"
        >
          {{ syncing === 'siga-patrimonio' ? 'Sincronizando…' : 'Sincronizar patrimonio' }}
        </button>
      </section>

      <section class="bg-surface border border-outline-variant rounded-xl p-6 space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-title-md font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">account_tree</span>
            SIGA — Organigrama y personal
          </h2>
          <span class="text-label-md text-on-surface-variant uppercase">{{ estado?.siga?.driver ?? '…' }}</span>
        </div>
        <p class="text-body-sm text-on-surface-variant">
          Actualiza unidades y referencias de personal activo (sin comités de derivación).
        </p>
        <div v-if="ultimoSiga('organigrama')" class="text-body-sm bg-surface-container-low rounded-lg p-3">
          <p>
            Última sync organigrama:
            <strong>{{ formatFecha(ultimoSiga('organigrama').ejecutado_at) }}</strong>
          </p>
        </div>
        <button
          type="button"
          class="btn-primary w-full md:w-auto"
          :disabled="syncing === 'siga-organigrama'"
          @click="syncOrganigrama"
        >
          {{ syncing === 'siga-organigrama' ? 'Sincronizando…' : 'Sincronizar organigrama' }}
        </button>
      </section>

      <section class="bg-surface border border-outline-variant rounded-xl p-6 space-y-4 lg:col-span-2">
        <div class="flex items-center justify-between">
          <h2 class="text-title-md font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">payments</span>
            SIAF — Ejecución presupuestal
          </h2>
          <span class="text-label-md text-on-surface-variant uppercase">{{ estado?.siaf?.driver ?? '…' }}</span>
        </div>
        <p class="text-body-sm text-on-surface-variant">
          Solo lectura. Alimenta el dashboard financiero restringido (Presupuesto / Tesorería / Contabilidad).
        </p>
        <div v-if="ultimoSiaf()" class="text-body-sm bg-surface-container-low rounded-lg p-3">
          <p>
            Última sync:
            <strong>{{ formatFecha(ultimoSiaf().ejecutado_at) }}</strong>
            — {{ ultimoSiaf().mensaje }}
          </p>
        </div>
        <button
          type="button"
          class="btn-primary w-full md:w-auto"
          :disabled="syncing === 'siaf'"
          @click="syncSiaf"
        >
          {{ syncing === 'siaf' ? 'Sincronizando…' : 'Sincronizar SIAF' }}
        </button>
      </section>
    </div>

    <section class="bg-surface border border-outline-variant rounded-xl overflow-hidden">
      <div class="px-6 py-4 border-b border-outline-variant">
        <h2 class="text-title-md font-semibold">Historial de sincronizaciones</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-body-sm">
          <thead class="bg-surface-container-low text-label-md text-on-surface-variant">
            <tr>
              <th class="text-left px-4 py-3">Fecha</th>
              <th class="text-left px-4 py-3">Sistema</th>
              <th class="text-left px-4 py-3">Tipo</th>
              <th class="text-left px-4 py-3">Modo</th>
              <th class="text-left px-4 py-3">Estado</th>
              <th class="text-right px-4 py-3">OK</th>
              <th class="text-right px-4 py-3">Err</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="log in logs" :key="log.id" class="border-t border-outline-variant/50">
              <td class="px-4 py-3 whitespace-nowrap">{{ formatFecha(log.ejecutado_at) }}</td>
              <td class="px-4 py-3 uppercase">{{ log.sistema }}</td>
              <td class="px-4 py-3">{{ log.tipo_sync }}</td>
              <td class="px-4 py-3">{{ log.modo }}</td>
              <td class="px-4 py-3">
                <span :class="estadoClass(log.estado)">{{ log.estado }}</span>
              </td>
              <td class="px-4 py-3 text-right">{{ log.registros_ok }}</td>
              <td class="px-4 py-3 text-right">{{ log.registros_error }}</td>
            </tr>
            <tr v-if="!logs.length">
              <td colspan="7" class="px-4 py-8 text-center text-on-surface-variant">
                Sin sincronizaciones registradas. Ejecute una sync manual.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useIntegracionesStore } from '../../stores/integraciones';

const store = useIntegracionesStore();
const { estado, logs, syncing, error } = storeToRefs(store);

function ultimoSiga(tipo) {
  return estado.value?.siga?.ultimos_sync?.[tipo] ?? null;
}

function ultimoSiaf() {
  return estado.value?.siaf?.ultimos_sync?.ejecucion ?? null;
}

function formatFecha(iso) {
  if (!iso) return '—';
  return new Date(iso).toLocaleString('es-PE', { dateStyle: 'short', timeStyle: 'short' });
}

function estadoClass(estado) {
  if (estado === 'ok') return 'text-primary font-medium';
  if (estado === 'parcial') return 'text-secondary font-medium';
  return 'text-error font-medium';
}

async function syncPatrimonio() {
  await store.syncSigaPatrimonio();
}

async function syncOrganigrama() {
  await store.syncSigaOrganigrama();
}

async function syncSiaf() {
  await store.syncSiafEjecucion();
}

onMounted(async () => {
  await Promise.all([store.cargarEstado(), store.cargarLogs()]);
});
</script>
