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
            $table->foreignId('unidad_id')->nullable()->constrained('unidades')->nullOnDelete();
            $table->foreignId('tipo_movimiento_id')->nullable()->constrained('tipos_movimientos')->nullOnDelete();
            $table->foreignId('almacen_id')->nullable()->after('usuario_id')->constrained('almacenes')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('movimientos');
    }
};
