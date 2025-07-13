<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\TiposProductos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class TiposProductosController extends Controller {

    public function index() {
        $Productos = TiposProductos::get();
        return Inertia::render('Catalogos/TiposProductos', compact('Productos'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string|max:85',
        ]);

        TiposProductos::create($data);
        return redirect()->back()->with('success', 'Tipo de producto creado');
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'nombre' => 'required|string|max:85',
        ]);

        $tipo = TiposProductos::findOrFail($id);
        $tipo->update($data);
        return redirect()->back()->with('success', 'Tipo de producto actualizado');
    }

    public function destroy($id) {
        $tipo = TiposProductos::findOrFail($id);
        $tipo->delete();
        return redirect()->back()->with('success', 'Tipo de producto eliminado');
    }

}
