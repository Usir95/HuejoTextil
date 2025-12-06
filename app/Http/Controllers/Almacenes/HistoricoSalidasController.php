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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Exports\SalidaAlmacenExport;
use App\Models\Almacenes\Salidas;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;


class HistoricoSalidasController extends Controller {

    public function index() {
        $Clientes = Clientes::Catalogo();

        return Inertia::render('Almacenes/Inventarios/HistoricoSalidas', compact(
            'Clientes',
        ));
    }

    public function FiltrarSalidas(Request $request) {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
        ]);

        $salidas = Salidas::with([
                'cliente',
                'usuario',
                'movimientos.producto',
                'movimientos.color',
                'movimientos.tipoCalidad',
            ])
            ->where('cliente_id', $validated['cliente_id'])
            ->orderByDesc('fecha_salida')
            ->orderByDesc('id')
            ->get();

        $data = $salidas->map(function ($s) {

            $detalle = $s->movimientos->map(function ($m) {
                return [
                    'id'            => $m->id,
                    'producto'      => $m->producto?->nombre,
                    'producto_id'   => $m->producto_id,
                    'color'         => $m->color?->nombre,
                    'color_id'      => $m->color_id,
                    'calidad'       => $m->tipoCalidad?->nombre,
                    'calidad_id'    => $m->tipo_calidad_id,
                    'num_tarjeta'   => $m->num_tarjeta,
                    'num_rollo'     => $m->num_rollo,
                    'cantidad'      => $m->cantidad,
                ];
            });

            return [
                'id'           => $s->id,
                'fecha'        => optional($s->fecha_salida)->format('Y-m-d'),
                'pedido_id'    => $s->pedido_id,
                'cliente'      => $s->cliente?->nombre,
                'total_rollos' => $s->total_rollos,
                'total_kgs'    => $s->total_kgs,
                'estatus'      => $s->estatus,
                'usuario'      => $s->usuario?->name,
                'detalle'      => $detalle,
            ];
        });

        return response()->json([
            'success' => true,
            'salidas' => $data,
        ]);
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

    public function ExpotarPedido(Request $request) {
        $entradas = $request->input('salidas', []);
        $resumen  = $request->input('resumen', []);

        $cliente  = $entradas[0]['cliente']['nombre'] ?? '';
        $articulo = $request->input('articulo', '');
        $fecha    = $entradas[0]['fecha_movimiento'] ?? null;

        return Excel::download(
            new SalidaAlmacenExport($entradas, $resumen, $cliente, $articulo, $fecha),
            'SalidaAlmacen-' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function GenerarPedido(Salidas $salida) {
        $salida->load([
            'cliente',
            'usuario',
            'movimientos.producto',
            'movimientos.color',
            'movimientos.tipoCalidad',
        ]);

        $empresaNombre   = 'HUEJOTEXTIL, S.A. de C.V.';
        $tituloDocumento = 'SALIDA DE ALMACÉN DE PRODUCTO TERMINADO';

        $articulo = optional($salida->movimientos->first()?->producto)->nombre ?? '';

        $pdf = Pdf::loadView('pdf.salidas.hoja_salida', [
            'salida'          => $salida,
            'empresaNombre'   => $empresaNombre,
            'tituloDocumento' => $tituloDocumento,
            'articulo'        => $articulo,
        ])->setPaper('letter', 'portrait');

        $nombreArchivo = 'Salida_' . $salida->id . '.pdf';

        return $pdf->stream($nombreArchivo);
        // si quieres descargar directo: return $pdf->download($nombreArchivo);
    }


}
