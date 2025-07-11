<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_pedido');
            $table->string('estado_pedido');
            $table->string('plazo_pago');
            $table->string('condiciones');
            $table->string('observaciones');

            // $table->unsignedBigInteger('cliente_id')->nullable()->index();
            // $table->foreign('cliente_id')->references('id')->on('clientes');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pedidos');
    }
};
