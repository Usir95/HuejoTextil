<?php

namespace Database\Seeders;

use App\Models\Catalogos\TiposMovimientos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposMovimientosSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        TiposMovimientos::create([
            'nombre' => 'Ingreso',
        ]);

        TiposMovimientos::create([
            'nombre' => 'Salida',
        ]);
    }
}
