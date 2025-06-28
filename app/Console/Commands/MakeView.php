<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeView extends Command {
    protected $signature = 'make:view {nombre}';
    protected $description = 'Crea una vista Inertia en resources/js/Pages';

    public function handle() {
        $nombre = Str::studly($this->argument('nombre'));
        $plural = Str::pluralStudly($nombre);
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
                const props = defineProps({ });

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

        $this->info("✅ Vista {$nombre}.vue creada en Pages/{$plural}");
    }
}
