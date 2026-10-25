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
        Schema::create('compra_detalles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('compra_id')
                ->constrained('compras')
                ->cascadeOnDelete();

            $table->foreignId('presentacion_id')
                ->constrained('presentaciones')
                ->restrictOnDelete();

            $table->unsignedInteger('cantidad');

            $table->decimal('costo_unitario', 10, 2);

            $table->decimal('costo_promedio_anterior', 10, 2);

            $table->decimal('costo_promedio_nuevo', 10, 2);

            $table->decimal('precio_unitario_anterior', 10, 2);

            $table->decimal('precio_unitario_nuevo', 10, 2)
                ->nullable();

            $table->decimal('subtotal', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compra_detalles');
    }
};
