<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('venta_reembolsos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->restrictOnDelete();

            $table->foreignId('metodo_pago_id')
                ->constrained('metodos_pago')
                ->restrictOnDelete();

            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('monto', 10, 2)->unsigned();
            $table->enum('estado', [
                'PENDIENTE',
                'PROCESADO',
                'ANULADO',
            ])->default('PENDIENTE');
            $table->string('referencia', 100)->nullable();
            $table->dateTime('fecha_reembolso')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_reembolsos');
    }
};
