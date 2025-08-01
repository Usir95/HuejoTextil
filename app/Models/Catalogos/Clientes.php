<?php

namespace App\Models\Catalogos;

use App\Models\Almacenes\Movimientos;
use App\Models\Almacenes\Pedidos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;


class Clientes extends Model implements AuditableContract {
    use HasFactory, SoftDeletes, Auditable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'nombre',
        'clave',
    ];

    public function pedidos() {
        return $this->hasMany(Pedidos::class, 'cliente_id');
    }

    public function movimientos() {
        return $this->hasMany(Movimientos::class, 'cliente_id');
    }


    /* ======================================== HELPERS ======================================== */
    public static function Catalogo() {
        return self::select('id', 'nombre')->get()->map(fn($item) => [
            'value' => $item->id,
            'label' => $item->nombre,
        ]);
    }
}
