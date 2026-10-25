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

            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('tipo_precio', [
                'UNITARIO',
                'DOCENA',
                'CIENTO',
            ]);

            $table->decimal('precio_anterior', 10, 2);
            $table->decimal('precio_nuevo', 10, 2);

            $table->timestamps();
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
