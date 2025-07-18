<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 65)->unique();
            $table->string('nombre', 125);
            $table->foreignId('tipo_producto_id')->nullable()->constrained('tipos_productos')->nullOnDelete();
            $table->foreignId('unidad_id')->nullable()->constrained('unidades')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('productos');
    }
};
