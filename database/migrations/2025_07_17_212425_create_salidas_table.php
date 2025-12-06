<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->nullable()
                ->constrained('clientes')
                ->nullOnDelete();

            $table->foreignId('pedido_id')
                ->nullable()
                ->constrained('pedidos')
                ->nullOnDelete();

            $table->date('fecha_salida');
            $table->string('folio_interno', 30)->nullable();
            $table->string('pedido_numero', 30)->nullable();

            $table->foreignId('almacen_id')
                ->nullable()
                ->constrained('almacenes')
                ->nullOnDelete();

            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();

            $table->decimal('total_kgs', 12, 3)->default(0);
            $table->unsignedInteger('total_rollos')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });

    }

    public function down(): void {
        Schema::dropIfExists('salidas');
    }
};
