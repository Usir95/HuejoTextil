<?php

namespace App\Models\RecursosHumanos;

use App\Models\Scopes\ActivoScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;


class Empleados extends Model implements AuditableContract {
    use HasFactory, SoftDeletes, Auditable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];

    /* ========================== SCOPE PERSONLIAZADOS ========================== */
    protected static function booted() {
        static::addGlobalScope(new ActivoScope);
    }

    /* ================================ RELACIONES ================================ */
    public function Usuario() {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
