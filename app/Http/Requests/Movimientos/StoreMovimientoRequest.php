<?php

namespace App\Http\Requests\Movimientos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Importar Rule para validación de existencia

class StoreMovimientoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'cantidad' => 'required|integer|min:1',
            'fecha_movimiento' => 'required|date',
            'tipo_producto_id' => [
                'nullable',
                Rule::exists('tipos_productos', 'id')
            ],
            'tipo_unidad_id' => [
                'nullable',
                Rule::exists('tipos_unidades', 'id')
            ],
            'unidad_medida_id' => [
                'nullable',
                Rule::exists('unidades', 'id')
            ],
            'cliente_id' => [
                'nullable',
                Rule::exists('clientes', 'id')
            ],
            'tipo_movimiento_id' => [
                'nullable',
                Rule::exists('tipos_movimientos', 'id')
            ],
            'pedido_id' => [
                'nullable',
                Rule::exists('pedidos', 'id')
            ],
            'usuario_id' => [
                'nullable',
                Rule::exists('users', 'id')
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser al menos 1.',
            'fecha_movimiento.required' => 'La fecha del movimiento es obligatoria.',
            'fecha_movimiento.date' => 'La fecha del movimiento no tiene un formato válido.',
            'tipo_producto_id.exists' => 'El tipo de producto seleccionado no es válido.',
            'tipo_unidad_id.exists' => 'El tipo de unidad seleccionado no es válido.',
            'unidad_medida_id.exists' => 'La unidad de medida seleccionada no es válida.',
            'cliente_id.exists' => 'El cliente seleccionado no es válido.',
            'tipo_movimiento_id.exists' => 'El tipo de movimiento seleccionado no es válido.',
            'pedido_id.exists' => 'El pedido seleccionado no es válido.',
            'usuario_id.exists' => 'El usuario seleccionado no es válido.',
        ];
    }
}
