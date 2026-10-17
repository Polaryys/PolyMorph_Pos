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
        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['PRODUCTO', 'SERVICIO']);
            $table->decimal('costoPromedioUnitario', 10, 2)->nullable()->default(0);
            $table->integer('cantidad')->nullable()->default(0);
            $table->integer('umbral')->nullable()->default(0);
            $table->foreignId('marca_id')
                ->nullable()
                ->constrained('marcas')
                ->onDelete('set null');
            $table->foreignId('categoria_id')
                ->nullable()
                ->constrained('categorias')
                ->onDelete('set null');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};
