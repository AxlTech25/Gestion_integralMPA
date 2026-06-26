<template>
  <div v-if="store.loading && !nc" class="text-on-surface-variant">Cargando...</div>
  <div v-else-if="nc" class="space-y-6 max-w-4xl">
    <router-link :to="{ name: 'calidad-no-conformidades' }" class="text-primary text-label-md font-semibold hover:underline">
      ← Volver al listado
    </router-link>

    <header class="flex flex-wrap items-start justify-between gap-4">
      <div>
        <h1 class="text-headline-lg font-bold font-mono text-primary">{{ nc.codigo }}</h1>
        <p class="text-body-sm text-on-surface-variant mt-1">
          {{ procesoLabel(nc.proceso) }} · {{ nc.unidad ?? '—' }} · reportado por {{ nc.reportado_por }}
        </p>
      </div>
      <span class="px-3 py-1 rounded-full text-sm font-bold uppercase bg-surface-container">{{ nc.estado }}</span>
    </header>

    <section class="bg-surface border border-outline-variant rounded-xl p-5 space-y-3">
      <h2 class="font-semibold">Descripción</h2>
      <p class="text-body-sm">{{ nc.descripcion }}</p>
      <p v-if="nc.requisito_incumplido" class="text-body-sm">
        <span class="font-semibold">Requisito:</span> {{ nc.requisito_incumplido }}
      </p>
      <p v-if="nc.contencion" class="text-body-sm bg-surface-container-low rounded-lg p-3">
        <span class="font-semibold">Contención:</span> {{ nc.contencion }}
      </p>
      <p v-if="nc.causa_raiz" class="text-body-sm">
        <span class="font-semibold">Causa raíz:</span> {{ nc.causa_raiz }}
      </p>
      <p v-if="nc.expediente_codigo" class="text-body-sm">
        Expediente vinculado:
        <router-link :to="{ name: 'trazabilidad-expediente', params: { id: nc.expediente_codigo } }" class="text-primary font-semibold">
          {{ nc.expediente_codigo }}
        </router-link>
      </p>
    </section>

    <section v-if="nc.acciones_correctivas?.length" class="space-y-3">
      <h2 class="text-headline-sm font-semibold">Acciones correctivas</h2>
      <article
        v-for="ac in nc.acciones_correctivas"
        :key="ac.id"
        class="bg-surface border border-outline-variant rounded-xl p-4 space-y-2"
      >
        <div class="flex flex-wrap gap-2 items-center">
          <span class="font-mono font-semibold text-secondary">{{ ac.codigo }}</span>
          <span class="text-xs uppercase font-bold px-2 py-0.5 bg-surface-container rounded-full">{{ ac.estado }}</span>
        </div>
        <p class="text-body-sm whitespace-pre-wrap">{{ ac.plan_acciones }}</p>
        <p v-if="ac.evidencia_implementacion" class="text-body-sm text-on-surface-variant">
          Evidencia: {{ ac.evidencia_implementacion }}
        </p>
        <div v-if="esGestor && ac.estado !== 'cerrada' && ac.estado !== 'ineficaz'" class="flex flex-wrap gap-2 pt-2">
          <button
            v-if="ac.estado === 'abierta'"
            type="button"
            class="text-label-md text-secondary font-semibold"
            @click="cambiarAc(ac.id, { estado: 'en_implementacion' })"
          >
            En implementación
          </button>
          <button
            v-if="ac.estado === 'en_implementacion'"
            type="button"
            class="text-label-md text-primary font-semibold"
            @click="cerrarAc(ac.id)"
          >
            Verificar y cerrar AC
          </button>
        </div>
      </article>
    </section>

    <section v-if="esGestor && nc.estado !== 'cerrada'" class="bg-surface-container-low rounded-xl p-5 space-y-4">
      <h2 class="font-semibold">Gestión OCI / calidad</h2>
      <button
        v-if="!nc.acciones_correctivas?.length"
        type="button"
        class="bg-secondary text-on-secondary px-4 py-2 rounded-lg font-semibold"
        @click="modalAc = true"
      >
        Crear acción correctiva
      </button>
      <button
        v-if="puedeCerrarNc"
        type="button"
        class="bg-primary text-on-primary px-4 py-2 rounded-lg font-semibold ml-0 sm:ml-2"
        @click="modalCerrar = true"
      >
        Cerrar no conformidad
      </button>
    </section>

    <section v-if="nc.estado === 'cerrada'" class="bg-primary-container/30 rounded-xl p-5">
      <h2 class="font-semibold">Cierre verificado</h2>
      <p class="text-body-sm mt-2">{{ nc.verificacion_eficacia }}</p>
      <p class="text-body-sm text-on-surface-variant mt-1">
        {{ nc.verificada_por }} · {{ formatFecha(nc.verificada_at) }}
      </p>
    </section>

    <div v-if="modalAc" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="modalAc = false">
      <div class="bg-surface rounded-xl p-6 w-full max-w-md space-y-3">
        <h3 class="font-semibold">Nueva acción correctiva</h3>
        <textarea v-model="nuevaAc.causa_raiz" rows="2" class="w-full border rounded-lg px-3 py-2" placeholder="Causa raíz" />
        <textarea v-model="nuevaAc.plan_acciones" rows="4" class="w-full border rounded-lg px-3 py-2" placeholder="Plan de acciones (mín. 10 caracteres)" />
        <button
          type="button"
          class="w-full bg-primary text-on-primary py-2 rounded-lg font-semibold disabled:opacity-50"
          :disabled="nuevaAc.plan_acciones.length < 10"
          @click="crearAc"
        >
          Crear AC
        </button>
      </div>
    </div>

    <div v-if="modalCerrar" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="modalCerrar = false">
      <div class="bg-surface rounded-xl p-6 w-full max-w-md space-y-3">
        <h3 class="font-semibold">Cerrar {{ nc.codigo }}</h3>
        <textarea v-model="cierre.verificacion_eficacia" rows="4" class="w-full border rounded-lg px-3 py-2" placeholder="Verificación de eficacia (obligatorio)" />
        <button
          type="button"
          class="w-full bg-primary text-on-primary py-2 rounded-lg font-semibold disabled:opacity-50"
          :disabled="cierre.verificacion_eficacia.length < 10"
          @click="cerrarNc"
        >
          Confirmar cierre
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useCalidadStore } from '../../stores/calidad';
import { useAuthStore } from '../../stores/auth';

