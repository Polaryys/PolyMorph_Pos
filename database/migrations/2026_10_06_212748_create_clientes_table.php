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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nit', 15);
            $table->string('nombres', 100);
            $table->string('apellidos', 100)->nullable();
            $table->string('telefono', 15)->nullable();
            $table->enum('clasificacion', ['Maroyista', 'Minorista']);
            $table->string('notas', 255)->nullable();
            $table->string('correo', 100)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
