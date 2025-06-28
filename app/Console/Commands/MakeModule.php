<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeModule extends Command {
    protected $signature = 'make:modulo {nombre}';
    protected $description = 'Crea modelo, migración, controlador, factory, seeder y vista Inertia';

    public function handle() {
        $nombre = Str::studly($this->argument('nombre'));
        $plural = Str::pluralStudly($nombre);

        // Modelo con migración, factory y seeder
        $this->call('make:model', [
            'name' => $nombre,
            '--migration' => true,
            // '--factory' => true,
            '--seed' => true,
        ]);

        // Controlador resource
        $this->call('make:controller', [
            'name' => "{$nombre}Controller",
            '--resource' => true,
        ]);

        // Crear carpeta y vista Inertia
        $rutaVista = resource_path("js/Pages/{$plural}");
        File::ensureDirectoryExists($rutaVista);

        $contenidoVue =
            <<<VUE
                <template>
                    <AppLayout title="{$plural}">
                        <template #options>
                        </template>
                    </AppLayout>
                </template>

                <script setup>
                import { ref, defineProps, inject, computed, onMounted } from 'vue';
                import AppLayout from '@/Layouts/AppLayout.vue';

                /* ============================================ Props ============================================ */
                const props = defineProps({

                });

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

        File::put("{$rutaVista}/{$nombre}.vue", $contenidoVue);

        $this->info("✅ Módulo {$nombre} creado con éxito con vista Inertia.");
    }
}
