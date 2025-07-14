<?php

namespace App\Http\Controllers;

use App\Models\Catalogos\Productos;
use App\Models\Catalogos\TiposProductos;
use App\Models\Catalogos\Unidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ProductosController extends Controller {

    public function index() {
        $TiposProductos = TiposProductos::orderBy('nombre', 'asc')->get()->map(fn($tipo) => [
            'value' => $tipo->id,
            'label' => $tipo->nombre,
        ]);

        $Unidades = Unidades::orderBy('nombre', 'asc')->get()->map(fn($unidad) => [
            'value' => $unidad->id,
            'label' => $unidad->abreviatura . ' - ' . $unidad->nombre,
        ]);

        $Productos = Productos::with(['tipoProducto', 'unidadMedida'])->orderBy('id', 'desc')->get();

        return Inertia::render('Catalogos/Productos', compact('Productos', 'TiposProductos', 'Unidades'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'codigo' => 'required|string|max:65|unique:productos,codigo',
            'nombre' => 'required|string|max:125',
            'tipo_producto_id' => 'nullable|exists:tipos_productos,id',
            'unidad_id' => 'nullable|exists:unidades,id',
        ]);

        Productos::create($data);
        return back()->with('success', 'Producto creado');
    }

    public function update(Request $request, $id) {
        $producto = Productos::findOrFail($id);

        $data = $request->validate([
            'codigo' => 'required|string|max:65|unique:productos,codigo,' . $producto->id,
            'nombre' => 'required|string|max:125',
            'tipo_producto_id' => 'nullable|exists:tipos_productos,id',
            'unidad_id' => 'nullable|exists:unidades,id',
        ]);

        $producto->update($data);
        return back()->with('success', 'Producto actualizado');
    }

    public function destroy($id) {
        Productos::findOrFail($id)->delete();
        return back()->with('success', 'Producto eliminado');
    }
}
