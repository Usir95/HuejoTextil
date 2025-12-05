<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Almacenes\Almacenes;
use App\Models\Almacenes\Movimientos;
use App\Models\Catalogos\Clientes;
use App\Models\Catalogos\Colores;
use App\Models\Catalogos\Productos;
use App\Models\Catalogos\TiposCalidades;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Inertia\Inertia;


class HistoricoSalidasController extends Controller {

    public function index() {
        $Productos       = Productos::Catalogo();
        $Colores         = Colores::Catalogo();
        $TiposCalidades  = TiposCalidades::Catalogo();
        $Almacenes       = Almacenes::Catalogo();
        $Clientes        = Clientes::Catalogo();

        $FechaLimite = Carbon::now()->subMonths(13)->startOfDay();

        $Salidas = Movimientos::with('cliente', 'producto')
            ->where('tipo_movimiento_id', 2)
            ->whereDate('fecha_movimiento', '>=', $FechaLimite)
            ->get();

        $Salidas->transform(function ($item) {
            $item->cantidad = (float) number_format($item->cantidad, 2, '.', '');
            return $item;
        });

        return Inertia::render('Almacenes/Inventarios/HistoricoSalidas', compact(
            'Clientes',
            'Salidas',
            'Productos',
            'Colores',
            'Clientes',
            'TiposCalidades',
        ));
    }

    public function FiltrarSalidas(Request $request) {
        $request->validate([
            'cliente_id'  => 'nullable',
            'num_tarjeta' => 'nullable',
        ]);

        $HistoricoSalidas = Movimientos::with('cliente', 'producto')
            ->where('tipo_movimiento_id', 2)
            ->when($request->cliente_id, function ($q) use ($request) {
                $q->where('cliente_id', $request->cliente_id);
            })
            ->when($request->num_tarjeta, function ($q) use ($request) {
                $q->where('num_tarjeta', $request->num_tarjeta);
            })
            ->orderByDesc('fecha_movimiento')
            ->get();

        $ResumenProductos = $HistoricoSalidas
            ->groupBy(fn($item) => $item->producto->nombre ?? 'Sin nombre')
            ->map(function ($items, $nombre) {
                $first = $items->first();

                return [
                    'item_id'  => $first->id,
                    'producto' => $nombre,
                    'rollos'   => $items->count(),
                    'total_kg' => round($items->sum('cantidad'), 2),
                ];
            })
            ->values();

        return [
            'salidas' => $HistoricoSalidas,
            'resumen' => $ResumenProductos,
        ];
    }

    public function ObtenerSalida(Request $request) {
        $data = $request->validate([
            'id' => 'required|integer',
        ]);

        $mov = Movimientos::with(['producto:id,nombre', 'color:id,nombre', 'cliente:id,nombre'])
            ->select(
                'id',
                'cliente_id',
                'num_tarjeta',
                'num_rollo',
                'producto_id',
                'color_id',
                'tipo_calidad_id',
                'cantidad',
                'peso_tara',
                'fecha_movimiento'
            )
            ->where('tipo_movimiento_id', 2)
            ->find($data['id']);

        if (!$mov) {
            return response()->json(['message' => 'Salida no encontrada'], 404);
        }

        return [
            'id'              => $mov->id,
            'cliente_id'      => $mov->cliente_id,
            'num_tarjeta'     => $mov->num_tarjeta,
            'num_rollo'       => $mov->num_rollo,
            'producto_id'     => $mov->producto_id,
            'color_id'        => $mov->color_id,
            'tipo_calidad_id' => $mov->tipo_calidad_id,
            'cantidad'        => (float) $mov->getRawOriginal('cantidad'),
            'peso_tara'       => (float) $mov->getRawOriginal('peso_tara'),
            'producto_label'  => $mov->producto?->nombre,
            'color_label'     => $mov->color?->nombre,
            'cliente_label'   => $mov->cliente?->nombre,
        ];
    }

    public function ExpotarPedido(Request $request): StreamedResponse {
        $entradas = $request->input('entradas', []);
        $resumen = $request->input('resumen', []);

        return response()->streamDownload(function () use ($entradas, $resumen) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Tarjeta', 'Cliente', 'Rollo', 'Producto', 'Cantidad', 'Calidad', 'Fecha']);

            foreach ($entradas as $item) {
                fputcsv($handle, [
                    $item['num_tarjeta'] ?? '',
                    $item['cliente']['nombre'] ?? '',
                    $item['num_rollo'] ?? '',
                    $item['producto']['nombre'] ?? '',
                    $item['cantidad'] ?? '',
                    ($item['tipo_calidad_id'] == 1 ? 'Buena' : 'Regular'),
                    $item['fecha_movimiento'] ?? '',
                ]);
            }

            fputcsv($handle, ['']);

            fputcsv($handle, ['Producto', 'Rollos', 'Total KG']);

            foreach ($resumen as $res) {
                fputcsv($handle, [
                    $res['producto'] ?? '',
                    $res['rollos'] ?? '',
                    $res['total_kg'] ?? '',
                ]);
            }

            fclose($handle);
        }, 'HistoricoEntradas.csv');
    }

}
