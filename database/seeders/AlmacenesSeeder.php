<?php

namespace Database\Seeders;

use App\Models\Almacenes\Almacenes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlmacenesSeeder extends Seeder {

    public function run(): void {
        Almacenes::create([
            'nombre' => 'Producto Terminado',
        ]);

        Almacenes::create([
            'nombre' => 'Materia Prima',
        ]);
    }
}
