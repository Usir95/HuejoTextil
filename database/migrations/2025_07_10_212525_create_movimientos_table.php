<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->dateTime('fecha_movimiento');
            $table->foreignId('tipo_producto_id')->nullable()->constrained('tipos_productos')->nullOnDelete();
            $table->foreignId('tipo_unidad_id')->nullable()->constrained('tipos_unidades')->nullOnDelete();
            $table->foreignId('unidad_medida_id')->nullable()->constrained('unidades')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('tipo_movimiento_id')->nullable()->constrained('tipos_movimientos')->nullOnDelete();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('movimientos');
    }
};
