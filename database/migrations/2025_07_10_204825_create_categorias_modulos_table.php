<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('categorias_modulos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',45);
            $table->string('icono', 35)->nullable();
            $table->string('descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('categorias_modulos');
    }
};
