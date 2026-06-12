<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-headline-lg font-bold text-primary">Usuarios del sistema</h1>
        <p class="text-body-sm text-on-surface-variant">Administración de cuentas y roles (UTIS).</p>
      </div>
      <button
        type="button"
        class="bg-primary text-on-primary px-4 py-2 rounded-lg text-label-md font-semibold hover:opacity-90"
        @click="openCreate"
      >
        Nuevo usuario
      </button>
    </div>

    <div class="flex gap-3">
      <input
        v-model="busqueda"
        type="search"
        placeholder="Buscar por nombre o usuario..."
        class="border border-outline-variant rounded-lg px-3 py-2 text-body-sm flex-1 max-w-md bg-surface"
        @keyup.enter="loadUsuarios"
      />
      <button type="button" class="px-4 py-2 border border-outline-variant rounded-lg text-label-md" @click="loadUsuarios">
        Buscar
      </button>
    </div>

    <div v-if="loading" class="text-on-surface-variant">Cargando usuarios...</div>
    <div v-else class="bg-surface border border-outline-variant rounded-xl overflow-hidden">
      <table class="w-full text-left text-body-sm">
        <thead class="bg-surface-container-low border-b border-outline-variant">
          <tr>
            <th class="px-4 py-3 font-semibold">Usuario</th>
            <th class="px-4 py-3 font-semibold">Nombre</th>
            <th class="px-4 py-3 font-semibold">Unidad</th>
            <th class="px-4 py-3 font-semibold">Roles</th>
            <th class="px-4 py-3 font-semibold">Estado</th>
            <th class="px-4 py-3 font-semibold w-32">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in usuarios" :key="u.id" class="border-b border-outline-variant/50 hover:bg-surface-container-low/50">
            <td class="px-4 py-3 font-mono">{{ u.username }}</td>
            <td class="px-4 py-3">{{ u.nombre_completo }}</td>
            <td class="px-4 py-3">{{ u.unidad_activa?.nombre ?? u.unidadActiva?.nombre ?? '—' }}</td>
            <td class="px-4 py-3">
              <span v-for="r in u.roles" :key="r.id" class="inline-block bg-secondary-container text-on-secondary-container text-xs px-2 py-0.5 rounded mr-1 mb-1">
                {{ r.nombre }}
              </span>
            </td>
            <td class="px-4 py-3">
              <span :class="u.activo ? 'text-primary' : 'text-error'">{{ u.activo ? 'Activo' : 'Inactivo' }}</span>
            </td>
            <td class="px-4 py-3">
              <button type="button" class="text-primary text-label-md font-semibold" @click="openTraslado(u)">Trasladar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal crear -->
    <div v-if="showForm" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="showForm = false">
      <form class="bg-surface rounded-xl p-6 w-full max-w-lg space-y-4 border border-outline-variant" @submit.prevent="guardarUsuario">
        <h2 class="text-title-lg font-bold">Nuevo usuario</h2>
        <label class="block">
          <span class="text-label-md">Usuario</span>
          <input v-model="form.username" required class="mt-1 w-full border border-outline-variant rounded-lg px-3 py-2" />
        </label>
        <label class="block">
          <span class="text-label-md">Nombre completo</span>
          <input v-model="form.nombre_completo" required class="mt-1 w-full border border-outline-variant rounded-lg px-3 py-2" />
        </label>
        <label class="block">
          <span class="text-label-md">Correo</span>
          <input v-model="form.email" type="email" class="mt-1 w-full border border-outline-variant rounded-lg px-3 py-2" />
        </label>
        <label class="block">
          <span class="text-label-md">Contraseña (min. 8 + especial)</span>
          <input v-model="form.password" type="password" required class="mt-1 w-full border border-outline-variant rounded-lg px-3 py-2" />
        </label>
        <label class="block">
          <span class="text-label-md">Unidad activa</span>
          <select v-model="form.unidad_activa_id" required class="mt-1 w-full border border-outline-variant rounded-lg px-3 py-2">
            <option v-for="un in unidadesFlat" :key="un.id" :value="un.id">{{ un.nombre }}</option>
          </select>
        </label>
        <label class="block">
          <span class="text-label-md">Roles</span>
          <select v-model="form.role_ids" multiple class="mt-1 w-full border border-outline-variant rounded-lg px-3 py-2 h-24">
            <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.nombre }}</option>
          </select>
        </label>
        <p v-if="formError" class="text-error text-body-sm">{{ formError }}</p>
        <div class="flex gap-3 justify-end">
          <button type="button" class="px-4 py-2 border rounded-lg" @click="showForm = false">Cancelar</button>
          <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-semibold">Guardar</button>
        </div>
      </form>
    </div>

    <!-- Modal traslado -->
    <div v-if="trasladoUser" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="trasladoUser = null">
      <form class="bg-surface rounded-xl p-6 w-full max-w-md space-y-4 border border-outline-variant" @submit.prevent="ejecutarTraslado">
        <h2 class="text-title-lg font-bold">Trasladar usuario</h2>
        <p class="text-body-sm">{{ trasladoUser.nombre_completo }}</p>
        <label class="block">
          <span class="text-label-md">Nueva unidad</span>
          <select v-model="trasladoForm.unidad_id" required class="mt-1 w-full border rounded-lg px-3 py-2">
            <option v-for="un in unidadesFlat" :key="un.id" :value="un.id">{{ un.nombre }}</option>
          </select>
        </label>
        <label class="block">
          <span class="text-label-md">Motivo</span>
          <textarea v-model="trasladoForm.motivo" rows="3" class="mt-1 w-full border rounded-lg px-3 py-2" />
        </label>
        <div class="flex gap-3 justify-end">
          <button type="button" class="px-4 py-2 border rounded-lg" @click="trasladoUser = null">Cancelar</button>
          <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg">Confirmar</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const usuarios = ref([]);
