<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('unidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('abreviacion');
            $table->foreignId('tipo_unidad_id')->nullable()->constrained('tipos_unidades')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('unidades');
    }
};
