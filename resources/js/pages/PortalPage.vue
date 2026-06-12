<template>
  <div class="min-h-screen bg-background text-on-surface overflow-x-hidden">
    <PortalHeader />

    <main class="min-h-screen">
      <!-- Hero -->
      <section class="relative h-[400px] flex items-center overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-primary/90 to-primary-container/40" />
        <div class="relative container mx-auto px-container-padding z-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
          <div class="text-on-primary-container">
            <h1 class="text-headline-xl text-white mb-4 font-bold leading-tight">Portal del Servidor Municipal</h1>
            <p class="text-body-lg text-white/90 max-w-lg mb-8 leading-relaxed">
              Bienvenido a la plataforma central de servicios internos de la Municipalidad Provincial de Acobamba.
              Acceda a sus herramientas de gestión y recursos administrativos desde un solo lugar.
            </p>
            <div class="flex flex-wrap gap-4">
              <router-link
                :to="sgmiRoute"
                class="bg-secondary-container text-on-secondary-container px-6 py-3 rounded-lg text-label-md flex items-center gap-2 hover:bg-secondary-fixed transition-all active:scale-95 shadow-lg font-semibold"
              >
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">login</span>
                INGRESAR AL SGMI
              </router-link>
              <a
                href="#manual-usuario"
                class="border border-white/30 bg-white/10 text-white backdrop-blur-md px-6 py-3 rounded-lg text-label-md hover:bg-white/20 transition-all active:scale-95 font-semibold"
              >
                GUÍA DE USUARIO
              </a>
            </div>
          </div>

          <div class="hidden md:block">
            <div class="glass-institutional p-6 rounded-xl border border-white/20 shadow-2xl">
              <div class="flex items-center gap-4 mb-6">
                <span class="material-symbols-outlined text-secondary text-4xl">event_upcoming</span>
                <div>
                  <p class="text-on-surface-variant text-label-md uppercase tracking-widest font-semibold">Próxima Actividad</p>
                  <h2 class="text-headline-md text-on-surface font-semibold">{{ actividad.titulo }}</h2>
                </div>
              </div>
              <dl class="space-y-3 text-sm">
                <div class="flex justify-between items-center border-b border-outline-variant pb-2">
                  <dt class="text-on-surface-variant">Fecha:</dt>
                  <dd class="font-bold text-on-surface">{{ actividad.fecha }}</dd>
                </div>
                <div class="flex justify-between items-center border-b border-outline-variant pb-2">
                  <dt class="text-on-surface-variant">Lugar:</dt>
                  <dd class="font-bold text-on-surface">{{ actividad.lugar }}</dd>
                </div>
                <div class="flex justify-between items-center">
                  <dt class="text-on-surface-variant">Estado:</dt>
                  <dd>
                    <span class="bg-primary/10 text-primary px-2 py-0.5 rounded-full text-xs font-bold uppercase">
                      {{ actividad.estado }}
                    </span>
                  </dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </section>

      <!-- Bento grid servicios -->
      <section class="container mx-auto px-container-padding -mt-16 relative z-20 pb-20" aria-label="Servicios del portal">
        <div class="bento-grid">
          <!-- SGMI -->
          <article class="span-6 bg-white border border-outline-variant rounded-xl p-8 shadow-sm flex flex-col md:flex-row gap-6 items-center hover:shadow-md transition-all group portal-bento-card">
            <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shrink-0">
              <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1">account_balance</span>
            </div>
            <div class="flex-1 text-center md:text-left">
              <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                <h2 class="text-headline-lg text-primary font-bold">SGMI</h2>
                <span class="bg-secondary-container text-on-secondary-container text-[10px] font-bold px-2 py-0.5 rounded uppercase">Acceso Principal</span>
              </div>
              <p class="text-on-surface-variant text-body-md mb-4">
                Sistema de Gestión Municipal Integral: Trámite Documentario, Rentas, Catastro y más.
              </p>
              <router-link :to="sgmiRoute" class="text-primary font-bold flex items-center justify-center md:justify-start gap-1 hover:gap-3 transition-all">
                Acceder al sistema
                <span class="material-symbols-outlined">arrow_forward</span>
              </router-link>
            </div>
          </article>

          <!-- Intranet -->
          <article class="span-3 bg-white border border-outline-variant rounded-xl p-6 shadow-sm hover:border-primary transition-all cursor-pointer portal-bento-card" @click="servicioPlaceholder('Intranet')">
            <span class="material-symbols-outlined text-secondary-container text-4xl mb-4" style="font-variation-settings: 'FILL' 1">newspaper</span>
            <h3 class="text-headline-md text-on-surface mb-2 font-semibold">Intranet</h3>
            <p class="text-on-surface-variant text-sm mb-4">Noticias institucionales, directivas y resoluciones internas.</p>
            <div class="h-1 w-12 bg-secondary rounded-full" />
          </article>

          <!-- Correo -->
          <article
            class="span-3 bg-white border border-outline-variant rounded-xl p-6 shadow-sm hover:border-primary transition-all cursor-pointer portal-bento-card"
            @click="abrirCorreo"
          >
            <span class="material-symbols-outlined text-primary text-4xl mb-4">mail</span>
            <h3 class="text-headline-md text-on-surface mb-2 font-semibold">Correo</h3>
            <p class="text-on-surface-variant text-sm mb-4">Acceso directo a su cuenta de Outlook Institucional.</p>
            <div class="h-1 w-12 bg-primary rounded-full" />
          </article>

          <!-- Trámites RR.HH. -->
          <article class="span-4 bg-white border border-outline-variant rounded-xl p-6 shadow-sm hover:bg-surface-container-low transition-all portal-bento-card">
            <div class="flex justify-between items-start mb-6">
              <div class="p-3 bg-tertiary-container/10 rounded-lg">
                <span class="material-symbols-outlined text-tertiary">badge</span>
              </div>
              <span class="bg-tertiary-container text-on-tertiary-container text-[10px] px-2 py-1 rounded font-semibold">RR.HH.</span>
            </div>
            <h3 class="text-headline-md text-on-surface mb-2 font-semibold">Trámites Internos</h3>
            <ul class="space-y-3 mt-4">
              <li
                v-for="tramite in TRAMITES_INTERNOS"
                :key="tramite"
                class="flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition-colors cursor-pointer"
                @click="servicioPlaceholder(tramite)"
              >
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                {{ tramite }}
              </li>
            </ul>
          </article>

          <!-- Capacitaciones -->
          <article class="span-4 bg-white border border-outline-variant rounded-xl p-6 shadow-sm hover:shadow-md transition-all portal-bento-card">
            <div class="flex items-center gap-4 mb-4">
              <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center text-white shrink-0">
                <span class="material-symbols-outlined">school</span>
              </div>
              <h3 class="text-headline-md text-on-surface font-semibold">Capacitaciones</h3>
            </div>
            <p class="text-on-surface-variant text-sm mb-4">
              Centro de aprendizaje continuo para el fortalecimiento de capacidades municipales.
            </p>
            <div class="bg-surface-container p-3 rounded-lg border border-outline-variant/30">
              <p class="text-[10px] text-primary font-bold uppercase mb-1">Curso Disponible</p>
              <p class="text-sm font-semibold text-on-surface">{{ CURSO_DISPONIBLE }}</p>
            </div>
          </article>

          <!-- Directorio -->
          <article class="span-4 bg-inverse-surface text-inverse-on-surface rounded-xl p-6 shadow-sm flex flex-col justify-between portal-bento-card min-h-[220px]">
            <div>
              <h3 class="text-headline-md font-semibold mb-2">Directorio Telefónico</h3>
              <p class="text-surface-variant text-sm mb-4">Busque extensiones internas y correos de contacto por gerencia.</p>
            </div>
            <div class="relative">
              <input
                v-model="busquedaDirectorio"
                type="search"
                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-1 focus:ring-primary placeholder-white/40 text-white"
                placeholder="Buscar anexo o nombre..."
                aria-label="Buscar en directorio"
              />
              <span class="material-symbols-outlined absolute right-3 top-2 text-white/60 pointer-events-none">search</span>
            </div>
            <ul v-if="busquedaDirectorio && resultadosDirectorio.length" class="mt-3 space-y-2 text-sm">
              <li
                v-for="item in resultadosDirectorio"
                :key="item.anexo"
                class="bg-white/10 rounded px-3 py-2 flex justify-between"
              >
                <span>{{ item.nombre }}</span>
                <span class="font-bold">Anexo {{ item.anexo }}</span>
              </li>
            </ul>
            <p v-else-if="busquedaDirectorio" class="mt-3 text-sm text-white/70">Sin resultados.</p>
          </article>
        </div>
      </section>

      <!-- Institucional -->
      <section class="bg-surface-container-high py-12 border-t border-outline-variant" id="manual-usuario">
        <div class="container mx-auto px-container-padding grid grid-cols-1 md:grid-cols-3 gap-12">
          <div>
            <div class="flex items-center gap-2 mb-4">
              <div class="w-10 h-10 bg-primary flex items-center justify-center rounded shrink-0">
                <span class="material-symbols-outlined text-white">domain</span>
              </div>
              <span class="text-label-md font-bold text-on-surface uppercase">Municipalidad de Acobamba</span>
            </div>
            <p class="text-on-surface-variant text-sm leading-relaxed">
              Comprometidos con el desarrollo integral y sostenible de nuestra provincia, brindando servicios de calidad
              y transparencia a todos nuestros ciudadanos y servidores.
            </p>
          </div>
          <div>
            <h4 class="text-label-md text-primary font-bold mb-6 uppercase tracking-wider">Enlaces de Interés</h4>
            <ul class="space-y-3">
              <li v-for="enlace in ENLACES_INTERES" :key="enlace.label">
                <a :href="enlace.href" class="text-on-surface-variant hover:text-primary text-sm transition-colors flex items-center gap-2">
                  <span class="material-symbols-outlined text-sm">link</span>
                  {{ enlace.label }}
                </a>
              </li>
            </ul>
          </div>
          <div>
            <h4 class="text-label-md text-primary font-bold mb-6 uppercase tracking-wider">Ubicación y Contacto</h4>
            <ul class="space-y-4">
              <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-secondary shrink-0">location_on</span>
                <span class="text-sm text-on-surface-variant">{{ contacto.direccion }}</span>
              </li>
              <li class="flex items-center gap-3">
                <span class="material-symbols-outlined text-secondary shrink-0">call</span>
                <span class="text-sm text-on-surface-variant">{{ contacto.telefono }}</span>
              </li>
              <li class="flex items-center gap-3">
                <span class="material-symbols-outlined text-secondary shrink-0">mail</span>
                <a :href="`mailto:${contacto.email}`" class="text-sm text-on-surface-variant hover:text-primary">{{ contacto.email }}</a>
              </li>
            </ul>
          </div>
        </div>
      </section>
    </main>

    <PortalFooter />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import PortalHeader from '../components/portal/PortalHeader.vue';
