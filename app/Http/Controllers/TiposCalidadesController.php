<?php

namespace App\Http\Controllers;

use App\Models\Catalogos\TiposCalidades;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TiposCalidadesController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $Variable = null;
        return Inertia::render('Ruta',  compact('Variable'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {

        //$validator = Validator::make($request->all(), [
        //    'field1' => 'required|string|max:255',
        //    'field2' => 'required|email|unique:table,column',
        //]);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(TiposCalidades $tiposCalidades){

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TiposCalidades $tiposCalidades){

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TiposCalidades $tiposCalidades){

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TiposCalidades $tiposCalidades){

        return redirect()->back();
    }
}
