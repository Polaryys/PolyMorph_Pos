
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
        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->restrictOnDelete();

            $table->foreignId('presentacion_id')
                ->constrained('presentaciones')
                ->restrictOnDelete();

            $table->enum('tipo_item', [
                'Producto',
                'Servicio',
            ]);

            $table->decimal('cantidad', 10, 2);
            $table->unsignedInteger('factor');
            $table->decimal('cantidad_base', 10, 2);

            $table->decimal('costo_unitario', 10, 2)
                ->nullable();

            $table->decimal('subtotal_costo', 10, 2)
                ->nullable();

            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('precio_aplicado', 10, 2);
            $table->decimal('descuento', 10, 2)
                ->default(0);

            // Total del detalle después de descuentos
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_detalles');
    }
};
