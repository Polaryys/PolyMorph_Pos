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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_venta', 20)->unique();
            $table->foreignId('vendedor_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('cliente_id')
                ->nullable()
                ->constrained('clientes')
                ->restrictOnDelete();
            $table->enum('tipo_operacion', [
                'VENTA',
                'DEVOLUCION',
                'CAMBIO',
            ])->default('VENTA');
            $table->enum('estado_venta', [
                'REGISTRADA',
                'ANULADA',
            ])->default('REGISTRADA');
            $table->enum('estado_pago', [
                'PENDIENTE',
                'PARCIAL',
                'PAGADO',
            ])->default('PENDIENTE');
            $table->foreignId('venta_referencia_id')
                ->nullable()
                ->constrained('ventas')
                ->restrictOnDelete();
            $table->text('motivo_operacion')->nullable();
            $table->decimal('total_descuentos', 10, 2)->default(0);
            $table->decimal('costo_total', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