const route = useRoute();
const store = useCalidadStore();
const auth = useAuthStore();
const esGestor = computed(() => auth.can('calidad.nc.gestionar'));
const nc = computed(() => store.ncActual);
const modalAc = ref(false);
const modalCerrar = ref(false);
const nuevaAc = ref({ causa_raiz: '', plan_acciones: '' });
const cierre = ref({ verificacion_eficacia: '' });

const procesoLabels = {
    documentaria: 'Documentaria',
    patrimonio_ti: 'Patrimonio TI',
    nucleo: 'Núcleo',
    indicadores: 'Indicadores',
    otro: 'Otro',
};

const puedeCerrarNc = computed(() => {
    if (!nc.value || nc.value.estado === 'cerrada') return false;
    if (!nc.value.requiere_ac) return true;
    return nc.value.acciones_correctivas?.every((ac) => ac.estado === 'cerrada');
});

onMounted(() => cargar());
watch(() => route.params.id, cargar);

async function cargar() {
    await store.cargarNc(route.params.id);
}

function procesoLabel(p) {
    return procesoLabels[p] ?? p;
}

function formatFecha(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleString('es-PE');
}

async function crearAc() {
    await store.crearAc(nc.value.id, nuevaAc.value);
    modalAc.value = false;
    nuevaAc.value = { causa_raiz: '', plan_acciones: '' };
}

async function cambiarAc(id, payload) {
    await store.actualizarAc(id, payload);
}

async function cerrarAc(id) {
    const evidencia = prompt('Evidencia de implementación:');
    const metodo = prompt('Método de verificación:');
    const resultado = prompt('Resultado (eficaz / ineficaz):', 'eficaz');
    if (!resultado) return;
    await store.actualizarAc(id, {
        estado: 'cerrada',
        evidencia_implementacion: evidencia?.trim() || undefined,
        metodo_verificacion: metodo?.trim() || undefined,
        resultado_verificacion: resultado === 'ineficaz' ? 'ineficaz' : 'eficaz',
    });
}

async function cerrarNc() {
    await store.cerrarNc(nc.value.id, cierre.value);
    modalCerrar.value = false;
}
</script>
