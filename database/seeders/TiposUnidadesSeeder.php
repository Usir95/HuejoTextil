<?php

namespace Database\Seeders;

use App\Models\Catalogos\TiposUnidades;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposUnidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalogo = [
            'Peso',
            'Distancia'
        ];
        foreach ($catalogo as $nombre) {
            TiposUnidades::firstOrCreate(
                ['nombre' => $nombre]
            );
        }
    }
}
