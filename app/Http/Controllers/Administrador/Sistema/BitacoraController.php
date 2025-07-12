<?php

namespace App\Http\Controllers\Administrador\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Models\Audit;
use Inertia\Inertia;


class BitacoraController extends Controller {

    public function index() {
        $BitacoraAudit = Audit::with('user')->orderBy('id', 'desc')->get();

        return Inertia::render('Administrador/Sistema/Bitacoras', compact('BitacoraAudit'));
    }
}
