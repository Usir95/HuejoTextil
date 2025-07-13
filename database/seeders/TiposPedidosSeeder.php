<?php

namespace Database\Seeders;

use App\Models\Catalogos\TiposPedidos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposPedidosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalogo = [
            'Normal',
            'Reproceso'
        ];
        foreach ($catalogo as $nombre) {
            TiposPedidos::firstOrCreate(
                ['nombre' => $nombre]
            );
        }
    }
}
