<?php

namespace Database\Seeders;

use App\Models\Catalogos\Productos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ProductosActualizarTaraSeeder extends Seeder {
    public function run(): void {
        $csv = storage_path('app/articulos.csv');
        if (!file_exists($csv)) {
            $this->command->error("No se encontró {$csv}");
            return;
        }

        $f = fopen($csv, 'r');
        if (!$f) {
            $this->command->error("No se pudo abrir {$csv}");
            return;
        }

        $this->command->info('Actualizando tara por nombre (B) y tara (E)...');

        $total = 0; $ok = 0; $sinMatch = 0; $invalid = 0;

        while (($row = fgetcsv($f)) !== false) {
            $total++;

            $nombre = isset($row[1]) ? trim($row[1]) : null;
            $taraRaw = $row[4] ?? null;

            if (!$nombre || $taraRaw === null || $taraRaw === '') {
                $invalid++;
                continue;
            }

            $tara = str_replace([' ', ','], ['', '.'], (string)$taraRaw);
            if (!is_numeric($tara)) { $invalid++; continue; }
            $tara = (float)$tara;

            $p = Productos::where('nombre', $nombre)->first();

            if (!$p) {
                $sinMatch++;
                Log::warning("Tara: no se encontró producto con nombre='{$nombre}'");
                continue;
            }

            $p->tara = $tara;
            $p->save();
            $ok++;
        }

        fclose($f);

        $this->command->info("Procesadas: {$total} | Actualizadas: {$ok} | Sin coincidencia: {$sinMatch} | Inválidas: {$invalid}");
    }
}
