<template>
    <AppLayout title="Roles y permisos del sistema">
        <section class="mt-4">
            <AgGrid
                :initial-row-data="roles"
                :initial-column-defs="columnas"
                @cell-clicked="onCellClicked"
                :pagination="true"
                height="80vh"
            />
        </section>

        <MdDialogModal :show="showModalRol" @close="showModalRol = false">
            <template #title> Configurar permisos del rol </template>

            <template #content>
                <div v-if="rolSeleccionado">
                    <h2 class="text-xl font-semibold mb-4">Permisos para: {{ rolSeleccionado.name }}</h2>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 max-h-[60vh] overflow-y-auto pr-2">
                        <div v-for="(grupo, modulo) in PermisosPorModulo" :key="modulo" class="border rounded-xl p-3 bg-[var(--color-text)] text-white">
                            <h3 class="font-extrabold text-xl text-center mb-2">{{ modulo }}</h3>
                            <div class="space-y-2">
                                <label v-for="permiso in grupo" :key="permiso.id" class="flex items-center justify-center gap-2">
                                    <input
                                        type="checkbox"
                                        :value="permiso.name"
                                        v-model="permisosSeleccionados"
                                    />
                                    <span>{{ permiso.name.replace(modulo + '.', '') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-gray-500">Selecciona un rol para ver sus permisos.</div>
            </template>

            <template #footer>
                <MdButton variant="primary" :loading="isLoading" @click="AsignarPermisos()">
                    Guardar cambios
                </MdButton>
                <MdButton variant="dark" @click="showModalRol = false">
                    Cancelar
                </MdButton>
            </template>
        </MdDialogModal>
    </AppLayout>
</template>

<script setup>
import { ref, defineProps, inject, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AgGrid from '@/Components/Dependencies/AgGrid.vue'
import MdDialogModal from '@/Components/MaterialDesign/MdDialogModal.vue';
import MdButton from '@/Components/MaterialDesign/MdButton.vue';

/* ============================================ Props ============================================ */
const props = defineProps({
    Roles: Object,
    Permisos: Object,
})

/* ============================================ Refs y states ============================================ */
const toast = inject('$toast');
const FormSection = ref(null);
const isLoading = ref(false);
const rolSeleccionado = ref(null);
const permisosSeleccionados = ref([]);
const showModalRol = ref(false);

const roles = ref([...props.Roles]);

/* ============================================ Watchers ============================================ */
watch(() => props.Roles, (nuevosRoles) => {
    roles.value = [...nuevosRoles];
});

/* ============================================ Columnas AgGrid ============================================ */

const columnas = [
    {
        headerName: 'ID',
        field: 'id',
        filter: 'agTextColumnFilter',
        minWidth: 100,
        flex: 1,
    },
    {
        headerName: 'Rol',
        field: 'name',
        filter: 'agTextColumnFilter',
        minWidth: 150,
        flex: 1,
    },
    {
        headerName: 'Acciones',
        field: 'acciones',
        pinned: 'right',
        minWidth: 180,
        cellRenderer: (params) => {
            return `
                <button data-action="SeleccionarRol" data-id="${params.data.id}" class="text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fas fa-cogs mr-1"></i> Configurar permisos
                </button>
            `;
        },
        sortable: false,
        filter: false,
    },
];

/* ============================================ Eventos AgGrid ============================================ */
const onCellClicked = (event) => {
    const target = event.event.target.closest('button');
    const action = target?.dataset.action;

    if (action === 'SeleccionarRol') {
        seleccionarRol(event.data);
    }
};

/* ============================================ Lógica permisos por módulo ============================================ */
const PermisosPorModulo = computed(() => {
    const agrupado = {};
    props.Permisos.forEach(p => {
        const [modulo, permiso] = p.name.split('.');
        if (!agrupado[modulo]) agrupado[modulo] = [];
        agrupado[modulo].push(p);
    });
    return agrupado;
});

/* ============================================ Funciones principales ============================================ */
const seleccionarRol = (data) => {
    rolSeleccionado.value = data;
    permisosSeleccionados.value = data.permissions?.map(p => p.name) ?? [];
    showModalRol.value = true;
};

const AsignarPermisos = async () => {
    try {
        isLoading.value = true;
        await router.post('RolesPermisos/AsignarPermisos', {
            rol_id: rolSeleccionado.value.id,
            permisos: permisosSeleccionados.value,
        });
        toast('Permisos actualizados correctamente', 'success');
        showModalRol.value = false;

        router.reload({ only: ['Roles'] });
    } catch (error) {
        toast('Error al actualizar permisos', 'error');
    } finally {
        isLoading.value = false;
    }
};
</script>

