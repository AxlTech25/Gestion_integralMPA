<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-background px-4 py-8">
    <!-- Login Container -->
    <main class="w-full max-w-md">
      <!-- Institutional Header / Logo Area -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center p-4 bg-white rounded-xl shadow-sm mb-4 border border-outline-variant">
          <img
            alt="MPA Logo"
            class="h-16 w-auto"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCWQpnYuBsS6l-9skcvy8jYULeRiqF3t3Oj-Reng_2j1LqK7uCp6RSdKw92Vg52MI63xP9zL5arYFw-Os_iFmjtQwr0onCBL5eYHq2_DdjgcNaOg_LpCBDMNK2otJkvlqiHVNAYPkofX3MDpc3yviCvmUWS9mXtkMVswMzTkbRrLl3zNrpYiu14uhTB5DKnfnRMRVZsJgw2Wx0rijkk5f7Aw9Vso-q8DtJ1QQtui6r77eJ74lpNGTsxCQLBEm5BZvXFnvGX8uLJhHk"
          />
        </div>
        <h1 class="font-headline-lg text-headline-lg text-on-surface mb-1 font-bold">SGMI Acobamba</h1>
        <p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-widest leading-none">Sistema de Gestión Municipal Integrado</p>
      </div>

      <!-- Login Card -->
      <div class="bg-surface-container-lowest rounded-xl p-8 md:p-10 border border-outline-variant shadow-sm">
        <form class="space-y-6" @submit.prevent="submit">
          <!-- Username Field -->
          <div class="space-y-2">
            <label class="block font-label-md text-label-md text-on-surface font-semibold" for="username">Usuario Institucional</label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">person</span>
              <input
                v-model="username"
                class="w-full pl-10 pr-4 py-3 bg-white border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all text-on-surface"
                id="username"
                name="username"
                placeholder="nombre.apellido"
                required
                autocomplete="username"
                type="text"
              />
            </div>
          </div>

          <!-- Password Field -->
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <label class="block font-label-md text-label-md text-on-surface font-semibold" for="password">Contraseña</label>
            </div>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">lock</span>
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                class="w-full pl-10 pr-12 py-3 bg-white border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all text-on-surface"
                id="password"
                name="password"
                placeholder="••••••••"
                required
                autocomplete="current-password"
              />
              <button
                class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors flex items-center"
                @click="showPassword = !showPassword"
                type="button"
              >
                <span class="material-symbols-outlined">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
              </button>
            </div>
          </div>

          <!-- Error Alert -->
          <p v-if="auth.error" class="text-sm text-error font-medium px-2 py-1.5 bg-error-container text-on-error-container rounded border border-error/20">
            {{ auth.error }}
          </p>

          <!-- Submit Button -->
          <button
            :disabled="auth.loading"
            class="w-full bg-[#2A9D8F] hover:bg-[#238276] text-white font-label-md text-label-md py-4 rounded-lg shadow-sm transition-all active:scale-[0.98] flex items-center justify-center gap-2 font-bold cursor-pointer disabled:opacity-70 disabled:cursor-not-allowed"
            type="submit"
          >
            <template v-if="auth.loading">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              VALIDANDO...
            </template>
            <template v-else>
              <span class="material-symbols-outlined" style="font-variation-settings: 'wght' 600;">login</span>
              INGRESAR
            </template>
          </button>

          <!-- Auxiliary Links -->
          <div class="flex flex-col gap-3 pt-2">
            <a class="text-center font-body-sm text-body-sm text-primary hover:underline transition-all font-semibold" href="#">
              Olvidé mi contraseña
            </a>
            <div class="h-px bg-outline-variant w-1/3 mx-auto"></div>
            <a class="text-center font-body-sm text-body-sm text-on-surface-variant hover:text-primary flex items-center justify-center gap-1 transition-colors font-semibold" href="#">
              <span class="material-symbols-outlined text-[18px]">contact_support</span>
              Soporte Técnico
            </a>
          </div>
        </form>
      </div>

      <!-- Security Warning -->
      <div class="mt-8 px-4 flex gap-3 items-start opacity-80">
        <span class="material-symbols-outlined text-error text-[20px] mt-0.5" style="font-variation-settings: 'FILL' 1;">warning</span>
        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
          <span class="font-bold text-error">Acceso restringido para personal autorizado.</span>
          Este sistema registra toda actividad IP. Bloqueo automático tras 5 intentos fallidos de inicio de sesión.
        </p>
      </div>

      <!-- Footer -->
      <footer class="mt-12 text-center">
        <p class="font-body-sm text-body-sm text-outline">
          © 2026 Municipalidad Provincial de Acobamba - SGMI
        </p>
      </footer>
    </main>
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

async function submit() {
  try {
    await auth.login(username.value, password.value);
    router.push({ name: 'dashboard' });
  } catch (e) {
    // Error will be shown in auth.error
  }
}
</script>
