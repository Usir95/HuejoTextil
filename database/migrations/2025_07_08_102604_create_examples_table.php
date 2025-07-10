<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('examples', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 35); // MdTextInput
            $table->unsignedTinyInteger('edad_aproximada'); // MdNumberInput
            $table->string('especie', 35); // MdTextInput
            $table->string('color_principal', 15); // MdColorPicker
            $table->string('foto')->nullable(); // FileUploader o input tipo file
            $table->string('origen', 25)->nullable(); // MdSelectInput
            $table->date('fecha_descubrimiento')->nullable(); // MdDateInput
            $table->date('rango_inicio')->nullable(); // MdDateRangeInput (inicio)
            $table->date('rango_fin')->nullable(); // MdDateRangeInput (fin)
            $table->string('nivel_peligro', 15)->nullable(); // MdSelectInput
            $table->text('descripcion')->nullable(); // MdTextarea
            $table->boolean('es_invisible')->default(false); // MdCheckbox
            $table->boolean('tiene_alas')->default(false); // MdCheckbox
            $table->string('genero', 10)->nullable(); // MdSelectInput
            $table->string('clave_identificacion', 15)->unique(); // MdTextInput
            $table->string('correo_contacto', 65)->nullable(); // MdTextInput con validación email
            $table->boolean('confirmacion')->default(false); // MdSwitch o MdCheckbox
            $table->string('estatus', 25); // MdSelectInput

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('examples');
    }
};
