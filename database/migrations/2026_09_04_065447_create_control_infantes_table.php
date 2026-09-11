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
        Schema::create('control_infantes', function (Blueprint $table) {
            $table->id();
            /*
            |--------------------------------------------------------------------------
            | Relación con ficha_infante
            |--------------------------------------------------------------------------
            |
            | Se utiliza codigo_infante como referencia.
            | ficha_infante.codigo_infante debe ser UNIQUE.
            |
            */
            $table->unsignedBigInteger('ficha_infante_id');
            $table->foreign('ficha_infante_id')->references('id')->on('ficha_infantes');
            /*
            |--------------------------------------------------------------------------
            | Número correlativo del control por infante
            |--------------------------------------------------------------------------
            */
            $table->unsignedInteger('numero_control');

            /*
            |--------------------------------------------------------------------------
            | Edad al momento del control
            |--------------------------------------------------------------------------
            */
            $table->unsignedTinyInteger('anios_edad_control');
            $table->unsignedTinyInteger('meses_edad_control');
            $table->unsignedTinyInteger('dia_edad_control');

            /*
            |--------------------------------------------------------------------------
            | Peso
            |--------------------------------------------------------------------------
            */
            $table->decimal('peso_actual_control', 5, 2);
            $table->decimal('peso_ideal_control', 5, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Indicador Peso / Edad
            |--------------------------------------------------------------------------
            */
            $table->enum('indicador_pe_control', [
                'PB Severo',
                'PB Moderado',
                'Riesgo BP',
                'Normal',
                'Sobrepeso',
                'Obesidad'
            ])->nullable();

            /*
            |--------------------------------------------------------------------------
            | Talla
            |--------------------------------------------------------------------------
            */
            $table->decimal('talla_actual_control', 5, 2);
            $table->decimal('talla_ideal_control', 5, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Indicador Talla / Edad
            |--------------------------------------------------------------------------
            */
            $table->enum('indicador_te_control', [
                'RC. Severo',
                'RC. Moderado',
                'Riesgo RC',
                'C. Adecuado',
                'Riesgo DC',
                'DC. Moderada',
                'DC. Severa'
            ])->nullable();

            /*
            |--------------------------------------------------------------------------
            | Indicador Peso / Talla
            |--------------------------------------------------------------------------
            */
            $table->enum('indicador_pt_control', [
                'DA Severa',
                'DA Moderada',
                'Riesgo DA',
                'Normal',
                'Riesgo SP',
                'Sobrepeso',
                'Obesidad'
            ])->nullable();

            /*
            |--------------------------------------------------------------------------
            | Evolución
            |--------------------------------------------------------------------------
            */
            $table->enum('evolucion_control', [
                'Mejoria',
                'Normal',
                'Sin mejora',
                'Desnutricion'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Producto entregado
            |--------------------------------------------------------------------------
            */
            $table->enum('producto_control', [
                'Leche',
                'Incaparina',
                'Atol'
            ])->nullable();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */
            $table->index('ficha_infante_id');

            /*
            | Evita que un mismo infante tenga dos controles
            | con el mismo número.
            */
            $table->unique([
                'ficha_infante_id',
                'numero_control'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            |
            | IMPORTANTE:
            | ficha_infante.codigo_infante debe tener índice UNIQUE.
            |
            */
  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_infantes');
    }
};
