<template>
  <header class="bg-surface sticky top-0 z-50 flex justify-between items-center w-full px-container-padding h-16 border-b border-outline-variant">
    <div class="flex items-center gap-4">
      <div class="flex flex-col">
        <span class="text-headline-lg text-primary font-bold">SGMI Acobamba</span>
        <span class="text-label-md text-on-surface-variant uppercase tracking-wider font-semibold">Gestión 2023-2026</span>
      </div>
    </div>

    <button
      type="button"
      class="md:hidden p-2 text-primary rounded-full hover:bg-surface-container-low"
      aria-label="Menú"
      @click="menuOpen = !menuOpen"
    >
      <span class="material-symbols-outlined">{{ menuOpen ? 'close' : 'menu' }}</span>
    </button>

    <div :class="[menuOpen ? 'flex' : 'hidden', 'md:flex items-center gap-6 absolute md:relative top-16 md:top-0 left-0 right-0 md:left-auto bg-surface md:bg-transparent border-b md:border-0 border-outline-variant p-4 md:p-0 flex-col md:flex-row shadow-lg md:shadow-none']">
      <nav class="flex flex-col md:flex-row gap-2 md:gap-4 w-full md:w-auto">
        <a class="text-body-md text-primary border-b-2 border-primary py-1 px-2 font-medium" href="#">Portal</a>
        <a class="text-body-md text-on-surface-variant hover:bg-surface-container-low transition-colors py-1 px-2 rounded" href="#">Institucional</a>
        <a class="text-body-md text-on-surface-variant hover:bg-surface-container-low transition-colors py-1 px-2 rounded" href="#">Transparencia</a>
      </nav>
      <div class="flex items-center gap-3 border-t md:border-t-0 md:border-l border-outline-variant pt-4 md:pt-0 md:pl-6">
        <button type="button" class="material-symbols-outlined text-primary p-2 rounded-full hover:bg-surface-container-low transition-colors" title="Notificaciones">
          notifications
        </button>
        <button type="button" class="material-symbols-outlined text-primary p-2 rounded-full hover:bg-surface-container-low transition-colors" title="Configuración">
          settings
        </button>
        <router-link
          :to="authLink"
          class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-bold overflow-hidden border border-outline-variant"
        >
          <img
            alt="Perfil"
            class="w-full h-full object-cover"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDHW5HW4HTnrKDVOz4uBiG3iY6VUGli389ESQ2NuWzpUgzhqwtI2HYBbvFvOHXXDUipXrPWmh55DxjnyW0l101D-6D3SRKE1LyuSzOWEZnma7z8SgX-aPxzntWAqu6ca8dwbFl3jlGHpiwZ-R1qopqDSlsNbH6flFRle0S-KrK0ljmNlw3nsrDHBfrG2aKbvTcDLmSxU2IZ1YodahPapURIhJc3XB-CQkW5GJGmGYXTEYpgoTRIUcSSOFii7FRPGZ3LazssG9eN57M"
          />
        </router-link>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '../../stores/auth';

const auth = useAuthStore();
const menuOpen = ref(false);

const authLink = computed(() =>
    auth.user ? { name: 'dashboard' } : { name: 'login' }
);
</script>
