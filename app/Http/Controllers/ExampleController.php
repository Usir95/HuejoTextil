<?php

namespace App\Http\Controllers;

use App\Models\Examples;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ExampleController extends Controller {

    public function index() {
        $Examples = Examples::get();

        $NivelesPeligro = ['bajo', 'medio', 'alto'];
        $Generos = ['macho', 'hembra', 'otro'];
        $Estatus = ['activo', 'inactivo', 'pendiente'];

        return Inertia::render('Example/Examples', compact('Examples', 'NivelesPeligro', 'Generos', 'Estatus'));
    }

    public function MyFunction(Request $request) {
        return "MyFunction";
    }

    public function store(Request $request) {
        return $request;
    }
}
