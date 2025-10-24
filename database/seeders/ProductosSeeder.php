<?php

namespace Database\Seeders;

use App\Models\Catalogos\Productos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
class ProductosSeeder extends Seeder {
    public function run(): void {
        $csvFile = storage_path('app/articulos.csv');

        // Verifica si el archivo CSV existe
        if (!file_exists($csvFile)) {
            $this->command->error("El archivo CSV no se encontró en  {$csvFile}");
            $this->command->info("Por favor, asegúrate de que el archivo esté en 'storage/app/' o ajusta la ruta en el seeder.");
            Log::error("ArticulosSeeder: Archivo CSV no encontrado en {$csvFile}");
            return; // Detiene la ejecución del seeder si el archivo no existe
        }

        // Abre el archivo CSV en modo lectura
        $file = fopen($csvFile, 'r');

        // Verifica si el archivo se pudo abrir correctamente
        if ($file === false) {
            $this->command->error("No se pudo abrir el archivo CSV: {$csvFile}. Verifica los permisos de archivo.");
            Log::error("ArticulosSeeder: No se pudo abrir el archivo CSV en {$csvFile}");
            return;
        }

        $this->command->info('Iniciando el seeder de Articulos desde CSV...');
        $processedCount = 0;

        // Itera sobre cada fila del archivo CSV
        while (($row = fgetcsv($file)) !== false) {

            $codigo = $row[0];
            $nombre = $row[1];
            $tipo_producto_id = $row[2];
            $unidad_id = $row[3];


            if (!empty($nombre)) {
                try {
                    Productos::firstOrCreate(
                        [ 'codigo' => $codigo, 'nombre' => $nombre, 'tipo_producto_id' => $tipo_producto_id, 'unidad_id' => $unidad_id ]
                    );
                    $processedCount++;
                } catch (\Exception $e) {
                    $this->command->error("Error al procesar la fila para Articulos (Nombre: {$nombre}): " . $e->getMessage());
                    Log::error("ArticulosSeeder: Error al procesar fila " . implode(', ', $row) . ": " . $e->getMessage());
                }
            } else {
                $this->command->warn("Fila ignorada debido a datos incompletos (nombre vacíos/nulos): " . implode(', ', $row));
                Log::warning("ArticulosSeeder: Fila ignorada por datos incompletos: " . implode(', ', $row));
            }
        }

        // Cierra el archivo
        fclose($file);

        $this->command->info("Seeder de Articulos completado. Se procesaron {$processedCount} registros.");
    }
}
