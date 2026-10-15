
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
        Schema::create('venta_detalle_pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('metodo_pago_id')
                ->constrained('metodos_pago')
                ->restrictOnDelete();

            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->restrictOnDelete();

            $table->decimal('monto', 10, 2);

            $table->decimal('monto_recibido', 10, 2)
                ->nullable();

            $table->decimal('cambio', 10, 2)
                ->nullable();

            $table->string('referencia', 20)
                ->nullable();

            $table->dateTime('fecha_pago');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_detalle_pagos');
    }
};
