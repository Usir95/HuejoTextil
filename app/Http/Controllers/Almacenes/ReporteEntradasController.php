<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ReporteEntradasController extends Controller {

    public function index(Request $request) {
        $Clientes = Clientes::Catalogo();

        $cliente_id = $request->query('cliente_id');
        $num_tarjeta = $request->query('num_tarjeta');

        $cliente_id = filled($cliente_id) ? $cliente_id : null;
        $num_tarjeta = filled($num_tarjeta) ? $num_tarjeta : null;

        return Inertia::render('Almacenes/Pedidos/ReporteEntradas', [
            'Clientes' => $Clientes,
            'cliente_id' => $cliente_id,
            'num_tarjeta' => $num_tarjeta,
        ]);
    }

}