const roles = ref([]);
const unidadesFlat = ref([]);
const loading = ref(false);
const busqueda = ref('');
const showForm = ref(false);
const formError = ref('');
const trasladoUser = ref(null);

const form = ref({
    username: '',
    nombre_completo: '',
    email: '',
    password: '',
    unidad_activa_id: null,
    role_ids: [],
});

const trasladoForm = ref({ unidad_id: null, motivo: '' });

function flattenTree(nodes, acc = []) {
    for (const n of nodes) {
        acc.push({ id: n.id, nombre: n.nombre });
        if (n.children?.length) flattenTree(n.children, acc);
    }
    return acc;
}

async function loadUsuarios() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/usuarios', { params: { q: busqueda.value || undefined } });
        usuarios.value = data.data ?? data;
    } finally {
        loading.value = false;
    }
}

async function loadMeta() {
    const [rolesRes, treeRes] = await Promise.all([
        axios.get('/api/roles'),
        axios.get('/api/unidades/tree'),
    ]);
    roles.value = rolesRes.data;
    unidadesFlat.value = flattenTree(treeRes.data);
}

function openCreate() {
    form.value = { username: '', nombre_completo: '', email: '', password: '', unidad_activa_id: unidadesFlat.value[0]?.id, role_ids: [] };
    formError.value = '';
    showForm.value = true;
}

async function guardarUsuario() {
    formError.value = '';
    try {
        await axios.post('/api/usuarios', {
            ...form.value,
            role_ids: form.value.role_ids.map(Number),
            unidad_activa_id: Number(form.value.unidad_activa_id),
        });
        showForm.value = false;
        await loadUsuarios();
    } catch (e) {
        formError.value = e.response?.data?.message ?? 'No se pudo crear el usuario.';
    }
}

function openTraslado(u) {
    trasladoUser.value = u;
    trasladoForm.value = { unidad_id: u.unidad_activa_id, motivo: '' };
}

async function ejecutarTraslado() {
    await axios.post(`/api/usuarios/${trasladoUser.value.id}/traslado`, {
        unidad_id: Number(trasladoForm.value.unidad_id),
        motivo: trasladoForm.value.motivo,
    });
    trasladoUser.value = null;
    await loadUsuarios();
}

onMounted(async () => {
    await loadMeta();
    await loadUsuarios();
});
</script>
