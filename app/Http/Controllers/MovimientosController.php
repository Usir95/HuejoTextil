<?php

namespace App\Http\Controllers;

use App\Models\Almacenes\Movimientos;
use Illuminate\Http\Request;
use Inertia\Inertia;
// Importar los nuevos Form Request
use App\Http\Requests\Movimientos\StoreMovimientoRequest;
use App\Http\Requests\Movimientos\UpdateMovimientoRequest;

class MovimientosController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $Variable = null;
        return Inertia::render('Ruta',  compact('Variable'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Movimientos\StoreMovimientoRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreMovimientoRequest $request) {
        // La validación se maneja automáticamente por StoreMovimientoRequest
        // Si el request llega aquí, significa que la validación ha pasado.

        Movimientos::create($request->validated()); // Usa validated() para obtener solo los datos validados

        return redirect()->back()->with('success', 'Movimiento creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Movimientos $movimientos){

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movimientos $movimientos){

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Movimientos\UpdateMovimientoRequest  $request
     * @param  \App\Models\Almacenes\Movimientos  $movimientos
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateMovimientoRequest $request, Movimientos $movimientos){
        // La validación se maneja automáticamente por UpdateMovimientoRequest
        // Si el request llega aquí, significa que la validación ha pasado.

        $movimientos->update($request->validated()); // Usa validated() para obtener solo los datos validados

        return redirect()->back()->with('success', 'Movimiento actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movimientos $movimientos){
        $movimientos->delete();
        return redirect()->back()->with('success', 'Movimiento eliminado exitosamente.');
    }
}
