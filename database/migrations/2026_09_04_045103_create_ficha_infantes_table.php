<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ficha_infantes', function (Blueprint $table) {
            // id incrementable
            $table->id();

            // Generado de manera automática (se puede manejar con un trigger o en el Modelo de Laravel)
            $table->string('codigo_infante', 20)->unique();

            // Datos del infante
            $table->string('nombre_infante'); // Obligatorio
            $table->enum('sexo_infante', ['Masculino', 'Femenino']); // Obligatorio (Corregido 'Femenico')
            $table->date('fecha_nacimiento_infante'); // Obligatorio
            $table->date('fecha_inscripcion_infante')->useCurrent(); // Por defecto la fecha actual
            
            // Datos médicos/físicos (Permiten decimales y son opcionales)
            $table->decimal('peso_infante', 5, 2)->nullable(); // Ej: 12.34 kg
            $table->decimal('talla_infante', 5, 2)->nullable(); // Ej: 85.50 cm
            $table->integer('prueba_apetito_infante')->nullable(); // Guardado como entero (porcentaje)
            $table->integer('examen_cognitivo_infante')->nullable(); // Guardado como entero (porcentaje)

            // Ubicación del encargado
            $table->string('departamento_residencia_encargado'); // Obligatorio
            $table->string('municipio_residencia_encargado'); // Obligatorio
            $table->text('direccion_residencia_encargado'); // Obligatorio (Text por si es larga)

            // Datos del encargado
            $table->enum('parentesco_encargado', ['madre', 'padre', 'abuelo', 'tio', 'encargado']); // Obligatorio
            $table->string('nombre_encargado'); // Obligatorio
            $table->string('dpi_encargado', 13)->nullable(); // Opcional, 13 dígitos (Estilo DPI Guatemala)
            $table->string('telefono_encargado', 8); // Obligatorio, 8 dígitos

            // Campos de auditoría (created_at y updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ficha_infantes');
    }
};
