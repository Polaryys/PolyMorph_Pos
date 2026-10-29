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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('inventario_id')
                ->constrained('inventario')
                ->restrictOnDelete();

            $table->enum('tipo_movimiento', [
                'ENTRADA',
                'SALIDA',
            ]);

            $table->unsignedInteger('cantidad_anterior');
            $table->unsignedInteger('cantidad');
            $table->unsignedInteger('cantidad_posterior');

            $table->text('motivo')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};