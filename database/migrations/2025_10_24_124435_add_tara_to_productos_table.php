<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('tara', 10, 3)->nullable()->after('unidad_id');
        });
    }
    public function down(): void {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('tara');
        });
    }
};
