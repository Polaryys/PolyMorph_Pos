<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_idempotencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('accion', 40);
            $table->uuid('clave');
            $table->char('hash_solicitud', 64);
            $table->foreignId('venta_id')
                ->nullable()
                ->constrained('ventas')
                ->restrictOnDelete();
            $table->timestamps();

            $table->unique(
                ['usuario_id', 'accion', 'clave'],
                'solicitudes_idempotencia_usuario_accion_clave_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_idempotencia');
    }
};
