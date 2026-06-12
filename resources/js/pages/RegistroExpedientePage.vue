<template>
  <div class="max-w-5xl mx-auto space-y-6">
    <!-- Form Card Wrapper -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
      <!-- Card Header -->
      <div class="bg-surface-container px-8 py-6 border-b border-outline-variant">
        <div class="flex justify-between items-center">
          <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Registro de Nuevo Expediente</h2>
            <p class="font-label-md text-label-md text-primary mt-1 flex items-center gap-1">
              <span class="material-symbols-outlined text-[16px]">fingerprint</span>
              Nro. Auto-asignado: EXP-2026-{{ mockRandomNumber }}
            </p>
          </div>
          <div class="bg-primary/10 text-primary px-3 py-1 rounded-full font-label-md text-label-md font-semibold">
            MOD-DOC
          </div>
        </div>
      </div>

      <!-- Form Content -->
      <form class="p-8 space-y-8" @submit.prevent="handleSubmit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
          <!-- Left Column -->
          <div class="space-y-6">
            <!-- Tipo Documental -->
            <div class="space-y-2">
              <label class="block font-label-md text-label-md text-on-surface font-semibold" for="doc-type">
                Tipo Documental <span class="text-error font-bold">*</span>
              </label>
              <div class="relative">
                <select
                  v-model="tipoDocumental"
                  class="w-full appearance-none bg-surface border border-outline-variant rounded-lg px-4 py-3 font-body-md text-body-md form-input-focus transition-all text-on-surface"
                  id="doc-type"
                  required
                >
                  <option value="" disabled>Seleccione el tipo de documento</option>
                  <option value="Oficio">Oficio</option>
                  <option value="Memorándum">Memorándum</option>
                  <option value="Informe Técnico">Informe Técnico</option>
                  <option value="Solicitud Ciudadana">Solicitud Ciudadana</option>
                  <option value="Resolución">Resolución de Alcaldía</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
              </div>
            </div>

            <!-- Unidad Origen -->
            <div class="space-y-2">
              <label class="block font-label-md text-label-md text-on-surface font-semibold" for="origin-unit">
                Unidad de Origen <span class="text-error font-bold">*</span>
              </label>
              <div class="relative">
                <select
                  v-model="unidadOrigen"
                  class="w-full appearance-none bg-surface border border-outline-variant rounded-lg px-4 py-3 font-body-md text-body-md form-input-focus transition-all text-on-surface"
                  id="origin-unit"
                  required
                >
                  <option value="" disabled>Seleccione la unidad remitente</option>
                  <option value="Gerencia de Administración">Gerencia de Administración</option>
                  <option value="Gerencia de Infraestructura">Gerencia de Infraestructura</option>
                  <option value="Oficina de Presupuesto">Oficina de Presupuesto</option>
                  <option value="Asesoría Jurídica">Asesoría Jurídica</option>
                  <option value="Mesa de Partes (Externo)">Mesa de Partes (Externo)</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
              </div>
            </div>

            <!-- Prioridad -->
            <div class="space-y-3">
              <span class="block font-label-md text-label-md text-on-surface font-semibold">Prioridad de Atención <span class="text-error font-bold">*</span></span>
              <div class="flex flex-wrap gap-4">
                <label class="flex items-center gap-2 cursor-pointer group">
                  <input v-model="prioridad" class="w-5 h-5 text-primary focus:ring-primary border-outline-variant" name="priority" type="radio" value="baja" />
                  <span class="font-body-md text-body-md text-on-surface-variant group-hover:text-on-surface">Baja</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                  <input v-model="prioridad" class="w-5 h-5 text-primary focus:ring-primary border-outline-variant" name="priority" type="radio" value="media" />
                  <span class="font-body-md text-body-md text-on-surface-variant group-hover:text-on-surface">Normal</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                  <input v-model="prioridad" class="w-5 h-5 text-error focus:ring-error border-outline-variant" name="priority" type="radio" value="alta" />
                  <span class="font-body-md text-body-md text-on-surface-variant group-hover:text-on-surface text-error font-bold">Urgente</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="space-y-6">
            <!-- Asunto -->
            <div class="space-y-2">
              <label class="block font-label-md text-label-md text-on-surface font-semibold" for="subject">
                Asunto / Referencia <span class="text-error font-bold">*</span>
              </label>
              <textarea
                v-model="asunto"
                class="w-full bg-surface border border-outline-variant rounded-lg px-4 py-3 font-body-md text-body-md form-input-focus transition-all resize-none text-on-surface"
                id="subject"
                placeholder="Describa brevemente el motivo del expediente..."
                rows="4"
                required
              ></textarea>
            </div>

            <!-- Details/Details Description (custom field to match design system density) -->
            <div class="space-y-2">
              <label class="block font-label-md text-label-md text-on-surface font-semibold" for="details">
                Detalles Adicionales
              </label>
              <input
                v-model="detalles"
                type="text"
                class="w-full bg-surface border border-outline-variant rounded-lg px-4 py-2 text-body-sm form-input-focus transition-all text-on-surface"
                id="details"
                placeholder="Notas u observaciones del expediente..."
              />
            </div>

            <!-- Attachment -->
            <div class="space-y-2">
              <span class="block font-label-md text-label-md text-on-surface font-semibold">Documento Adjunto (PDF/Digitalizado)</span>
              <div class="relative group">
                <input
                  class="hidden"
                  id="attachment"
                  type="file"
                  accept=".pdf"
                  @change="handleFileChange"
                />
                <label
                  :class="[
                    fileName ? 'bg-primary/5 border-primary' : 'bg-surface-container-low hover:bg-surface-container-high hover:border-primary border-outline-variant',
                    'flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-xl transition-all cursor-pointer'
                  ]"
                  for="attachment"
                >
                  <span class="material-symbols-outlined text-[32px] text-on-surface-variant mb-2">cloud_upload</span>
                  <span class="font-label-md text-label-md text-on-surface font-bold">
                    {{ fileName ? fileName : 'Haga clic para subir o arrastre' }}
                  </span>
                  <span class="font-body-sm text-body-sm text-on-surface-variant mt-1">Soporta PDF hasta 10MB</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="pt-8 border-t border-outline-variant flex flex-col md:flex-row justify-end items-center gap-4">
          <button
            class="w-full md:w-auto px-8 py-3 font-label-md text-label-md text-on-surface-variant hover:bg-surface-container-high transition-all rounded-lg active:scale-95 cursor-pointer font-bold"
            type="button"
            @click="cancelar"
          >
            Cancelar
          </button>
          <button
            :disabled="submitting"
            :class="[
              isSuccess ? 'bg-green-600 hover:bg-green-700' : 'bg-primary hover:bg-primary-container',
              'w-full md:w-auto text-on-primary px-10 py-3 rounded-lg font-label-md text-label-md transition-all active:scale-95 flex items-center justify-center gap-2 font-bold cursor-pointer disabled:opacity-75'
            ]"
            type="submit"
          >
            <span class="material-symbols-outlined" :class="{ 'animate-spin': submitting && !isSuccess }">
              {{ isSuccess ? 'check_circle' : submitting ? 'sync' : 'save' }}
            </span>
            <span>{{ isSuccess ? 'Registrado con éxito' : submitting ? 'Procesando...' : 'Registrar Expediente' }}</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Informational Section (Bento Grid Style) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
      <div class="bg-surface-container-low p-6 rounded-xl border border-outline-variant flex items-start gap-4">
        <div class="p-3 bg-secondary-container rounded-lg text-on-secondary-container shrink-0">
          <span class="material-symbols-outlined">timer</span>
        </div>
        <div>
          <h3 class="font-label-md text-label-md text-on-surface font-bold">Plazo Estimado</h3>
          <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 leading-normal">El tiempo promedio de respuesta para este tipo documental es de 72 horas hábiles.</p>
        </div>
      </div>
      <div class="bg-surface-container-low p-6 rounded-xl border border-outline-variant flex items-start gap-4">
        <div class="p-3 bg-tertiary-container rounded-lg text-on-tertiary-container shrink-0">
          <span class="material-symbols-outlined">history_edu</span>
        </div>
        <div>
          <h3 class="font-label-md text-label-md text-on-surface font-bold">Trazabilidad</h3>
          <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 leading-normal">Cada paso del expediente será registrado y notificado a la unidad de origen automáticamente.</p>
        </div>
      </div>
      <div class="bg-surface-container-low p-6 rounded-xl border border-outline-variant flex items-start gap-4">
        <div class="p-3 bg-primary-container rounded-lg text-on-primary-container shrink-0">
          <span class="material-symbols-outlined">security</span>
        </div>
        <div>
          <h3 class="font-label-md text-label-md text-on-surface font-bold">Confidencialidad</h3>
          <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 leading-normal">Los documentos adjuntos están protegidos bajo protocolos de seguridad institucional.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useDocumentosStore } from '../stores/documentos';

