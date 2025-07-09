<?php

namespace App\Http\Controllers;

use App\Models\Examples;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ExampleController extends Controller {

    public function index() {
        $Examples = Examples::get();
        return Inertia::render('Example/Examples', compact('Examples'));
    }

    public function MyFunction(Request $request) {
        return "MyFunction";
    }

    public function store(Request $request) {
        return $request;
    }
}
