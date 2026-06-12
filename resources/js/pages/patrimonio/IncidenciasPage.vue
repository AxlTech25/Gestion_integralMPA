<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:justify-between gap-4">
      <div>
        <h1 class="text-headline-lg font-bold">Incidencias de soporte</h1>
        <p class="text-body-sm text-on-surface-variant">Panel UTIS — fallas, averías y requerimientos.</p>
      </div>
      <button type="button" class="bg-primary text-on-primary px-4 py-2 rounded-lg font-semibold" @click="modalOpen = true">
        Nueva incidencia
      </button>
    </div>

    <div v-if="store.loading" class="text-on-surface-variant">Cargando...</div>
    <div v-else class="space-y-3">
      <article
        v-for="inc in store.incidencias"
        :key="inc.id"
        class="bg-surface border border-outline-variant rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
      >
        <div>
          <p class="font-mono text-primary font-semibold">{{ inc.codigo_patrimonial }}</p>
          <p class="text-body-sm text-on-surface">{{ inc.descripcion }}</p>
          <p class="text-body-sm text-on-surface-variant mt-1">{{ inc.unidad }} · {{ inc.reportado_por }}</p>
        </div>
        <div class="flex items-center gap-2">
          <span class="px-2 py-0.5 rounded-full text-xs font-bold uppercase bg-surface-container">{{ inc.estado }}</span>
          <button
            v-if="inc.estado === 'abierta'"
            type="button"
            class="text-label-md text-secondary font-semibold"
            @click="cambiarEstado(inc.id, 'en_atencion')"
          >
            Atender
          </button>
          <button
            v-if="inc.estado !== 'cerrada'"
            type="button"
            class="text-label-md text-primary font-semibold"
            @click="cerrar(inc.id)"
          >
            Cerrar
          </button>
        </div>
      </article>
      <p v-if="!store.incidencias.length" class="text-center text-on-surface-variant py-8">No hay incidencias abiertas.</p>
    </div>

    <div v-if="modalOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="modalOpen = false">
      <div class="bg-surface rounded-xl p-6 w-full max-w-md space-y-3">
        <h2 class="font-semibold text-headline-sm">Reportar incidencia</h2>
        <input v-model.number="nueva.equipo_id" type="number" placeholder="ID equipo" class="w-full border rounded-lg px-3 py-2" />
        <select v-model="nueva.tipo" class="w-full border rounded-lg px-3 py-2">
          <option value="falla">Falla</option>
          <option value="averia">Avería</option>
          <option value="requerimiento">Requerimiento</option>
        </select>
        <textarea v-model="nueva.descripcion" rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="Descripción" />
        <button type="button" class="w-full bg-primary text-on-primary py-2 rounded-lg font-semibold" @click="crearIncidencia">Registrar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePatrimonioStore } from '../../stores/patrimonio';

const store = usePatrimonioStore();
const modalOpen = ref(false);
const nueva = ref({ equipo_id: '', tipo: 'falla', descripcion: '' });

onMounted(() => store.cargarIncidencias({ solo_abiertas: true }));

async function cambiarEstado(id, estado) {
    await store.actualizarIncidencia(id, { estado });
}

async function cerrar(id) {
    const solucion = prompt('Solución aplicada:');
    if (!solucion?.trim()) return;
    await store.actualizarIncidencia(id, { estado: 'cerrada', solucion: solucion.trim(), estado_operativo_equipo: 'operativo' });
}

async function crearIncidencia() {
    await store.reportarIncidencia(nueva.value);
    modalOpen.value = false;
}
</script>
