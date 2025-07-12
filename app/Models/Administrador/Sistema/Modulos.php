<?php

namespace App\Models\Administrador\Sistema;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;


class Modulos extends Model implements AuditableContract {
    use HasFactory, SoftDeletes, Auditable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];

    public function Categoria() {
        return $this->belongsTo(CategoriasModulos::class, 'categoria_modulo_id');
    }

    public function Submodulos() {
        return $this->hasMany(Modulos::class, 'modulo_padre_id');
    }

    public function ModuloPadre() {
        return $this->belongsTo(Modulos::class, 'modulo_padre_id');
    }

}
