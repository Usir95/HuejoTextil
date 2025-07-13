<template>
    <AppLayout title="Bitacoras">

        <section class="mx-1 my-4">
            <AgGrid
                :initial-row-data="BitacoraAudit"
                :initial-column-defs="columnas"
                @cell-clicked="onCellClicked"
                height="90vh"
            />
        </section>

        <MdDialogModal v-if="modal" :show="modal" @close="ToggleModal">
            <template #title>
                Crear Bitacoras
            </template>

            <template #content>
                <section class="text-sm">
                    <div class="flex flex-col space-y-2">
                        <div>
                            <span class="font-semibold">Usuario: </span>
                            <span>{{ DetalleBitacora.user ? DetalleBitacora.user.name : 'Desconocido' }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Acción: </span>
                            <span class="px-2 py-1 text-blue-700 bg-blue-200 rounded-full">
                                {{ DetalleBitacora.event }}
                            </span>
                        </div>
                        <div>
                            <span class="font-semibold">Modelo afectado: </span>
                            <span>{{ DetalleBitacora.auditable_type }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Fecha: </span>
                            <span>{{ new Date(DetalleBitacora.created_at).toLocaleString() }}</span>
                        </div>
                        <div v-if="DetalleBitacora.old_values && DetalleBitacora.new_values">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 rounded bg-[var(--color-tertiary)] dark:bg-[#1e293b]">
                                    <h3 class="text-[var(--color-primary)] font-bold mb-2">🕑 Valores anteriores</h3>
                                    <ul class="pl-4 list-disc space-y-1 text-gray-800 dark:text-gray-300">
                                        <li v-for="(value, key) in DetalleBitacora.old_values" :key="key">
                                            <strong>{{ key }}:</strong> {{ value }}
                                        </li>
                                    </ul>
                                </div>

                                <div class="p-4 rounded bg-[var(--color-tertiary)] dark:bg-[#1e293b]">
                                    <h3 class="text-[var(--color-primary)] font-bold mb-2">🚀 Nuevos valores</h3>
                                    <ul class="pl-4 list-disc space-y-1 text-gray-800 dark:text-gray-300">
                                        <li v-for="(value, key) in DetalleBitacora.new_values" :key="key">
                                            <strong>{{ key }}:</strong> {{ value }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </template>

            <template #footer>
                <div class="space-x-2">
                    <MdButton variant="dark" @click="ToggleModal()">Cerrar</MdButton>
                </div>
            </template>
        </MdDialogModal>
    </AppLayout>
</template>

<script setup>
    import { ref, inject, defineProps } from 'vue'
    import { useForm } from '@inertiajs/vue3'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import AgGrid from '@/Components/Dependencies/AgGrid.vue'
    import {
        MdButton,
        MdDialogModal,
        MdTextInput
    } from '@/Components/MaterialDesign'

    /* ========================== Props ========================== */
    const props = defineProps({
        BitacoraAudit: Object
    })

    /* ========================== Refs ========================== */
    const toast = inject('$toast')
    const modal = ref(false)
    const DetalleBitacora  = ref({})

    const form = useForm({
        id: '',
    })

    const columnas = [
        { headerName: 'Usuario', field: 'user.nombre', filter: 'agTextColumnFilter', minWidth: 150, flex: 1 },
        { headerName: 'Acción', field: 'event', filter: 'agTextColumnFilter',
            cellRenderer: (params) => {
                return `
                    <span class="px-3 py-1 text-xs text-blue-700 bg-blue-200 rounded-full">
                        ${params.value}
                    </span>
                `;
            }, minWidth: 100, flex: 1
        },
        { headerName: 'Modelo', field: 'auditable_type', filter: 'agTextColumnFilter', minWidth: 150, flex: 1 },
        {
            headerName: 'Fecha', field: 'created_at', filter: 'agDateColumnFilter', minWidth: 150, flex: 1,
            valueFormatter: (params) => {
                return new Date(params.value).toLocaleString();
            }
        },
        {
            headerName: 'Acciones',
            field: 'acciones',
            pinned: 'right',
            maxWidth: 130,
            cellRenderer: (params) => {
                return `
                    <div class="flex gap-2">
                        <button data-action="VerDetalle" class="text-indigo-500 hover:text-indigo-900 cursor-pointer" data-id="${params.data}" title="VerDetalle">
                            <i class="fa-regular fa-eye"></i> Ver Detalle
                        </button>
                    </div>
                `;
            },
            sortable: false,
            filter: false,
        }
    ]

    /* ========================== Funciones ========================== */
    const ToggleModal = () => {
        modal.value = !modal.value
    }

    const onCellClicked = (event) => {
        const target = event.event.target.closest('button');
        const action = target?.dataset.action;
        console.log(event.data);

        if (action == 'VerDetalle') {
            VerDetalles(event.data);
        }
    }

    const VerDetalles = (data) => {
        ToggleModal();
        DetalleBitacora.value = data;
    };
</script>
