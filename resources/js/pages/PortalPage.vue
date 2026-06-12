<template>
  <div class="min-h-screen bg-background text-on-surface font-sans overflow-x-hidden">
    <!-- Top Navigation Bar -->
    <header class="bg-surface sticky top-0 z-50 flex justify-between items-center w-full px-container-padding h-16 border-b border-outline-variant">
      <div class="flex items-center gap-4">
        <div class="flex flex-col">
          <span class="font-headline-lg text-headline-lg font-bold text-primary">SGMI Acobamba</span>
          <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Gestión 2023-2026</span>
        </div>
      </div>
      <div class="hidden md:flex items-center gap-6">
        <nav class="flex gap-4">
          <a class="font-body-md text-primary border-b-2 border-primary py-1 px-2 transition-colors" href="#">Portal</a>
          <a class="font-body-md text-on-surface-variant hover:bg-surface-container-low transition-colors py-1 px-2" href="#">Institucional</a>
          <a class="font-body-md text-on-surface-variant hover:bg-surface-container-low transition-colors py-1 px-2" href="#">Transparencia</a>
        </nav>
        <div class="flex items-center gap-3 border-l border-outline-variant pl-6">
          <span class="material-symbols-outlined text-primary cursor-pointer active:opacity-80 p-2 rounded-full hover:bg-surface-container-low">notifications</span>
          <span class="material-symbols-outlined text-primary cursor-pointer active:opacity-80 p-2 rounded-full hover:bg-surface-container-low">settings</span>
          <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-bold overflow-hidden border border-outline-variant">
            <img
              alt="User profile photo"
              class="w-full h-full object-cover"
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuDHW5HW4HTnrKDVOz4uBiG3iY6VUGli389ESQ2NuWzpUgzhqwtI2HYBbvFvOHXXDUipXrPWmh55DxjnyW0l101D-6D3SRKE1LyuSzOWEZnma7z8SgX-aPxzntWAqu6ca8dwbFl3jlGHpiwZ-R1qopqDSlsNbH6flFRle0S-KrK0ljmNlw3nsrDHBfrG2aKbvTcDLmSxU2IZ1YodahPapURIhJc3XB-CQkW5GJGmGYXTEYpgoTRIUcSSOFii7FRPGZ3LazssG9eN57M"
            />
          </div>
        </div>
      </div>
    </header>

    <main class="min-h-screen">
      <!-- Hero Section with Welcome Message -->
      <section class="relative h-[400px] flex items-center overflow-hidden bg-primary-container/20">
        <!-- Background decorative grid/effects -->
        <div class="absolute inset-0 bg-gradient-to-r from-primary/95 to-primary-container/50 z-0"></div>
        <div class="relative container mx-auto px-container-padding z-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
          <div class="text-white">
            <h1 class="font-headline-xl text-headline-xl mb-4 text-white font-bold leading-tight">Portal del Servidor Municipal</h1>
            <p class="font-body-lg text-body-lg text-white/95 max-w-lg mb-8 leading-relaxed">
              Bienvenido a la plataforma central de servicios internos de la Municipalidad Provincial de Acobamba. Acceda a sus herramientas de gestión y recursos administrativos desde un solo lugar.
            </p>
            <div class="flex gap-4">
              <router-link
                :to="{ name: 'login' }"
                class="bg-secondary-container text-on-secondary-container px-6 py-3 rounded-lg font-label-md text-label-md flex items-center gap-2 hover:bg-secondary-fixed transition-all active:scale-95 shadow-lg font-bold"
              >
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">login</span>
                INGRESAR AL SGMI
              </router-link>
              <button class="border border-white/30 bg-white/10 text-white backdrop-blur-md px-6 py-3 rounded-lg font-label-md text-label-md hover:bg-white/20 transition-all active:scale-95">
                GUÍA DE USUARIO
              </button>
            </div>
          </div>
          <div class="hidden md:block">
            <div class="glass-institutional p-6 rounded-xl border border-white/20 shadow-2xl">
              <div class="flex items-center gap-4 mb-6">
                <span class="material-symbols-outlined text-secondary text-4xl">event_upcoming</span>
                <div>
                  <p class="text-on-surface-variant font-label-md text-label-md uppercase tracking-widest">Próxima Actividad</p>
                  <h3 class="font-headline-md text-headline-md text-on-surface font-bold">Capacitación SIGA-MEF</h3>
                </div>
              </div>
              <div class="space-y-3">
                <div class="flex justify-between items-center text-sm border-b border-outline-variant pb-2">
                  <span class="text-on-surface-variant">Fecha:</span>
                  <span class="font-bold text-on-surface">15 de Octubre, 2026</span>
                </div>
                <div class="flex justify-between items-center text-sm border-b border-outline-variant pb-2">
                  <span class="text-on-surface-variant">Lugar:</span>
                  <span class="font-bold text-on-surface">Auditorio Municipal</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                  <span class="text-on-surface-variant">Estado:</span>
                  <span class="bg-primary/10 text-primary px-2 py-0.5 rounded-full text-xs font-bold uppercase">Confirmado</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Services Bento Grid -->
      <section class="container mx-auto px-container-padding -mt-16 relative z-20 pb-20">
        <div class="bento-grid">
          <!-- SGMI - Main Access -->
          <div class="span-6 bg-white border border-outline-variant rounded-xl p-8 shadow-sm flex flex-col md:flex-row gap-6 items-center hover:shadow-md transition-all group">
            <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
              <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1;">account_balance</span>
            </div>
            <div class="flex-1 text-center md:text-left">
              <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                <h2 class="font-headline-lg text-headline-lg text-primary font-bold">SGMI</h2>
                <span class="bg-secondary-container text-on-secondary-container text-[10px] font-bold px-2 py-0.5 rounded uppercase">Acceso Principal</span>
              </div>
              <p class="text-on-surface-variant font-body-md mb-4 leading-normal">Sistema de Gestión Municipal Integral: Trámite Documentario, Rentas, Catastro y más.</p>
              <router-link :to="{ name: 'login' }" class="text-primary font-bold flex items-center justify-center md:justify-start gap-1 hover:gap-3 transition-all">
                Acceder al sistema <span class="material-symbols-outlined">arrow_forward</span>
              </router-link>
            </div>
          </div>

          <!-- Intranet -->
          <div class="span-3 bg-white border border-outline-variant rounded-xl p-6 shadow-sm hover:border-primary transition-all cursor-pointer">
            <span class="material-symbols-outlined text-secondary-container text-4xl mb-4" style="font-variation-settings: 'FILL' 1;">newspaper</span>
            <h3 class="font-headline-md text-headline-md text-on-surface mb-2 font-bold">Intranet</h3>
            <p class="text-on-surface-variant text-sm mb-4 leading-normal">Noticias institucionales, directivas y resoluciones internas.</p>
            <div class="h-1 w-12 bg-secondary rounded-full"></div>
          </div>

          <!-- Correo -->
          <div class="span-3 bg-white border border-outline-variant rounded-xl p-6 shadow-sm hover:border-primary transition-all cursor-pointer">
            <span class="material-symbols-outlined text-primary text-4xl mb-4">mail</span>
            <h3 class="font-headline-md text-headline-md text-on-surface mb-2 font-bold">Correo</h3>
            <p class="text-on-surface-variant text-sm mb-4 leading-normal">Acceso directo a su cuenta de Outlook Institucional.</p>
            <div class="h-1 w-12 bg-primary rounded-full"></div>
          </div>

          <!-- Trámites Internos (HR) -->
          <div class="span-4 bg-white border border-outline-variant rounded-xl p-6 shadow-sm hover:bg-surface-container-low transition-all">
            <div class="flex justify-between items-start mb-6">
              <div class="p-3 bg-tertiary-container/10 rounded-lg">
                <span class="material-symbols-outlined text-tertiary">badge</span>
              </div>
              <span class="bg-tertiary-container text-on-tertiary-container text-[10px] px-2 py-1 rounded font-semibold">RR.HH.</span>
            </div>
            <h3 class="font-headline-md text-headline-md text-on-surface mb-2 font-bold">Trámites Internos</h3>
            <ul class="space-y-3 mt-4">
              <li class="flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-xs">chevron_right</span> Solicitud de Vacaciones
              </li>
              <li class="flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-xs">chevron_right</span> Permisos y Licencias
              </li>
              <li class="flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-xs">chevron_right</span> Boletas de Pago
              </li>
            </ul>
          </div>

          <!-- Capacitaciones -->
          <div class="span-4 bg-white border border-outline-variant rounded-xl p-6 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-4 mb-4">
              <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center text-white">
                <span class="material-symbols-outlined text-white">school</span>
              </div>
              <h3 class="font-headline-md text-headline-md text-on-surface font-bold">Capacitaciones</h3>
            </div>
            <p class="text-on-surface-variant text-sm mb-4 leading-normal">Centro de aprendizaje continuo para el fortalecimiento de capacidades municipales.</p>
            <div class="bg-surface-container p-3 rounded-lg border border-outline-variant/30">
              <p class="text-[10px] text-primary font-bold uppercase mb-1">Curso Disponible</p>
              <p class="text-sm font-semibold text-on-surface">Ética en la Función Pública 2026</p>
            </div>
          </div>

          <!-- Directorio -->
          <div class="span-4 bg-inverse-surface text-white rounded-xl p-6 shadow-sm flex flex-col justify-between">
            <div>
              <h3 class="font-headline-md text-headline-md mb-2 font-bold text-white">Directorio Telefónico</h3>
              <p class="text-surface-variant text-sm mb-6 leading-normal">Busque extensiones internas y correos de contacto por gerencia.</p>
            </div>
            <div class="relative">
              <input
                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary placeholder-white/40 text-white"
                placeholder="Buscar anexo o nombre..."
                type="text"
              />
              <span class="material-symbols-outlined absolute right-3 top-2 text-white/60">search</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Institutional Footer / Management Info -->
      <section class="bg-surface-container-high py-12 border-t border-outline-variant">
        <div class="container mx-auto px-container-padding grid grid-cols-1 md:grid-cols-3 gap-12">
          <div>
            <div class="flex items-center gap-2 mb-4">
              <div class="w-10 h-10 bg-primary flex items-center justify-center rounded">
                <span class="material-symbols-outlined text-white">domain</span>
              </div>
              <span class="font-label-md text-label-md font-bold text-on-surface uppercase">Municipalidad de Acobamba</span>
            </div>
            <p class="text-on-surface-variant text-sm leading-relaxed">
              Comprometidos con el desarrollo integral y sostenible de nuestra provincia, brindando servicios de calidad y transparencia a todos nuestros ciudadanos y servidores.
            </p>
          </div>
          <div>
            <h4 class="font-label-md text-label-md text-primary font-bold mb-6 uppercase tracking-wider">Enlaces de Interés</h4>
            <ul class="space-y-3">
              <li><a class="text-on-surface-variant hover:text-primary text-sm transition-colors flex items-center gap-2" href="#"><span class="material-symbols-outlined text-sm">link</span> Portal de Transparencia</a></li>
              <li><a class="text-on-surface-variant hover:text-primary text-sm transition-colors flex items-center gap-2" href="#"><span class="material-symbols-outlined text-sm">link</span> Mesa de Partes Virtual</a></li>
              <li><a class="text-on-surface-variant hover:text-primary text-sm transition-colors flex items-center gap-2" href="#"><span class="material-symbols-outlined text-sm">link</span> Boletín Municipal</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-label-md text-label-md text-primary font-bold mb-6 uppercase tracking-wider">Ubicación y Contacto</h4>
            <ul class="space-y-4">
              <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-secondary">location_on</span>
                <span class="text-sm text-on-surface-variant leading-none">Plaza Principal s/n - Acobamba, Huancavelica - Perú</span>
              </li>
              <li class="flex items-center gap-3">
                <span class="material-symbols-outlined text-secondary">call</span>
                <span class="text-sm text-on-surface-variant leading-none">(067) 486012 - Anexo 101</span>
              </li>
              <li class="flex items-center gap-3">
                <span class="material-symbols-outlined text-secondary">mail</span>
                <span class="text-sm text-on-surface-variant leading-none font-bold">info@muniacobamba.gob.pe</span>
              </li>
            </ul>
          </div>
        </div>
      </section>
    </main>

    <footer class="bg-surface-container-high border-t border-outline-variant flex flex-col md:flex-row justify-between items-center px-container-padding py-6 w-full text-on-surface-variant gap-4">
      <div class="flex flex-col items-center md:items-start">
        <span class="font-label-md text-label-md font-bold text-on-surface">SGMI - Gestión Municipal Integral</span>
        <span class="font-body-sm text-body-sm">© 2026 Municipalidad Provincial de Acobamba - Todos los derechos reservados.</span>
      </div>
      <div class="flex gap-6">
        <a class="font-body-sm text-body-sm hover:text-primary transition-opacity underline" href="#">Soporte Técnico</a>
        <a class="font-body-sm text-body-sm hover:text-primary transition-opacity underline" href="#">Manual de Usuario</a>
        <a class="font-body-sm text-body-sm hover:text-primary transition-opacity underline" href="#">Privacidad</a>
      </div>
    </footer>
  </div>
</template>

<script setup>
</script>
