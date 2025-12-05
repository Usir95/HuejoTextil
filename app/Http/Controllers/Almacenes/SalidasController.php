<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Almacenes\Inventarios;
use App\Models\Almacenes\Movimientos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class SalidasController extends Controller {

    public function index() {
        return Inertia::render('Almacenes/Salidas');
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

    public function RegistrarSalida($movimiento_id) {
        $mov = Movimientos::find($movimiento_id);

        if (!$mov) {
            return back()->with('toast', [
                'message' => 'No se encontró el movimiento especificado.',
                'type' => 'danger'
            ]);
        }

        try {
            DB::transaction(function () use ($mov) {

                // Bloquear inventario dentro de la transacción
                $inventario = Inventarios::where('producto_id', $mov->producto_id)
                    ->where('color_id', $mov->color_id)
                    ->where('tipo_calidad_id', $mov->tipo_calidad_id)
                    ->where('almacen_id', $mov->almacen_id)
                    ->lockForUpdate()
                    ->first();

                if (!$inventario) {
                    throw new \Exception('No existe inventario para este producto.');
                }

                if ($inventario->cantidad < $mov->cantidad) {
                    throw new \Exception('Inventario insuficiente para dar salida al rollo.');
                }

                // Restar inventario
                $inventario->update([
                    'cantidad' => $inventario->cantidad - $mov->cantidad
                ]);

                // Registrar movimiento
                Movimientos::create([
                    'cliente_id'         => $mov->cliente_id,
                    'num_tarjeta'        => $mov->num_tarjeta,
                    'num_rollo'          => $mov->num_rollo,
                    'peso_tara'          => $mov->peso_tara,
                    'producto_id'        => $mov->producto_id,
                    'color_id'           => $mov->color_id,
                    'tipo_calidad_id'    => $mov->tipo_calidad_id,
                    'cantidad'           => $mov->cantidad,
                    'fecha_movimiento'   => now(),
                    'tipo_movimiento_id' => 2,
                    'usuario_id'         => Auth::user()->id,
                    'almacen_id'         => $mov->almacen_id,
                ]);
            });

        } catch (\Exception $e) {
            return back()->with('toast', [
                'message' => $e->getMessage(),
                'type' => 'danger'
            ]);
        }

        return back()->with('toast', [
            'message' => 'Salida registrada correctamente.',
            'type' => 'success'
        ]);
    }


}
