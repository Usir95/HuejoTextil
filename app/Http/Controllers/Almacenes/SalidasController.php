<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Almacenes\Inventarios;
use App\Models\Almacenes\Movimientos;
use App\Models\Almacenes\Pedidos;
use App\Models\Almacenes\Salidas;
use App\Models\Catalogos\Clientes;
use App\Models\Catalogos\Productos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class SalidasController extends Controller {

    public function index() {
        $Clientes = Clientes::Catalogo();
        return Inertia::render('Almacenes/Salidas', compact('Clientes'));
    }

    public function BuscarPedidos(Request $request) {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
        ]);

        $pedidos = Pedidos::where('cliente_id', $request->cliente_id)
            ->whereNotIn('estado_pedido', ['completado', 'cancelado'])
            ->orderBy('fecha_pedido', 'desc')
            ->get();

        $data = $pedidos->map(function ($p) {
            $detalle = collect($p->detalle_pedido ?? [])->map(function ($item) {
                $producto = Productos::find($item['producto_id']);
                return [
                    'producto_id' => $item['producto_id'],
                    'producto'    => $producto?->nombre ?? 'Producto desconocido',
                    'cantidad'    => $item['cantidad'],
                ];
            });

            return [
                'id'             => $p->id,
                'fecha_pedido'   => $p->fecha_pedido,
                'estado_pedido'  => $p->estado_pedido,
                'plazo_pago'     => $p->plazo_pago,
                'condiciones'    => $p->condiciones,
                'observaciones'  => $p->observaciones,
                'detalle'        => $detalle,
            ];
        });

        return response()->json([
            'success' => true,
            'pedidos' => $data,
        ]);
    }

    public function BuscarMovimiento($movimiento_id) {
        $Movimiento = Movimientos::with(['producto', 'color', 'tipoCalidad', 'tipoUnidad', 'almacen'])
            ->find($movimiento_id);

        $Inventario = Inventarios::where('producto_id', $Movimiento->producto_id)
            ->where('color_id', $Movimiento->color_id)
            ->where('tipo_calidad_id', $Movimiento->tipo_calidad_id)
            ->where('almacen_id', $Movimiento->almacen_id)
            ->first();

        $Movimiento->cantidad_disponible = $Inventario?->cantidad ?? 0;

        return $Movimiento;
    }

    public function RegistrarSalidaLote(Request $request) {
        $data = $request->validate([
            'movimiento_ids'   => 'required|array|min:1',
            'movimiento_ids.*' => 'required|integer|distinct|exists:movimientos,id',
            'pedido_id'        => 'nullable|integer|exists:pedidos,id',
        ]);

        try {

            return DB::transaction(function () use ($data) {

                $entradas = Movimientos::whereIn('id', $data['movimiento_ids'])
                    ->where('tipo_movimiento_id', 1)
                    ->get();

                if ($entradas->count() !== count($data['movimiento_ids'])) {
                    throw new \Exception("Uno o más movimientos no son entradas válidas.");
                }

                $clienteId = $entradas->first()->cliente_id;
                $almacenId = $entradas->first()->almacen_id;

                // Totales
                $totalKgs    = $entradas->sum('cantidad');
                $totalRollos = $entradas->count();

                $salida = Salidas::create([
                    'cliente_id'   => $clienteId,
                    'pedido_id'    => $data['pedido_id'] ?? null,
                    'fecha_salida' => now()->toDateString(),
                    'almacen_id'   => $almacenId,
                    'usuario_id'   => Auth::id(),
                    'total_kgs'    => $totalKgs,
                    'total_rollos' => $totalRollos,
                ]);

                foreach ($entradas as $mov) {

                    $inventario = Inventarios::where('producto_id', $mov->producto_id)
                        ->where('color_id', $mov->color_id)
                        ->where('tipo_calidad_id', $mov->tipo_calidad_id)
                        ->where('almacen_id', $almacenId)
                        ->lockForUpdate()
                        ->first();

                    if (!$inventario) {
                        throw new \Exception("Inventario no encontrado para el rollo {$mov->num_rollo}");
                    }

                    if ($inventario->cantidad < $mov->cantidad) {
                        throw new \Exception("Inventario insuficiente para el rollo {$mov->num_rollo}");
                    }

                    $inventario->update([
                        'cantidad' => $inventario->cantidad - $mov->cantidad
                    ]);

                    // Crear movimiento de salida
                    Movimientos::create([
                        'cliente_id'         => $mov->cliente_id,
                        'pedido_id'          => $data['pedido_id'] ?? null,
                        'num_tarjeta'        => $mov->num_tarjeta,
                        'num_rollo'          => $mov->num_rollo,
                        'peso_tara'          => $mov->peso_tara,
                        'producto_id'        => $mov->producto_id,
                        'color_id'           => $mov->color_id,
                        'tipo_calidad_id'    => $mov->tipo_calidad_id,
                        'cantidad'           => $mov->cantidad,
                        'fecha_movimiento'   => now(),
                        'tipo_movimiento_id' => 2,
                        'usuario_id'         => Auth::id(),
                        'almacen_id'         => $mov->almacen_id,
                        'salida_id'          => $salida->id,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'salida_id' => $salida->id,
                    'message' => "Salida registrada correctamente.",
                ]);

            });

        } catch (\Throwable $e) {

            Log::error('Error al registrar salida lote', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }



}
