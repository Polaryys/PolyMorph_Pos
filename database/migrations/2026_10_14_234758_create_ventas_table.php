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
            $table->string('numero_venta', 20)->unique;
            $table->foreignId('vendedor_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('cliente_id')
                ->nullable()->constrained('clientes')
                ->nullOnDelete();
            $table->enum('tipo_transacción', ['ENTRADA', 'SALIDA', 'CAMBIO',]);
            $table->enum('estadp_venta', ['Completada', 'Reembolsada', 'Cambiado',]);
            $table->enum('estado_pago', ['Parcial', 'Completo', 'Pendiente']);
            $table->foreignId('venta_referencia_id')
                ->nullable()
                ->constrained('ventas')
                ->restrictOnDelete();
            $table->decimal('total_descuentos', 10, 2);
            $table->decimal('costo_total', 10, 2);
            $table->decimal('total', 10, 2);
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
