<?php

namespace App\Http\Controllers;

use App\Models\Examples;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ExampleController extends Controller {

    public function index() {
        $Examples = Examples::get();

        $NivelesPeligro = collect(['bajo', 'medio', 'alto'])->map(function ($v, $i) {
            return [
                'label' => ucfirst($v),
                'value' => $i + 1 // Empieza desde 1
            ];
        });

        $Generos = collect(['macho', 'hembra', 'otro'])->map(function ($v, $i) {
            return [
                'label' => ucfirst($v),
                'value' => $i + 1
            ];
        });

        $Estatus = collect(['activo', 'inactivo', 'pendiente'])->map(function ($v, $i) {
            return [
                'label' => ucfirst($v),
                'value' => $i + 1
            ];
        });


        return Inertia::render('Example/Examples', compact('Examples', 'NivelesPeligro', 'Generos', 'Estatus'));
    }

    public function MyFunction(Request $request) {
        return "MyFunction";
    }

    public function store(Request $request) {
        return $request;
    }
}
