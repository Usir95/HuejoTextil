<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Almacenes\Almacenes;
use App\Models\Almacenes\Inventarios;
use App\Models\Almacenes\Movimientos;
use App\Models\Catalogos\Clientes;
use App\Models\Catalogos\Colores;
use App\Models\Catalogos\Productos;
use App\Models\Catalogos\TiposCalidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class EntradasController extends Controller {

    public function index() {
        $Productos       = Productos::Catalogo();
        $Colores         = Colores::Catalogo();
        $TiposCalidades  = TiposCalidades::Catalogo();
        $Almacenes       = Almacenes::Catalogo();
        $Clientes        = Clientes::Catalogo();


        return Inertia::render('Almacenes/Entradas', compact(
            'Productos',
            'Colores',
            'TiposCalidades',
            'Almacenes',
            'Clientes',
        ));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'cliente_id'      => 'required',
            'num_tarjeta'     => 'required|numeric',
            'num_rollo'       => 'nullable|numeric',
            'peso_tara'       => 'nullable|numeric|min:0.01',
            'producto_id'     => 'required',
            'color_id'        => 'required',
            'tipo_calidad_id' => 'required',
            'cantidad'        => 'required|numeric|min:0.01',
        ]);

        $cantidad = round((float) $data['cantidad'], 3);

        try {
            $movimiento = DB::transaction(function () use ($data, $cantidad) {

                if (!empty($data['num_rollo'])) {
                    $numRolloFinal = $data['num_rollo'];
                } else {
                    $ultimoRollo = Movimientos::where('cliente_id', $data['cliente_id'])
                        ->where('num_tarjeta', $data['num_tarjeta'])
                        ->orderByDesc('num_rollo')
                        ->value('num_rollo');

                    $numRolloFinal = $ultimoRollo
                        ? str_pad(((int) $ultimoRollo) + 1, 4, '0', STR_PAD_LEFT)
                        : '0001';
                }

                $movimiento = Movimientos::create([
                    'cliente_id'         => $data['cliente_id'],
                    'num_tarjeta'        => $data['num_tarjeta'],
                    'num_rollo'          => $numRolloFinal,
                    'peso_tara'          => $data['peso_tara'] ?? 0,
                    'producto_id'        => $data['producto_id'],
                    'color_id'           => $data['color_id'],
                    'tipo_calidad_id'    => $data['tipo_calidad_id'],
                    'cantidad'           => $cantidad,
                    'fecha_movimiento'   => now(),
                    'tipo_movimiento_id' => 1,
                    'usuario_id'         => Auth::id(),
                    'almacen_id'         => 1,
                ]);

                if (!$movimiento) {
                    throw new \Exception('No se pudo crear el movimiento');
                }

                Log::info('Movimiento creado correctamente', ['id' => $movimiento->id]);

                $inventario = Inventarios::where('producto_id', $data['producto_id'])
                    ->where('almacen_id', 1)
                    ->where('color_id', $data['color_id'])
                    ->where('tipo_calidad_id', $data['tipo_calidad_id'])
                    ->lockForUpdate()
                    ->first();

                if ($inventario) {
                    $inventario->update([
                        'cantidad' => DB::raw("cantidad + {$cantidad}")
                    ]);
                } else {
                    Inventarios::create([
                        'producto_id'     => $data['producto_id'],
                        'almacen_id'      => 1,
                        'color_id'        => $data['color_id'],
                        'tipo_calidad_id' => $data['tipo_calidad_id'],
                        'cantidad'        => $cantidad,
                    ]);
                }

                return $movimiento;
            });

            // === Registro en archivo CSV ===
            $logDir = storage_path('app/logs');
            if (!file_exists($logDir)) {
                mkdir($logDir, 0777, true);
            }

            $file = $logDir . '/movimientos.csv';

            // Si el archivo no existe, crea encabezados
            if (!file_exists($file)) {
                $headers = [
                    'fecha', 'id', 'cliente_id', 'num_tarjeta', 'num_rollo',
                    'producto_id', 'color_id', 'tipo_calidad_id',
                    'cantidad', 'peso_tara', 'usuario_id'
                ];
                $fh = fopen($file, 'w');
                fputcsv($fh, $headers);
                fclose($fh);
            }

            // Añade la fila
            $fh = fopen($file, 'a');
            fputcsv($fh, [
                now()->toDateTimeString(),
                $movimiento->id,
                $movimiento->cliente_id,
                $movimiento->num_tarjeta,
                $movimiento->num_rollo,
                $movimiento->producto_id,
                $movimiento->color_id,
                $movimiento->tipo_calidad_id,
                $movimiento->cantidad,
                $movimiento->peso_tara,
                $movimiento->usuario_id,
            ]);
            fclose($fh);

            return response()->json([
                'success'        => true,
                'movimiento_id'  => $movimiento->id,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error al registrar movimiento de entrada', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el movimiento: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function FiltrarPorducto(Request $request) {
        $producto = Productos::select('id', 'nombre', 'tara')
            ->where('id', $request->producto_id)
            ->first();

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return [
            'tara' => $producto->tara
        ];
    }

    public function ObtenerTarjetasViajeras(Request $request) {
        $validated = $request->validate([
            'cliente_id' => 'required|integer|exists:clientes,id',
        ]);

        // Obtener todas las tarjetas viajeras únicas de este cliente
        $tarjetas = Movimientos::where('cliente_id', $validated['cliente_id'])
            ->select('num_tarjeta')
            ->distinct()
            ->orderBy('num_tarjeta', 'asc')
            ->pluck('num_tarjeta')
            ->map(fn($tarjeta) => [
                'value' => $tarjeta,
                'label' => 'TV: ' . $tarjeta,
            ])
            ->values();

        return response()->json([
            'tarjetas' => $tarjetas,
        ]);
    }



}
