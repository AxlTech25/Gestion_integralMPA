<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:justify-between gap-4">
      <div>
        <h1 class="text-headline-lg font-bold">No conformidades</h1>
        <p class="text-body-sm text-on-surface-variant">Registro ISO 10.2 — F-ISO-01 / F-ISO-02.</p>
      </div>
      <button
        v-if="puedeReportar"
        type="button"
        class="bg-primary text-on-primary px-4 py-2 rounded-lg font-semibold"
        @click="modalOpen = true"
      >
        Nueva NC
      </button>
    </div>

    <div class="flex gap-2">
      <button
        type="button"
        :class="filtro === 'abiertas' ? 'bg-primary text-on-primary' : 'bg-surface-container'"
        class="px-3 py-1.5 rounded-lg text-label-md font-semibold"
        @click="cambiarFiltro('abiertas')"
      >
        Abiertas
      </button>
      <button
        type="button"
        :class="filtro === 'todas' ? 'bg-primary text-on-primary' : 'bg-surface-container'"
        class="px-3 py-1.5 rounded-lg text-label-md font-semibold"
        @click="cambiarFiltro('todas')"
      >
        Todas
      </button>
    </div>

    <div v-if="store.loading" class="text-on-surface-variant">Cargando...</div>
    <div v-else class="space-y-3">
      <router-link
        v-for="nc in store.noConformidades"
        :key="nc.id"
        :to="{ name: 'calidad-nc-detalle', params: { id: nc.id } }"
        class="block bg-surface border border-outline-variant rounded-xl p-4 hover:border-primary transition-colors"
      >
        <div class="flex flex-wrap items-center gap-2">
          <span class="font-mono text-primary font-semibold">{{ nc.codigo }}</span>
          <span class="px-2 py-0.5 rounded-full text-xs font-bold uppercase bg-surface-container">{{ nc.estado }}</span>
          <span class="text-label-md text-on-surface-variant">{{ procesoLabel(nc.proceso) }}</span>
          <span
            :class="severidadClass(nc.severidad)"
            class="px-2 py-0.5 rounded-full text-xs font-bold uppercase"
          >
            {{ nc.severidad }}
          </span>
        </div>
        <p class="text-body-sm text-on-surface mt-2 line-clamp-2">{{ nc.descripcion }}</p>
        <p class="text-body-sm text-on-surface-variant mt-1">
          {{ nc.unidad ?? '—' }} · {{ nc.reportado_por }}
          <span v-if="nc.requiere_ac"> · requiere AC</span>
        </p>
      </router-link>
      <p v-if="!store.noConformidades.length" class="text-center text-on-surface-variant py-8">
        No hay registros para mostrar.
      </p>
    </div>

    <div v-if="modalOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="modalOpen = false">
      <div class="bg-surface rounded-xl p-6 w-full max-w-lg space-y-3 max-h-[90vh] overflow-y-auto">
        <h2 class="font-semibold text-headline-sm">Registrar no conformidad</h2>
        <select v-model="nueva.proceso" class="w-full border rounded-lg px-3 py-2">
          <option value="documentaria">Documentaria (S.01)</option>
          <option value="patrimonio_ti">Patrimonio / TI (S.05-S.06)</option>
          <option value="nucleo">Seguridad / Núcleo</option>
          <option value="indicadores">Indicadores / Dashboard</option>
          <option value="otro">Otro</option>
        </select>
        <select v-model="nueva.severidad" class="w-full border rounded-lg px-3 py-2">
          <option value="leve">Leve</option>
          <option value="moderada">Moderada</option>
          <option value="grave">Grave</option>
        </select>
        <textarea v-model="nueva.descripcion" rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="Descripción de la NC (mín. 10 caracteres)" />
        <input v-model="nueva.requisito_incumplido" type="text" class="w-full border rounded-lg px-3 py-2" placeholder="Requisito incumplido (opcional)" />
        <textarea v-model="nueva.contencion" rows="2" class="w-full border rounded-lg px-3 py-2" placeholder="Contención inmediata (opcional)" />
        <p v-if="errorForm" class="text-error text-body-sm">{{ errorForm }}</p>
        <button
          type="button"
          class="w-full bg-primary text-on-primary py-2 rounded-lg font-semibold disabled:opacity-50"
          :disabled="nueva.descripcion.length < 10"
          @click="crearNc"
        >
          Registrar NC
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useCalidadStore } from '../../stores/calidad';
import { useAuthStore } from '../../stores/auth';

const store = useCalidadStore();
const auth = useAuthStore();
const puedeReportar = computed(() => auth.can('calidad.nc.reportar') || auth.can('calidad.nc.gestionar'));
const modalOpen = ref(false);
const filtro = ref('abiertas');
const errorForm = ref('');
const nueva = ref({
    proceso: 'documentaria',
    severidad: 'moderada',
    descripcion: '',
    requisito_incumplido: '',
    contencion: '',
});

const procesoLabels = {
    documentaria: 'Documentaria',
    patrimonio_ti: 'Patrimonio TI',
    nucleo: 'Núcleo',
    indicadores: 'Indicadores',
    otro: 'Otro',
};

onMounted(() => cargarLista());

function procesoLabel(p) {
    return procesoLabels[p] ?? p;
}

function severidadClass(s) {
    if (s === 'grave') return 'bg-error-container text-on-error-container';
    if (s === 'moderada') return 'bg-secondary-container text-on-secondary-container';
    return 'bg-surface-container';
}

async function cargarLista() {
    const params = filtro.value === 'abiertas' ? { solo_abiertas: true } : {};
    await store.cargarNoConformidades(params);
}

async function cambiarFiltro(nuevo) {
    filtro.value = nuevo;
    await cargarLista();
}

async function crearNc() {
    errorForm.value = '';
    try {
        const nc = await store.reportarNc(nueva.value);
        modalOpen.value = false;
        nueva.value = { proceso: 'documentaria', severidad: 'moderada', descripcion: '', requisito_incumplido: '', contencion: '' };
        await cargarLista();
    } catch (e) {
        errorForm.value = e.response?.data?.message ?? 'No se pudo registrar la NC.';
    }
}
</script>
