<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModule extends Command {
    protected $signature = 'make:module {nombre}';
    protected $description = 'Crea modelo, migración, controlador, seeder y vista Inertia';

    public function handle() {
        $input = str_replace('\\', '/', trim($this->argument('nombre')));
        $segments = explode('/', $input);

        $baseName = array_pop($segments); // Ejemplo
        $folderPath = implode('/', $segments);

        $modelName = Str::studly($baseName);            // Ejemplo
        $modelPlural = Str::pluralStudly($modelName);   // Ejemplos

        // 1. Crear modelo + migración + seeder
        $this->call('make:model', [
            'name' => $modelName,
            '--migration' => true,
            '--seed' => true,
        ]);

        // 2. Crear controlador
        $this->call('make:controller', [
            'name' => "{$modelPlural}Controller",
            '--resource' => true,
        ]);

        // 3. Crear carpeta
        $pagesPath = resource_path('js/Pages');
        $fullFolderPath = $folderPath ? $pagesPath . '/' . $folderPath : $pagesPath;
        File::ensureDirectoryExists($fullFolderPath);

        // 4. Ruta final del archivo .vue
        $vueFile = "{$fullFolderPath}/{$modelPlural}.vue";

        if (File::exists($vueFile)) {
            $this->warn("⚠️ El archivo ya existe: {$vueFile}");
            if (! $this->confirm('¿Deseas sobrescribirlo?')) {
                $this->info('⏹ Operación cancelada.');
                return;
            }
        }

        // 5. Contenido del .vue
        $contenidoVue =
        <<<VUE
            <template>
                <AppLayout title="{$modelPlural}">
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
                            Crear {$modelName}
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
                {$modelPlural}: Object
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

            const items = ref(props.{$modelPlural})

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
                form.post(route('Modulos.store'), {
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
                form.put(route('Modulos.update', form.id), {
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
        VUE;

        File::put($vueFile, $contenidoVue);
        $this->info("✅ Módulo creado correctamente en: {$vueFile}");
    }
}
