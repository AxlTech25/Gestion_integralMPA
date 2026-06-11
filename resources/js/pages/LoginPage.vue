<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-100">
    <form class="w-full max-w-md bg-white p-8 rounded-lg shadow" @submit.prevent="submit">
      <h1 class="text-2xl font-semibold text-slate-800 mb-2">SGMI — Municipalidad de Acobamba</h1>
      <p class="text-sm text-slate-500 mb-6">Sistema de Gestión Municipal Integral</p>

      <label class="block text-sm font-medium text-slate-700 mb-1">Usuario</label>
      <input
        v-model="username"
        type="text"
        required
        autocomplete="username"
        class="w-full border border-slate-300 rounded px-3 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
      />

      <label class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
      <input
        v-model="password"
        type="password"
        required
        autocomplete="current-password"
        class="w-full border border-slate-300 rounded px-3 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
      />

      <p v-if="auth.error" class="text-sm text-red-600 mb-4">{{ auth.error }}</p>

      <button
        type="submit"
        :disabled="auth.loading"
        class="w-full bg-blue-700 text-white py-2 rounded hover:bg-blue-800 disabled:opacity-50"
      >
        {{ auth.loading ? 'Ingresando…' : 'Ingresar' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const router = useRouter();
const username = ref('');
const password = ref('');

async function submit() {
    try {
        await auth.login(username.value, password.value);
        router.push({ name: 'dashboard' });
    } catch {
        // error en store
    }
}
</script>
