<template>
    <AppLayout title="Modulos">
        <template #header-right>
            <MdButton :loading="IsLoading" @click="ToggleModal()">Registra Nuevo modulo</MdButton>
        </template>

        <AgGrid :initial-row-data="Modulos" :initial-column-defs="columnas" :pagination="true" height="70vh" />

        <MdDialogModal v-if="modal" :show="modal" @close="ToggleModal">
            <template #title>
                Crear Modulos
            </template>

            <template #content>
                <section ref="FormSection" class="grid grid-cols-2 space-y-4">
                    <MdTextInput v-model="form.nombre" id="nombre" label="Nombre" required :uppercase="true"
                        :error="form.errors.nombre" :success="!form.errors.nombre" />

                    <MdTextInput v-model="form.ruta" id="ruta" label="Ruta del módulo" required
                        :error="form.errors.ruta" :success="!form.errors.ruta" />

                    <MdSelectSearchInput id="categoria_id" name="categoria_id" v-model="form.categoria_id"
                        :options="ListaCategorias" label="Selecciona una categoria" :required="true"
                        helper="Categoria para seccionar los modulos" :error="form.errors.categoria_id"
                        :success="!form.errors.categoria_id" />

                    <MdSelectSearchInput id="icono" name="icono" v-model="form.icono" :options="ListaIconos"
                        label="Selecciona un icono" :required="true" helper="Icono para el modulo"
                        :error="form.errors.icono" :success="!form.errors.icono" />

                    <MdTextareaInput id="descripcion" name="descripcion" v-model="form.descripcion"
                        label="Descripción" class="col-span-2" required helper="Uso del módulo"
                        :error="form.errors.descripcion" :success="!form.errors.descripcion" />
                </section>
            </template>

            <template #footer>
                <div class="space-x-2">
                    <MdButton variant="primary" :loading="IsLoading" @click="Upsert()">
                        {{ IsEditMode ? 'Actualizar' : 'Registrar' }}
                    </MdButton>
                    <MdButton variant="dark" @click="ToggleModal()">Cancelar</MdButton>
                </div>
            </template>
        </MdDialogModal>

    </AppLayout>
</template>

<script setup>
    import { ref, inject, defineProps,onMounted } from 'vue'
    import { useForm } from '@inertiajs/vue3'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import AgGrid from '@/Components/Dependencies/AgGrid.vue'
    import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
    import MdTextareaInput from '@/Components/MaterialDesign/MdTextareaInput.vue'
    import MdButton from '@/Components/MaterialDesign/MdButton.vue'
    import MdLoadingScreen from '@/Components/MaterialDesign/MdLoadingScreen.vue'
    import MdDialogModal from '@/Components/MaterialDesign/MdDialogModal.vue'
    import MdSelectSearchInput from '@/Components/MaterialDesign/MdSelectSearchInput.vue'

    /* ========================== Props ========================== */
    const props = defineProps({
        Categorias: Object,
        Modulos: Object,
        ListaIconos: Object,
        ListaCategorias: Object,
    })

    /* ========================== Refs ========================== */
    const FormValidate = inject('FormValidate')
    const IsLoading = inject('IsLoading')
    const toast = inject('$toast')
    const IsEditMode = ref(false)
    const modal = ref(false)
    const FormSection  = ref(null)

    const form = useForm({
        id: null,
        categoria_id: null,
        nombre: '',
        ruta: '',
        descripcion: '',
        icono: '',
    })

    const columnas = [
        { headerName: 'Nombre', field: 'nombre' },
        { headerName: 'Ruta', field: 'ruta' },
        { headerName: 'Icono', field: 'icono' },
        { headerName: 'Descripción', field: 'descripcion' },
    ]

    /* ========================== Mounted ========================== */
    onMounted(() => {
    })

    /* ========================== Funciones ========================== */
    function ToggleModal() {
  modal.value=!modal.value
}

    const Upsert = () => {
        const t0 = performance.now();
        console.log('[Upsert] Inicio', t0.toFixed(2));

        if (!FormValidate(FormSection)) {
            console.log('[Upsert] Validación fallida');
            return;
        }

        const t1 = performance.now();
        console.log('[Upsert] Validación completada en', (t1 - t0).toFixed(2), 'ms');

        ToggleModal();
        const t2 = performance.now();
        console.log('[Upsert] Modal cerrado en', (t2 - t1).toFixed(2), 'ms');

        IsLoading.value = true;

        IsEditMode.value ? Update(t2) : Insert(t2);
    };

    const Insert = (startTime) => {
        console.log('[Insert] Enviando formulario...');

        form.post(route('Modulos.store'), {
            onStart: () => {
                console.log('[Insert] Inertia empezó la petición');
            },
            onSuccess: () => {
                const tEnd = performance.now();
                console.log('[Insert] Éxito en', (tEnd - startTime).toFixed(2), 'ms');

                form.reset();
                IsLoading.value = false;
                toast('Registro guardado correctamente', 'success');
            },
            onError: (e) => {
                const tErr = performance.now();
                console.error('[Insert] Error en', (tErr - startTime).toFixed(2), 'ms', e);

                IsLoading.value = false;
                toast('Ocurrió un error', 'danger');
            }
        });
    };

    const Update = (startTime) => {
        console.log('[Update] Enviando formulario (PUT)...');

        form.put(route('Modulos.update', form.id), {
            onStart: () => {
                console.log('[Update] Inertia empezó la petición');
            },
            onSuccess: () => {
                const tEnd = performance.now();
                console.log('[Update] Éxito en', (tEnd - startTime).toFixed(2), 'ms');

                form.reset();
                IsLoading.value = false;
                toast('Registro actualizado', 'success');
            },
            onError: () => {
                const tErr = performance.now();
                console.log('[Update] Error en', (tErr - startTime).toFixed(2), 'ms');

                IsLoading.value = false;
                toast('Ocurrió un error', 'danger');
            }
        });
    };

    const Edit = (locacion) => {
        form.reset();
        Object.assign(form, locacion);
        IsEditMode.value = true;
        ShowModal.value = true;
    };

    const Delete = (id) => {
        confirm(
            '¿Estás seguro?',
            'Esta acción no se puede deshacer.',
            'Sí, eliminar',
            'Cancelar',
            () => {
                form.delete(route('Modulos.destroy', id), {
                    onSuccess: () => {
                        toast('Registro eliminado', 'success');
                    },
                    onError: (e) => {
                        console.log('Ocurrió un error', e);
                    }
                });
            },
            () => {
                console.log('Acción cancelada');
            }
        );
    };
</script>
