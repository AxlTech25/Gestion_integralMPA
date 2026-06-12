<template>
  <div class="max-w-5xl mx-auto">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
      <div class="bg-surface-container px-8 py-6 border-b border-outline-variant">
        <div class="flex justify-between items-center gap-4">
          <div>
            <h1 class="text-headline-lg text-on-surface font-semibold">Registro de Nuevo Expediente</h1>
            <p class="text-label-md text-primary mt-1 flex items-center gap-1 font-semibold">
              <span class="material-symbols-outlined text-[16px]">fingerprint</span>
              Nro. Auto-asignado: {{ previewId }}
            </p>
          </div>
          <div class="bg-primary/10 text-primary px-3 py-1 rounded-full text-label-md font-semibold shrink-0">
            MOD-DOC
          </div>
        </div>
      </div>

      <form class="p-8 space-y-8" @submit.prevent="submit">
        <p
          v-if="formError"
          class="text-body-sm text-on-error-container bg-error-container border border-error/20 rounded-lg px-4 py-3 font-medium"
        >
          {{ formError }}
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
          <div class="space-y-6">
            <FormField label="Tipo Documental" required>
              <div class="relative">
                <select
                  id="doc-type"
                  v-model="form.tipo_documental_id"
                  required
                  class="w-full appearance-none bg-surface border border-outline-variant rounded-lg px-4 py-3 text-body-md form-input-focus transition-all text-on-surface"
                  @change="actualizarPreview"
                >
                  <option value="" disabled>Seleccione el tipo de documento</option>
                  <option v-for="tipo in docsStore.tiposDocumentales" :key="tipo.id" :value="tipo.id">
                    {{ tipo.nombre }}
                  </option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">
                  expand_more
                </span>
              </div>
            </FormField>

            <div class="space-y-3">
              <span class="block text-label-md text-on-surface font-semibold">
                Prioridad de Atención <span class="text-error font-bold">*</span>
              </span>
              <div class="flex flex-wrap gap-4">
                <label
                  v-for="opt in PRIORIDADES_REGISTRO"
                  :key="opt.value"
                  class="flex items-center gap-2 cursor-pointer group"
                >
                  <input
                    v-model="form.prioridad"
                    type="radio"
                    name="priority"
                    :value="opt.value"
                    :class="['w-5 h-5 border-outline-variant', opt.inputClass]"
                  />
                  <span class="text-body-md text-on-surface-variant group-hover:text-on-surface">
                    {{ opt.label }}
                  </span>
                </label>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <FormField label="Asunto / Referencia" required>
              <textarea
                id="subject"
                v-model="form.asunto"
                required
                rows="4"
                placeholder="Describa brevemente el motivo del expediente..."
                class="w-full bg-surface border border-outline-variant rounded-lg px-4 py-3 text-body-md form-input-focus transition-all resize-none text-on-surface"
              />
            </FormField>

            <FileDropzone ref="dropzoneRef" :max-mb="MAX_ARCHIVO_MB" @file-selected="onFileSelected" />
          </div>
        </div>

        <div class="pt-8 border-t border-outline-variant flex flex-col md:flex-row justify-end items-center gap-4">
          <button
            type="button"
            class="w-full md:w-auto px-8 py-3 text-label-md text-on-surface-variant hover:bg-surface-container-high transition-opacity duration-200 rounded-lg active:scale-95 cursor-pointer font-semibold"
            @click="cancelar"
          >
            Cancelar
          </button>
          <button
            type="submit"
            :disabled="submitting"
            :class="[
              submitSuccess ? 'bg-green-600 hover:bg-green-700' : 'bg-primary hover:bg-primary-container',
              'w-full md:w-auto text-on-primary px-10 py-3 rounded-lg text-label-md transition-colors shadow-sm active:scale-95 flex items-center justify-center gap-2 font-semibold cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed'
            ]"
          >
            <span class="material-symbols-outlined" :class="{ 'animate-spin': submitting && !submitSuccess }">
              {{ submitSuccess ? 'check_circle' : submitting ? 'sync' : 'save' }}
            </span>
            {{ submitLabel }}
          </button>
        </div>
      </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
      <InfoTipCard
        v-for="tip in INFO_REGISTRO_EXPEDIENTE"
        :key="tip.title"
        :icon="tip.icon"
        :title="tip.title"
        :text="tip.text"
        :icon-bg="tip.iconBg"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useDocumentosStore } from '../stores/documentos';
import {
    PRIORIDADES_REGISTRO,
    INFO_REGISTRO_EXPEDIENTE,
    MAX_ARCHIVO_MB,
    prioridadParaStore,
} from '../constants/documentos';
import FileDropzone from '../components/documentos/FileDropzone.vue';
import InfoTipCard from '../components/documentos/InfoTipCard.vue';
import FormField from '../components/documentos/FormField.vue';

const docsStore = useDocumentosStore();
const router = useRouter();
const dropzoneRef = ref(null);

const form = ref({
    tipo_documental_id: '',
    prioridad: 'media',
    asunto: '',
});
const archivo = ref(null);
const submitting = ref(false);
const submitSuccess = ref(false);
const formError = ref('');
const previewId = ref('—');

onMounted(async () => {
    await docsStore.cargarTiposDocumentales();
    if (docsStore.tiposDocumentales.length) {
        form.value.tipo_documental_id = docsStore.tiposDocumentales[0].id;
        await actualizarPreview();
    }
});

const submitLabel = computed(() => {
    if (submitSuccess.value) return 'Registrado con éxito';
    if (submitting.value) return 'Procesando...';
    return 'Registrar Expediente';
});

async function actualizarPreview() {
    if (!form.value.tipo_documental_id) return;
    previewId.value = await docsStore.previewCodigo(form.value.tipo_documental_id);
}

function onFileSelected(file) {
    archivo.value = file;
}

function cancelar() {
    if (!hasChanges() || confirm('¿Desea cancelar el registro? Se perderán los datos introducidos.')) {
        router.push({ name: 'bandeja-pendientes' });
    }
}

function hasChanges() {
    return form.value.tipo_documental_id || form.value.asunto || archivo.value;
}

async function submit() {
    formError.value = '';
    submitting.value = true;
    submitSuccess.value = false;

    try {
        const formData = new FormData();
        formData.append('tipo_documental_id', form.value.tipo_documental_id);
        formData.append('asunto', form.value.asunto.trim());
        formData.append('prioridad', prioridadParaStore(form.value.prioridad));
        if (archivo.value) {
            formData.append('archivo', archivo.value);
        }

        const codigo = await docsStore.registrarExpediente(formData);
        submitSuccess.value = true;
        await new Promise((r) => setTimeout(r, 800));
        router.push({ name: 'trazabilidad-expediente', params: { id: codigo } });
    } catch (e) {
        formError.value = e.response?.data?.message
            ?? e.response?.data?.errors?.tipo_documental_id?.[0]
            ?? 'No se pudo registrar el expediente.';
        submitting.value = false;
        submitSuccess.value = false;
    }
}
</script>
