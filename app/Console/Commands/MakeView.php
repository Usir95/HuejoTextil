<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeView extends Command {
    protected $signature = 'make:view {nombre}';
    protected $description = 'Crea una vista Inertia en resources/js/Pages';

    public function handle() {
        $input = str_replace('\\', '/', trim($this->argument('nombre')));
        $segments = explode('/', $input);

        $baseName = array_pop($segments); // Nombre de la vista
        $folderPath = implode('/', $segments); // Carpeta (puede estar vacía)

        $viewName = Str::pluralStudly($baseName);              // Ejemplo → Ejemplos
        $fullPath = resource_path("js/Pages/{$folderPath}");

        File::ensureDirectoryExists($fullPath);

        $filePath = "{$fullPath}/{$viewName}.vue";

        if (File::exists($filePath)) {
            $this->warn("⚠️ La vista ya existe: {$filePath}");
            if (! $this->confirm('¿Deseas sobrescribirla?')) {
                $this->info('⏹ Operación cancelada.');
                return;
            }
        }

        $contenidoVue =
        <<<VUE
            <template>
                <AppLayout title="{$viewName}">
                    <template #header-right>
                        <MdButton @click="ToggleModal()">Nuevo</MdButton>
                    </template>

                    <AgGrid
                        :initial-row-data="items"
                        :initial-column-defs="columnas"
                        :pagination="true"
                        height="70vh"
                    />

                    <MdDialogModal v-if="modal" :show="modal" @close="ToggleModal">
                        <template #title>
                            Crear {$viewName}
                        </template>

                        <template #content>
                            <section class="space-y-4">
                                <MdTextInput v-model="form.nombre" label="Nombre" required />
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
                {$viewName}: Object
            })

            /* ========================== Refs ========================== */
            const toast = inject('\$toast')
            const FormValidate = inject('FormValidate')
            const IsLoading = ref(false);
            const IsEditMode = ref(false)
            const modal = ref(false)
            const FormSection  = ref(null)

            const form = useForm({
                id: '',
                nombre: ''
            })

            const columnas = [
                { headerName: 'Nombre', field: 'nombre' },
            ]

            const items = ref(props.{$viewName})

            /* ========================== Funciones ========================== */
            const ToggleModal = () => {
                modal.value = !modal.value
            }

            const Upsert = () => {
                if (!FormValidate(FormSection)) {
                    console.log('[Upsert] Validación fallida');
                    return;
                }
                ToggleModal();
                IsLoading.value = true;
                IsEditMode.value ? Update() : Insert();
            };

            const Insert = (startTime) => {
                form.post(route('{$viewName}.store'), {
                    onStart: () => {
                        console.log('[Insert] Inertia empezó la petición');
                    },
                    onSuccess: () => {
                        form.reset();
                        IsLoading.value = false;
                        toast('Registro guardado correctamente', 'success');
                    },
                    onError: (e) => {
                        IsLoading.value = false;
                        toast('Ocurrió un error', 'danger');
                    }
                });
            };

            const Update = () => {
                form.put(route('{$viewName}.update', form.id), {
                    onSuccess: () => {
                        form.reset();
                        IsLoading.value = false;
                        toast('Registro actualizado', 'success');
                    },
                    onError: () => {
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
                        form.delete(route('{$viewName}.destroy', id), {
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
        VUE;

        File::put($filePath, $contenidoVue);

        $this->info("✅ Vista creada correctamente: Pages/{$folderPath}/{$viewName}.vue");
    }
}
