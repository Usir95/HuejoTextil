<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\Catalogos\TiposProductos;
use App\Models\Catalogos\Unidades;


class Productos extends Model implements AuditableContract {
    use HasFactory, SoftDeletes, Auditable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];

    public function tipoProducto() {
        return $this->belongsTo(TiposProductos::class, 'tipo_producto_id');
    }

    public function unidadMedida() {
        return $this->belongsTo(Unidades::class, 'unidad_id');
    }

}
