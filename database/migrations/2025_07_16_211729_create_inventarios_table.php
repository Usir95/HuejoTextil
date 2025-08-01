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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id(); // Este es el auto_increment primary key de la tabla
            $table->integer('cantidad')->default(0); // Corregido: Eliminado auto_increment primary key de aquí
            $table->foreignId('almacen_id')->constrained('almacenes')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('colores')->nullOnDelete(); // Asumiendo tabla 'colores'
            $table->foreignId('tipo_calidad_id')->nullable()->constrained('tipos_calidades')->nullOnDelete(); // Asumiendo tabla 'tipos_calidad'
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
