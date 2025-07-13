<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ClientesController extends Controller {

    public function index() {
        $Clientes = Clientes::get();
        return Inertia::render('Catalogos/Clientes', compact('Clientes'));
    }

    public function store(Request $request) {
        $validate = $request->validate([
            'nombre' => 'required|string|max:125',
            'clave' => 'required|string|max:125',
        ]);

        Clientes::create([
            'nombre' => $validate['nombre'],
            'clave' => $validate['clave'],
        ]);

        return redirect()->back()->with('success', 'Cliente creado');
    }

    public function update(Request $request, $id) {
        $validate = $request->validate([
            'nombre' => 'required|string|max:125',
            'clave' => 'required|string|max:125',
        ]);

        $cliente = Clientes::findOrFail($id);

        $cliente->update([
            'nombre' => $validate['nombre'],
            'clave' => $validate['clave'],
        ]);

        return redirect()->back()->with('success', 'Cliente actualizado');
    }

    public function destroy($id) {
        $cliente = Clientes::findOrFail($id);
        $cliente->delete();

        return redirect()->back()->with('success', 'Cliente eliminado');
    }


}
