<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void {
        $this->call([
            CategoriasModulosSeeder::class,
            EmpleadosSeeder::class,
            ColoresSeeder::class,
            ClientesSeeder::class,
            TiposPedidosSeeder::class,
            TiposCalidadesSeeder::class,
            TiposProductosSeeder::class,
            TiposUnidadesSeeder::class,
            UnidadesSeeder::class,
            ProductosSeeder::class,
        ]);
    }
}
