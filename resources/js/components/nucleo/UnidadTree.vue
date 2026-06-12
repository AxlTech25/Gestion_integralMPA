<template>
  <ul class="space-y-1">
    <li v-for="node in nodes" :key="node.id">
      <div class="flex flex-wrap items-center gap-2 py-1.5">
        <span class="material-symbols-outlined text-on-surface-variant text-[18px]">
          {{ node.children?.length ? 'folder' : 'corporate_fare' }}
        </span>
        <span class="font-mono text-xs text-outline">{{ node.codigo_org }}</span>
        <span class="text-body-md">{{ node.nombre }}</span>
        <span class="text-xs px-2 py-0.5 rounded bg-surface-container-high text-on-surface-variant">{{ node.tipo }}</span>
        <span v-if="!node.activa" class="text-xs px-2 py-0.5 rounded bg-error-container text-on-error-container">inactiva</span>
        <span v-if="node.permite_derivacion" class="text-xs text-primary">derivación</span>
        <template v-if="editable && puedeEditar(node)">
          <button
            type="button"
            class="text-xs px-2 py-0.5 rounded border border-outline-variant hover:bg-surface-container-low"
            @click="toggleActiva(node)"
          >
            {{ node.activa ? 'Desactivar' : 'Activar' }}
          </button>
          <button
            v-if="node.tipo !== 'comite' && node.tipo !== 'politico'"
            type="button"
            class="text-xs px-2 py-0.5 rounded border border-outline-variant hover:bg-surface-container-low"
            @click="toggleDerivacion(node)"
          >
            {{ node.permite_derivacion ? 'Sin derivación' : 'Permitir derivación' }}
          </button>
        </template>
      </div>
      <UnidadTree
        v-if="node.children?.length"
        :nodes="node.children"
        :editable="editable"
        class="pl-6 border-l border-outline-variant/40 ml-2"
        @update="(id, cambios) => emit('update', id, cambios)"
      />
    </li>
  </ul>
</template>

<script setup>
defineProps({
    nodes: { type: Array, required: true },
    editable: { type: Boolean, default: false },
});

const emit = defineEmits(['update']);

function puedeEditar(node) {
    return node.tipo !== 'comite' && node.tipo !== 'politico';
}

function toggleActiva(node) {
    emit('update', node.id, { activa: !node.activa });
}

function toggleDerivacion(node) {
    emit('update', node.id, { permite_derivacion: !node.permite_derivacion });
}
</script>
