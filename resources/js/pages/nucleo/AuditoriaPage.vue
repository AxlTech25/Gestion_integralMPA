<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-headline-lg font-bold text-primary">Auditoría del sistema</h1>
        <p class="text-body-sm text-on-surface-variant">Consulta de solo lectura para control interno (OCI).</p>
      </div>
      <a
        href="/api/auditoria/export"
        class="text-label-md text-primary underline"
        target="_blank"
        rel="noopener"
      >
        Exportar CSV
      </a>
    </div>

    <div class="flex flex-wrap gap-3">
      <input v-model="filtros.modulo" placeholder="Módulo" class="border rounded-lg px-3 py-2 text-body-sm" />
      <input v-model="filtros.accion" placeholder="Acción" class="border rounded-lg px-3 py-2 text-body-sm" />
      <button type="button" class="px-4 py-2 bg-primary text-on-primary rounded-lg text-label-md" @click="loadLogs">
        Filtrar
      </button>
    </div>

    <div class="bg-surface border border-outline-variant rounded-xl overflow-x-auto">
      <table class="w-full text-left text-body-sm min-w-[640px]">
        <thead class="bg-surface-container-low border-b">
          <tr>
            <th class="px-4 py-3">Fecha</th>
            <th class="px-4 py-3">Usuario</th>
            <th class="px-4 py-3">Módulo</th>
            <th class="px-4 py-3">Acción</th>
            <th class="px-4 py-3">Entidad</th>
            <th class="px-4 py-3">IP</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="log in logs" :key="log.id" class="border-b border-outline-variant/40">
            <td class="px-4 py-2 whitespace-nowrap">{{ formatDate(log.created_at) }}</td>
            <td class="px-4 py-2">{{ log.usuario?.username ?? '—' }}</td>
            <td class="px-4 py-2">{{ log.modulo }}</td>
            <td class="px-4 py-2">{{ log.accion }}</td>
            <td class="px-4 py-2">{{ log.entidad }} #{{ log.entidad_id ?? '—' }}</td>
            <td class="px-4 py-2 font-mono text-xs">{{ log.ip_address }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const logs = ref([]);
const filtros = ref({ modulo: '', accion: '' });

function formatDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleString('es-PE');
}

async function loadLogs() {
    const { data } = await axios.get('/api/auditoria', {
        params: {
            modulo: filtros.value.modulo || undefined,
            accion: filtros.value.accion || undefined,
        },
    });
    logs.value = data.data ?? data;
}

onMounted(loadLogs);
</script>
