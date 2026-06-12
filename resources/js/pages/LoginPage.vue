<template>
  <div class="login-page min-h-screen flex items-center justify-center p-4">
    <main class="w-full max-w-md">
      <!-- Institutional Header / Logo Area -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center p-4 bg-white rounded-xl shadow-sm mb-4 border border-outline-variant">
          <img
            alt="Escudo Municipalidad Provincial de Acobamba"
            class="h-16 w-auto"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCWQpnYuBsS6l-9skcvy8jYULeRiqF3t3Oj-Reng_2j1LqK7uCp6RSdKw92Vg52MI63xP9zL5arYFw-Os_iFmjtQwr0onCBL5eYHq2_DdjgcNaOg_LpCBDMNK2otJkvlqiHVNAYPkofX3MDpc3yviCvmUWS9mXtkMVswMzTkbRrLl3zNrpYiu14uhTB5DKnfnRMRVZsJgw2Wx0rijkk5f7Aw9Vso-q8DtJ1QQtui6r77eJ74lpNGTsxCQLBEm5BZvXFnvGX8uLJhHk"
          />
        </div>
        <h1 class="text-headline-lg text-on-surface mb-1 font-semibold tracking-tight">SGMI Acobamba</h1>
        <p class="text-label-md text-on-surface-variant uppercase tracking-widest font-semibold">Sistema de Gestión Municipal Integrado</p>
      </div>

      <!-- Login Card -->
      <div class="login-card bg-surface-container-lowest rounded-xl p-8 md:p-10">
        <form class="space-y-6" @submit.prevent="submit">
          <!-- Username -->
          <div class="space-y-2">
            <label class="block text-label-md text-on-surface font-semibold" for="username">Usuario Institucional</label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">person</span>
              <input
                id="username"
                v-model="username"
                type="text"
                name="username"
                required
                autocomplete="username"
                placeholder="nombre.apellido"
                class="login-input w-full pl-10 pr-4 py-3 bg-white border border-outline-variant rounded-lg text-body-md text-on-surface transition-all"
              />
            </div>
          </div>

          <!-- Password -->
          <div class="space-y-2">
            <label class="block text-label-md text-on-surface font-semibold" for="password">Contraseña</label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">lock</span>
              <input
                id="password"
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="login-input w-full pl-10 pr-12 py-3 bg-white border border-outline-variant rounded-lg text-body-md text-on-surface transition-all"
              />
              <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors"
                :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                @click="showPassword = !showPassword"
              >
                <span class="material-symbols-outlined">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
              </button>
            </div>
          </div>

          <!-- Error -->
          <p
            v-if="auth.error"
            class="text-body-sm text-on-error-container bg-error-container border border-error/20 rounded-lg px-3 py-2 font-medium"
          >
            {{ auth.error }}
          </p>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="auth.loading"
            class="w-full bg-[#2A9D8F] hover:bg-[#238276] text-white text-label-md py-4 rounded-lg shadow-sm transition-all active:scale-[0.98] flex items-center justify-center gap-2 font-semibold cursor-pointer disabled:opacity-70 disabled:cursor-not-allowed disabled:active:scale-100"
          >
            <template v-if="auth.loading">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              VALIDANDO...
            </template>
            <template v-else>
              <span class="material-symbols-outlined" style="font-variation-settings: 'wght' 600">login</span>
              INGRESAR
            </template>
          </button>

          <!-- Auxiliary Links -->
          <div class="flex flex-col gap-3 pt-2">
            <button
              type="button"
              class="text-center text-body-sm text-primary hover:underline transition-all font-medium bg-transparent border-0 cursor-pointer"
              @click="mostrarRecuperacion = true"
            >
              Olvidé mi contraseña
            </button>
            <div class="h-px bg-outline-variant w-1/3 mx-auto" />
            <a
              href="mailto:utis@mpa.gob.pe"
              class="text-center text-body-sm text-on-surface-variant hover:text-primary flex items-center justify-center gap-1 transition-colors font-medium"
            >
              <span class="material-symbols-outlined text-[18px]">contact_support</span>
              Soporte Técnico
            </a>
          </div>
        </form>
      </div>

      <!-- Security Warning -->
      <div class="mt-8 px-4 flex gap-3 items-start opacity-80">
        <span class="material-symbols-outlined text-error text-[20px] mt-0.5 shrink-0" style="font-variation-settings: 'FILL' 1">warning</span>
        <p class="text-body-sm text-on-surface-variant leading-relaxed">
          <span class="font-bold text-error">Acceso restringido para personal autorizado.</span>
          Este sistema registra toda actividad IP. Bloqueo automático tras 5 intentos fallidos de inicio de sesión.
        </p>
      </div>

      <!-- Footer -->
      <footer class="mt-12 text-center">
        <p class="text-body-sm text-outline">
          © {{ anio }} Municipalidad Provincial de Acobamba - SGMI
        </p>
      </footer>
    </main>

    <!-- Modal recuperación (placeholder Fase 1) -->
    <div
      v-if="mostrarRecuperacion"
      class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/40 p-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby="recuperar-titulo"
    >
      <div class="bg-surface-container-lowest rounded-xl p-6 max-w-sm w-full shadow-lg border border-outline-variant">
        <h2 id="recuperar-titulo" class="text-headline-md text-on-surface font-semibold mb-2">Recuperación de acceso</h2>
        <p class="text-body-sm text-on-surface-variant mb-4">
          Contacte a la Unidad de Tecnologías de la Información y Sistemas (UTIS) para restablecer su contraseña institucional.
        </p>
        <button
          type="button"
          class="w-full py-2 bg-primary text-on-primary rounded-lg text-label-md font-semibold"
          @click="mostrarRecuperacion = false"
        >
          Entendido
        </button>
      </div>
    </div>
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
const showPassword = ref(false);
const mostrarRecuperacion = ref(false);
const anio = new Date().getFullYear();

async function submit() {
  try {
    await auth.login(username.value.trim(), password.value);
    await router.push({ name: 'dashboard' });
  } catch {
    // Mensaje en auth.error
  }
}
</script>
