<template>
  <div class="min-h-screen bg-slate-50">
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">
      <div>
        <h1 class="text-lg font-semibold text-slate-800">Panel principal</h1>
        <p class="text-sm text-slate-500">SGMI — Fase 1</p>
      </div>
      <div class="flex items-center gap-4">
        <span class="text-sm text-slate-600">{{ auth.user?.nombre_completo }}</span>
        <button
          type="button"
          class="text-sm text-blue-700 hover:underline"
          @click="logout"
        >
          Cerrar sesión
        </button>
      </div>
    </header>
    <main class="p-6">
      <div class="bg-white rounded-lg border border-slate-200 p-6">
        <p class="text-slate-700">
          Bienvenido, <strong>{{ auth.user?.nombre_completo }}</strong>.
        </p>
        <p class="text-sm text-slate-500 mt-2">
          Usuario: {{ auth.user?.username }} · Unidad ID: {{ auth.user?.unidad_activa_id }}
        </p>
        <p class="text-sm text-slate-400 mt-4">
          Sprint 1: autenticación local operativa. Módulos documentaria y patrimonio en siguientes iteraciones.
        </p>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const router = useRouter();

async function logout() {
    await auth.logout();
    router.push({ name: 'login' });
}
</script>
