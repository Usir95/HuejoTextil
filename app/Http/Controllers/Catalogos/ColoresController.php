<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Colores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ColoresController extends Controller {

    public function index() {
        $Colores = Colores::orderby('nombre', 'asc')->get();
        return Inertia::render('Catalogos/Colores', compact('Colores'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string|max:85',
        ]);

        Colores::create($data);
        return redirect()->back();
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'nombre' => 'required|string|max:85',
        ]);

        $tipo = Colores::findOrFail($id);
        $tipo->update($data);
        return redirect()->back();
    }

    public function destroy($id) {
        $tipo = Colores::findOrFail($id);
        $tipo->delete();
        return redirect()->back();
    }
}
