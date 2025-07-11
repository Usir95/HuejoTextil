<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100)->nullable();

            $table->string('telefono', 15)->nullable();
            $table->string('correo', 100)->nullable()->index();

            $table->unsignedBigInteger('usuario_id')->nullable()->index();
            $table->foreign('usuario_id')->references('id')->on('usuarios');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('empleados');
    }
};
