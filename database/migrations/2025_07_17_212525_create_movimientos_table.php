<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->dateTime('fecha_movimiento');

            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('unidad_id')->nullable()->constrained('unidades')->nullOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('colores')->nullOnDelete();
            $table->foreignId('tipo_calidad_id')->nullable()->constrained('tipos_calidades')->nullOnDelete();
            $table->foreignId('tipo_movimiento_id')->nullable()->constrained('tipos_movimientos')->nullOnDelete();
            // Define usuario_id antes de almacen_id si quieres que aparezca antes en la tabla
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            // Eliminado ->after('usuario_id') para evitar el error de sintaxis
            $table->foreignId('almacen_id')->nullable()->constrained('almacenes')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
