<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Models\Catalogos\TiposUnidades;


class Unidades extends Model implements AuditableContract {
    use HasFactory, SoftDeletes, Auditable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];

    public function tipoUnidad()
    {
        return $this->belongsTo(TiposUnidades::class, 'tipo_unidad_id');
    }

}
