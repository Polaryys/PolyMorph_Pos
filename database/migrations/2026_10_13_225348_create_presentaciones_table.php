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
        Schema::create('presentaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_id')
                ->constrained('inventarios');
            $table->string('codigoSKU', 50)->unique();
            $table->string('codigoBarras', 50)->nullable()->unique();
            $table->string('nombre', 100);
            $table->text('detalle')->nullable();
            $table->integer('factor')->default(1);
            $table->boolean('base');
            $table->decimal('precioUnitario', 10, 2)->default(0);
            $table->decimal('precioUnitarioDocena', 10, 2)->nullable();
            $table->decimal('precioUnitarioCiento', 10, 2)->nullable();
            $table->foreignId('imagen_id')
                ->nullable()
                ->constrained('imagenes')
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
        Schema::dropIfExists('presentaciones');
    }
};
