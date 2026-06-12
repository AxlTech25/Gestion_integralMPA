<template>
  <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden flex flex-col hover-card transition-all duration-200">
    <div class="p-6 border-b border-outline-variant bg-surface-container-low">
      <h2 class="text-headline-md text-on-surface font-semibold">Alertas de Inventario TI</h2>
    </div>
    <div class="flex-1 overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-surface-container-high/50 border-b border-outline-variant">
            <th class="px-6 py-3 text-label-md text-on-surface-variant font-semibold">EQUIPO</th>
            <th class="px-6 py-3 text-label-md text-on-surface-variant font-semibold">ESTADO</th>
            <th class="px-6 py-3 text-label-md text-on-surface-variant font-semibold">GERENCIA</th>
            <th class="px-6 py-3 text-label-md text-on-surface-variant font-semibold">ACCIÓN</th>
          </tr>
        </thead>
        <tbody class="text-table-data">
          <tr
            v-for="alerta in alertas"
            :key="alerta.id"
            class="border-b border-outline-variant hover:bg-surface-container-low transition-colors h-10 last:border-b-0"
          >
            <td class="px-6 py-3 font-semibold text-on-surface">{{ alerta.equipo }}</td>
            <td class="px-6 py-3">
              <span :class="[estadoClass(alerta.color), 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase']">
                {{ alerta.estado }}
              </span>
            </td>
            <td class="px-6 py-3 text-on-surface-variant">{{ alerta.gerencia }}</td>
            <td class="px-6 py-3">
              <button
                type="button"
                class="text-primary font-bold hover:underline cursor-pointer bg-transparent border-0"
                @click="$emit('accion', alerta)"
              >
                {{ alerta.accion }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ALERTA_ESTADO_CLASSES } from '../../constants/dashboardEstrategico';

defineProps({
    alertas: { type: Array, required: true },
});

defineEmits(['accion']);

function estadoClass(color) {
    return ALERTA_ESTADO_CLASSES[color] ?? ALERTA_ESTADO_CLASSES.secondary;
}
</script>
