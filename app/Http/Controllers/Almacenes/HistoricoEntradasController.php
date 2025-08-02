<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Almacenes\Movimientos;
use App\Models\Catalogos\Clientes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class HistoricoEntradasController extends Controller {

    public function index() {
        $Clientes = Clientes::Catalogo();

        $FechaLimite = Carbon::now()->subMonths(13)->startOfDay();

        $Entradas = Movimientos::with('cliente', 'producto')
            ->where('tipo_movimiento_id', 1)
            ->whereDate('fecha_movimiento', '>=', $FechaLimite)
            ->get();

        return Inertia::render('Almacenes/Inventarios/HistoricoEntradas', compact('Clientes', 'Entradas'));
    }

    public function FiltrarEntradas(Request $request) {
        $request->validate([
            'cliente_id'   => 'nullable',
            'num_tarjeta'  => 'nullable',
        ]);

        $HistoricoEntradas = Movimientos::with('cliente', 'producto')
            ->where('tipo_movimiento_id', 1)
            ->where('cliente_id', $request->cliente_id)
            ->where('num_tarjeta', $request->num_tarjeta)
            ->orderByDesc('fecha_movimiento')
            ->get();

        $ResumenProductos = $HistoricoEntradas->groupBy(fn($item) => $item->producto->nombre ?? 'Sin nombre')->map(function ($items, $nombre) {
            return [
                'producto'  => $nombre,
                'rollos'    => $items->count(),
                'total_kg'  => round($items->sum('cantidad'), 2),
            ];
        })->values();

        return $data = [
            'entradas' => $HistoricoEntradas,
            'resumen'  => $ResumenProductos,
        ];
    }

}
