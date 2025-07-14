<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Almacenes\Pedidos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class PedidosClientesController extends Controller {

    public function index() {
        $PedidosClientes = Pedidos::get();

        $NombresEstados = ['Pendiente', 'En proceso', 'Completado', 'Cancelado'];
        $TiposPagos = ['Contado', 'Credito', 'Debito', 'Cheque', 'Transferencia'];

        $Estatus = [];
        $FormasPagos = [];

        foreach ($NombresEstados as $estado) {
            $Estatus[] = [
                'value' => $estado,
                'label' => $estado,
            ];
        }

        foreach ($TiposPagos as $pago) {
            $FormasPagos[] = [
                'value' => $pago,
                'label' => $pago,
            ];
        }

        return Inertia::render('Almacenes/Pedidos/PedidosClientes', compact('PedidosClientes', 'Estatus', 'FormasPagos'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'fecha_pedido' => 'required|date',
            'estado_pedido' => 'required|string|max:15',
            'plazo_pago' => 'required|string|max:30',
            'condiciones' => 'required|string',
            'observaciones' => 'nullable|string',
            'cliente_id' => 'nullable|exists:clientes,id',
        ]);

        Pedidos::create([
            'fecha_pedido' => $validated['fecha_pedido'],
            'estado_pedido' => $validated['estado_pedido'],
            'plazo_pago' => $validated['plazo_pago'],
            'condiciones' => $validated['condiciones'],
            'observaciones' => $validated['observaciones'],
            'cliente_id' => $validated['cliente_id'],
        ]);
        return back()->with('success', 'Pedido creado');
    }

    public function update(Request $request, $id) {
        $pedido = Pedidos::findOrFail($id);

        $validated = $request->validate([
            'fecha_pedido' => 'required|date',
            'estado_pedido' => 'required|string|max:15',
            'plazo_pago' => 'required|string|max:30',
            'condiciones' => 'required|string',
            'observaciones' => 'nullable|string',
            'cliente_id' => 'nullable|exists:clientes,id',
        ]);

        $pedido->update([
            'fecha_pedido'   => $validated['fecha_pedido'],
            'estado_pedido'  => $validated['estado_pedido'],
            'plazo_pago'     => $validated['plazo_pago'],
            'condiciones'    => $validated['condiciones'],
            'observaciones'  => $validated['observaciones'] ?? '',
            'cliente_id'     => $validated['cliente_id'],
        ]);

        return back()->with('success', 'Pedido actualizado');
    }

    public function destroy($id) {
        Pedidos::findOrFail($id)->delete();
        return back()->with('success', 'Pedido eliminado');
    }
}
