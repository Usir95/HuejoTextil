<?php

namespace Database\Seeders;

use App\Models\Catalogos\TiposCalidades;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposCalidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalogo = [
            'Buena',
            'Segunda'
        ];
        foreach ($catalogo as $nombre) {
            TiposCalidades::firstOrCreate(
                ['nombre' => $nombre]
            );
        }
    }
}