import PortalFooter from '../components/portal/PortalFooter.vue';
import {
    PROXIMA_ACTIVIDAD,
    CURSO_DISPONIBLE,
    TRAMITES_INTERNOS,
    ENLACES_INTERES,
    CONTACTO_INSTITUCIONAL,
    DIRECTORIO_ANEXOS,
} from '../constants/portal';

const auth = useAuthStore();
const busquedaDirectorio = ref('');

const actividad = PROXIMA_ACTIVIDAD;
const contacto = CONTACTO_INSTITUCIONAL;

const sgmiRoute = computed(() =>
    auth.user ? { name: 'dashboard' } : { name: 'login' }
);

const resultadosDirectorio = computed(() => {
    const q = busquedaDirectorio.value.trim().toLowerCase();
    if (!q) return [];
    return DIRECTORIO_ANEXOS.filter(
        (item) =>
            item.nombre.toLowerCase().includes(q) ||
            item.anexo.includes(q) ||
            item.gerencia.toLowerCase().includes(q)
    ).slice(0, 5);
});

function servicioPlaceholder(nombre) {
    alert(`Módulo "${nombre}" disponible en una fase posterior del SGMI.`);
}

function abrirCorreo() {
    window.open('https://outlook.office.com', '_blank', 'noopener,noreferrer');
}
</script>