const docsStore = useDocumentosStore();
const router = useRouter();

const mockRandomNumber = ref(Math.floor(1000 + Math.random() * 9000));

// Form values
const tipoDocumental = ref('');
const unidadOrigen = ref('');
const prioridad = ref('media');
const asunto = ref('');
const detalles = ref('');
const fileName = ref('');

// Submit status
const submitting = ref(false);
const isSuccess = ref(false);

function handleFileChange(e) {
  if (e.target.files.length > 0) {
    fileName.value = e.target.files[0].name;
  }
}

function cancelar() {
  if (confirm('¿Desea cancelar el registro? Se perderán los datos introducidos.')) {
    router.push({ name: 'bandeja-pendientes' });
  }
}

function handleSubmit() {
  submitting.value = true;
  
  setTimeout(() => {
    const generatedId = docsStore.registrarExpediente({
      tipo: tipoDocumental.value,
      unidad_origen: unidadOrigen.value,
      prioridad: prioridad.value,
      asunto: asunto.value,
      detalles: detalles.value
    });

    isSuccess.value = true;
    
    setTimeout(() => {
      submitting.value = false;
      isSuccess.value = false;
      // Redirect to the newly created expediente traceability page!
      router.push({ name: 'trazabilidad-expediente', params: { id: generatedId } });
    }, 1500);
  }, 1200);
}
</script>
