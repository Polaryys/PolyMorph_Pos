<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historial_precios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('presentacion_id')
                ->constrained('presentaciones')
                ->restrictOnDelete();

            // Precios anteriores
            $table->decimal('precio_unitario_anterior', 10, 2)->nullable();
            $table->decimal('precio_docena_anterior', 10, 2)->nullable();
            $table->decimal('precio_ciento_anterior', 10, 2)->nullable();

            // Precios nuevos
            $table->decimal('precio_unitario_nuevo', 10, 2);
            $table->decimal('precio_docena_nuevo', 10, 2)->nullable();
            $table->decimal('precio_ciento_nuevo', 10, 2)->nullable();

            $table->timestamps();

            $table->index('presentacion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_precios');
    }
};
