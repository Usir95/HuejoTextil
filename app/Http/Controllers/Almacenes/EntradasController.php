<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Almacenes\Almacenes;
use App\Models\Almacenes\Inventarios;
use App\Models\Almacenes\Movimientos;
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
        $Productos = Productos::select('id', 'nombre')->get()->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->nombre,
            ];
        });

        $Colores = Colores::select('id', 'nombre')->get()->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->nombre,
            ];
        });

        $TiposCalidades = TiposCalidades::select('id', 'nombre')->get()->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->nombre,
            ];
        });

        $Almacenes = Almacenes::select('id', 'nombre')->get()->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->nombre,
            ];
        });

        return Inertia::render('Almacenes/Entradas', compact(
            'Productos',
            'Colores',
            'TiposCalidades',
            'Almacenes'
        ));
    }

    public function store(Request $request) {
        $request->validate([
            'producto_id' => 'required',
            'cantidad' => 'required|numeric',
            'color_id' => 'required',
            'tipo_calidad_id' => 'required',
        ]);

        $movimiento = null;

        DB::transaction(function () use ($request, &$movimiento) {
            $movimiento = Movimientos::create([
                'cantidad' => $request->cantidad,
                'fecha_movimiento' => now(),
                'producto_id' => $request->producto_id,
                'unidad_id' => 1,
                'color_id' => $request->color_id,
                'tipo_calidad_id' => $request->tipo_calidad_id,
                'tipo_movimiento_id' => 1,
                'usuario_id' => Auth::id(),
                'almacen_id' => 1,
            ]);

            if (!$movimiento) {
                throw new \Exception('No se pudo crear el movimiento');
            }

            Log::info('Movimiento creado:', ['id' => $movimiento->id]);

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
                    'producto_id' => $request->producto_id,
                    'almacen_id' => 1,
                    'color_id' => $request->color_id,
                    'tipo_calidad_id' => $request->tipo_calidad_id,
                    'cantidad' => $request->cantidad,
                ]);
            }
        });

        if (!$movimiento) {
            return redirect()->back()->withErrors(['Error al registrar el movimiento']);
        }

        return response()->json([
            'movimiento_id' => $movimiento->id,
        ]);
    }

}
