<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('ruta', 50);
            $table->string('descripcion', 150)->nullable();
            $table->string('icono', 100)->nullable();
            $table->string('imagen')->nullable();

            $table->foreignId('categoria_modulo_id')
                ->constrained('categorias_modulos')
                ->onDelete('cascade');


            $table->foreignId('modulo_padre_id')
                ->nullable()
                ->constrained('modulos')
                ->onDelete('NO ACTION')
                ->onUpdate('NO ACTION'); // Evitar ciclos en SQL Server

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('modulos');
    }
};
