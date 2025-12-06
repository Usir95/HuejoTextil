<?php

namespace App\Models\Almacenes;

use App\Models\Catalogos\Clientes;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;


class Salidas extends Model implements AuditableContract {
    use HasFactory, SoftDeletes, Auditable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];

    public function cliente()  {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }

    public function pedido()   {
        return $this->belongsTo(Pedidos::class, 'pedido_id');
    }

    public function almacen()  {
        return $this->belongsTo(Almacenes::class, 'almacen_id');
    }

    public function usuario()  {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function movimientos() {
        return $this->hasMany(Movimientos::class, 'salida_id');
    }

}
