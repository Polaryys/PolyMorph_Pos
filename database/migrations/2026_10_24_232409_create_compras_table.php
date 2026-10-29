<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();

            $table->string('numero_compra', 20)->unique();

            $table->foreignId('usuario_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('proveedor_id')
                ->nullable()
                ->constrained('proveedores')
                ->nullOnDelete();

            $table->string('numero_factura', 50)->nullable();

            $table->decimal('costo_total', 12, 2)->default(0);

            $table->unsignedInteger('cantidad_total')->default(0);

            $table->text('observaciones')->nullable();

            $table->enum('estado', [
                'PENDIENTE',
                'REGISTRADA',
                'REVERSADA',
            ])->default('PENDIENTE');

            $table->date('fecha_compra');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
