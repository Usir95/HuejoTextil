<?php

namespace Database\Seeders;

use App\Models\Catalogos\Clientes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage; // Necesario para interactuar con el sistema de archivos
use Illuminate\Support\Facades\Log;     // Para registrar mensajes de depuración/error

class ClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $csvFile = storage_path('app/clientes.csv');

        // Verifica si el archivo CSV existe
        if (!file_exists($csvFile)) {
            $this->command->error("El archivo CSV no se encontró en  {$csvFile}");
            $this->command->info("Por favor, asegúrate de que el archivo esté en 'storage/app/' o ajusta la ruta en el seeder.");
            Log::error("ClientesSeeder: Archivo CSV no encontrado en {$csvFile}");
            return; // Detiene la ejecución del seeder si el archivo no existe
        }

        // Abre el archivo CSV en modo lectura
        $file = fopen($csvFile, 'r');

        // Verifica si el archivo se pudo abrir correctamente
        if ($file === false) {
            $this->command->error("No se pudo abrir el archivo CSV: {$csvFile}. Verifica los permisos de archivo.");
            Log::error("ClientesSeeder: No se pudo abrir el archivo CSV en {$csvFile}");
            return;
        }

        $this->command->info('Iniciando el seeder de Clientes desde CSV...');
        $processedCount = 0;

        // Itera sobre cada fila del archivo CSV
        while (($row = fgetcsv($file)) !== false) {

            $clave = $row[0];
            $nombre = $row[1];


            if (!empty($clave) && !empty($nombre)) {
                try {
                    Clientes::firstOrCreate(
                        ['clave' => $clave],
                        ['nombre' => $nombre]
                    );
                    $processedCount++;
                } catch (\Exception $e) {
                    $this->command->error("Error al procesar la fila para cliente (Clave: {$clave}, Nombre: {$nombre}): " . $e->getMessage());
                    Log::error("ClientesSeeder: Error al procesar fila " . implode(', ', $row) . ": " . $e->getMessage());
                }
            } else {
                $this->command->warn("Fila ignorada debido a datos incompletos (clave o nombre vacíos/nulos): " . implode(', ', $row));
                Log::warning("ClientesSeeder: Fila ignorada por datos incompletos: " . implode(', ', $row));
            }
        }

        // Cierra el archivo
        fclose($file);

        $this->command->info("Seeder de Clientes completado. Se procesaron {$processedCount} registros.");
    }
}
