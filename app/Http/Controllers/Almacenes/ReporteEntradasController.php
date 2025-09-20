<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ReporteEntradasController extends Controller {

    public function index() {
        $Clientes = Clientes::Catalogo();
        return Inertia::render('Almacenes/Pedidos/ReporteEntradas', compact('Clientes'));
    }

    public function MyFunction(Request $request) {
        return "MyFunction";
    }
}
