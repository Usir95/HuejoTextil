<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Almacenes\Inventarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ProductosTerminadosController extends Controller {

    public function index() {
        $Inventarios = Inventarios::with(['Producto', 'Color', 'TipoCalidad'])->get();

        return Inertia::render('Almacenes/Inventarios/ProductosTerminados', compact('Inventarios'));
    }

    public function MyFunction(Request $request) {
        return "MyFunction";
    }
}
