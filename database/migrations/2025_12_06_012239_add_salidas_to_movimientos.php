<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('movimientos', function (Blueprint $table) {
            // $table->foreignId('salida_id')
            //     ->nullable()
            //     ->after('cliente_id')
            //     ->constrained('salidas')
            //     ->nullOnDelete();

            // $table->foreignId('pedido_id')
            //     ->nullable()
            //     ->after('salida_id')
            //     ->constrained('pedidos')
            //     ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('movimientos', function (Blueprint $table) {
            //
        });
    }
};
