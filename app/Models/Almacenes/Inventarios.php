<?php

namespace App\Models\Almacenes;

use App\Models\Catalogos\Colores;
use App\Models\Catalogos\Productos;
use App\Models\Catalogos\TiposCalidades;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;


class Inventarios extends Model implements AuditableContract {
    use HasFactory, SoftDeletes, Auditable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];

    public function Almacen() {
        return $this->belongsTo(Almacenes::class, 'almacen_id');
    }

    public function Producto() {
        return $this->belongsTo(Productos::class, 'producto_id');
    }

    public function Color() {
        return $this->belongsTo(Colores::class, 'color_id');
    }

    public function TipoCalidad() {
        return $this->belongsTo(TiposCalidades::class, 'tipo_calidad_id');
    }
}
