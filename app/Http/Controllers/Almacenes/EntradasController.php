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
        $request->validate([
            'cliente_id'        => 'required',
            'num_tarjeta'       => 'required',
            'num_rollo'         => 'nullable',
            'producto_id'       => 'required',
            'color_id'          => 'required',
            'tipo_calidad_id'   => 'required',
            'cantidad'          => 'required|numeric|min:0.01',
        ]);

        try {
                $movimiento = DB::transaction(function () use ($request) {

                if ($request->filled('num_rollo')) {
                    $numRolloFinal = $request->num_rollo;
                } else {
                    $ultimoRollo = Movimientos::where('cliente_id', $request->cliente_id)
                        ->where('num_tarjeta', $request->num_tarjeta)
                        ->orderByDesc('num_rollo')
                        ->value('num_rollo');

                    $numRolloFinal = $ultimoRollo
                        ? str_pad(((int) $ultimoRollo) + 1, 4, '0', STR_PAD_LEFT)
                        : '0001';
                }

                // Crear movimiento
                $movimiento = Movimientos::create([
                    'cliente_id'         => $request->cliente_id,
                    'num_tarjeta'        => $request->num_tarjeta,
                    'num_rollo'          => $numRolloFinal,
                    'producto_id'        => $request->producto_id,
                    'color_id'           => $request->color_id,
                    'tipo_calidad_id'    => $request->tipo_calidad_id,
                    'cantidad'           => $request->cantidad,
                    'fecha_movimiento'   => now(),
                    'tipo_movimiento_id' => 1,
                    'usuario_id'         => Auth::id(),
                    'almacen_id'         => 1,
                ]);

                if (!$movimiento) {
                    throw new \Exception('No se pudo crear el movimiento');
                }

                Log::info('Movimiento creado correctamente', ['id' => $movimiento->id]);

                $inventario = Inventarios::where('producto_id', $request->producto_id)
                    ->where('almacen_id', 1)
                    ->where('color_id', $request->color_id)
                    ->where('tipo_calidad_id', $request->tipo_calidad_id)
                    ->first();

                if ($inventario) {
                    $inventario->cantidad += $request->cantidad;
                    $inventario->save();
                } else {
                    Inventarios::create([
                        'producto_id'      => $request->producto_id,
                        'almacen_id'       => 1,
                        'color_id'         => $request->color_id,
                        'tipo_calidad_id'  => $request->tipo_calidad_id,
                        'cantidad'         => $request->cantidad,
                    ]);
                }

                return $movimiento;
            });

            return response()->json([
                'success' => true,
                'movimiento_id' => $movimiento->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al registrar movimiento de entrada', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el movimiento: ' . $e->getMessage(),
            ], 500);
        }
    }

}
