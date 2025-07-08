<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModule extends Command {
    protected $signature = 'make:module {nombre}';
    protected $description = 'Crea modelo, migración, controlador, seeder y vista Inertia';

    public function handle() {
        // 1. Normalizamos el input y separamos carpeta y archivo
        $input = str_replace('\\', '/', trim($this->argument('nombre')));
        $segments = explode('/', $input);

        $baseName = array_pop($segments); // último segmento es el nombre base
        $folderPath = implode('/', $segments); // carpeta de destino (puede estar vacía)

        $modelName = Str::pluralStudly($baseName); // nombre del modelo y del archivo .vue

        // 2. Generar modelo, migración y seeder
        $this->call('make:model', [
            'name' => $modelName,
            '--migration' => true,
            '--seed' => true,
        ]);

        // 3. Generar controlador
        $this->call('make:controller', [
            'name' => "{$modelName}Controller",
            '--resource' => true,
        ]);

        // 4. Definir ruta final de la carpeta
        $pagesPath = resource_path('js/Pages');
        $fullFolderPath = $folderPath ? $pagesPath . '/' . $folderPath : $pagesPath;

        // 5. Asegurar que la carpeta existe
        File::ensureDirectoryExists($fullFolderPath);

        // 6. Ruta final del archivo .vue
        $vueFile = $fullFolderPath . '/' . $modelName . '.vue';

        // 7. Si ya existe, preguntar si sobrescribir
        if (File::exists($vueFile)) {
            $this->warn("⚠️ El archivo ya existe: {$vueFile}");
            if (! $this->confirm('¿Deseas sobrescribirlo?')) {
                $this->info('⏹ Operación cancelada.');
                return;
            }
        }

        // 8. Contenido del archivo
        $contenidoVue = <<<VUE
            <template>
                <AppLayout title="{$modelName}">
                    <template #options>
                    </template>
                </AppLayout>
            </template>

            <script setup>
            import { ref, defineProps, inject, computed, onMounted } from 'vue';
            import AppLayout from '@/Layouts/AppLayout.vue';

            /* ============================================ Props ============================================ */
            const props = defineProps({});

            /* ============================================ Variables ============================================ */
            const toast = inject('\$toast');
            const loading = inject('\$loading');
            const items = ref([]);

            /* ============================================ Mounted ============================================ */
            onMounted(() => {
                // Fetch or initialize data here
            });

            /* ============================================ Functions ============================================ */
            </script>
            VUE;

        // 9. Crear archivo
        File::put($vueFile, $contenidoVue);
        $this->info("✅ Módulo creado correctamente en: {$vueFile}");
    }
}
