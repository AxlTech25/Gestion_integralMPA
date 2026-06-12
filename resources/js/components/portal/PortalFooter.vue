<template>
  <footer class="bg-surface-container-high border-t border-outline-variant flex flex-col md:flex-row justify-between items-center px-container-padding py-6 w-full gap-4">
    <div class="flex flex-col items-center md:items-start text-center md:text-left">
      <span class="text-label-md font-bold text-on-surface">SGMI - Gestión Municipal Integral</span>
      <span class="text-body-sm text-on-surface-variant">
        © {{ anio }} Municipalidad Provincial de Acobamba - Todos los derechos reservados.
      </span>
    </div>
    <div class="flex gap-6">
      <a class="text-body-sm text-on-surface-variant hover:text-primary underline transition-opacity" href="mailto:utis@mpa.gob.pe">
        Soporte Técnico
      </a>
      <a class="text-body-sm text-on-surface-variant hover:text-primary underline transition-opacity" href="#">
        Manual de Usuario
      </a>
      <a class="text-body-sm text-on-surface-variant hover:text-primary underline transition-opacity" href="#">
        Privacidad
      </a>
    </div>
  </footer>
</template>

<script setup>
const anio = new Date().getFullYear();
</script>
