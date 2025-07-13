<?php

namespace Database\Seeders;

use App\Models\Catalogos\TiposProductos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalogo = [
            'TELA',
            'HILO',
        ];
        foreach ($catalogo as $nombre) {
            TiposProductos::firstOrCreate(
                ['nombre' => $nombre]
            );
        }
    }
}
