<?php

namespace App\Models\Almacenes;

use App\Models\Catalogos\Clientes;
use App\Models\Almacenes\Pedidos;
use App\Models\Catalogos\TiposMovimientos;
use App\Models\Catalogos\TiposProductos;
use App\Models\Catalogos\TiposUnidades;
use App\Models\Catalogos\Unidades;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;


class Movimientos extends Model implements AuditableContract {
    use HasFactory, SoftDeletes, Auditable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];

    public function cliente() {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }

    public function pedido() {
        return $this->belongsTo(Pedidos::class, 'pedido_id');
    }

    public function tipoMovimiento() {
        return $this->belongsTo(TiposMovimientos::class, 'tipo_movimiento_id');
    }

    public function unidadMedida() {
        return $this->belongsTo(Unidades::class, 'unidad_medida_id');
    }

    public function tipoUnidad() {
        return $this->belongsTo(TiposUnidades::class, 'tipo_unidad_id');
    }

    public function tipoProducto() {
        return $this->belongsTo(TiposProductos::class, 'tipo_producto_id');
    }

    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }

}
