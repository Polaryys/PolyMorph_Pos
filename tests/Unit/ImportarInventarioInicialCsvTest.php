<?php

use App\Models\Inventario;
use App\Models\Presentacion;
use App\Services\ImportarInventarioInicialCsv;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Schema::create('marcas', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->timestamps();
    });

    Schema::create('inventario', function (Blueprint $table) {
        $table->id();
        $table->string('tipo');
        $table->decimal('costoPromedioUnitario', 10, 2)->nullable();
        $table->integer('cantidad')->nullable();
        $table->integer('umbral')->nullable();
        $table->unsignedBigInteger('marca_id')->nullable();
        $table->unsignedBigInteger('categoria_id')->nullable();
        $table->boolean('estado');
        $table->timestamps();
    });

    Schema::create('presentaciones', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('inventario_id');
        $table->string('codigoSKU');
        $table->string('nombre');
        $table->string('ubicacion', 100)->nullable();
        $table->integer('factor');
        $table->boolean('base');
        $table->decimal('precioUnitario', 10, 2);
        $table->decimal('precioUnitarioDocena', 10, 2)->nullable();
        $table->decimal('precioUnitarioCiento', 10, 2)->nullable();
        $table->boolean('estado');
        $table->timestamps();
    });

    Schema::create('movimientos', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('usuario_id');
        $table->unsignedBigInteger('inventario_id');
        $table->string('tipo_movimiento');
        $table->integer('cantidad_anterior');
        $table->integer('cantidad');
        $table->integer('cantidad_posterior');
        $table->string('motivo');
        $table->timestamps();
    });
});

it('imports inventory without a conversion-factor column and sets the factor to one', function () {
    $archivo = UploadedFile::fake()->createWithContent(
        'inventario.csv',
        implode(',', [
            'SKU',
            'Tipo',
            'Nombre',
            'Marca',
            'Existencias actuales',
            'Costo unitario',
            'Precio unitario',
            'Precio unitario por docena',
            'Precio unitario por ciento',
            'Stock mínimo',
        ])."\nSKU-001,PRODUCTO,Artículo de prueba,Marca de prueba,5,10,20,,,1\n"
    );

    $cantidadImportada = app(ImportarInventarioInicialCsv::class)->importar($archivo, 1);

    expect($cantidadImportada)->toBe(1)
        ->and(Presentacion::query()->where('codigoSKU', 'SKU-001')->value('factor'))->toBe(1);
});

it('allows zero stock and minimum stock for services', function () {
    $archivo = UploadedFile::fake()->createWithContent(
        'inventario.csv',
        implode(',', [
            'SKU',
            'Tipo',
            'Nombre',
            'Marca',
            'Existencias actuales',
            'Costo unitario',
            'Precio unitario',
            'Precio unitario por docena',
            'Precio unitario por ciento',
            'Stock mínimo',
        ])."\nSKU-SERVICIO,SERVICIO,Servicio de prueba,Marca de prueba,0,10,20,,,0\n"
    );

    $cantidadImportada = app(ImportarInventarioInicialCsv::class)->importar($archivo, 1);
    $inventario = Inventario::query()->firstOrFail();

    expect($cantidadImportada)->toBe(1)
        ->and($inventario->cantidad)->toBe(0)
        ->and($inventario->umbral)->toBe(0);
});

it('continues requiring positive stock values for products', function () {
    $archivo = UploadedFile::fake()->createWithContent(
        'inventario.csv',
        implode(',', [
            'SKU',
            'Tipo',
            'Nombre',
            'Marca',
            'Existencias actuales',
            'Costo unitario',
            'Precio unitario',
            'Precio unitario por docena',
            'Precio unitario por ciento',
            'Stock mínimo',
        ])."\nSKU-PRODUCTO,PRODUCTO,Producto de prueba,Marca de prueba,0,10,20,,,0\n"
    );

    expect(fn () => app(ImportarInventarioInicialCsv::class)->importar($archivo, 1))
        ->toThrow(ValidationException::class);
});

it('imports an optional presentation location', function () {
    $archivo = UploadedFile::fake()->createWithContent(
        'inventario.csv',
        implode(',', [
            'SKU',
            'Tipo',
            'Nombre',
            'Marca',
            'Ubicación',
            'Existencias actuales',
            'Costo unitario',
            'Precio unitario',
            'Precio unitario por docena',
            'Precio unitario por ciento',
            'Stock mínimo',
        ])."\nSKU-UBICACION,PRODUCTO,Producto de prueba,Marca de prueba,Bodega A-1,5,10,20,,,1\n"
    );

    app(ImportarInventarioInicialCsv::class)->importar($archivo, 1);

    expect(Presentacion::query()->where('codigoSKU', 'SKU-UBICACION')->value('ubicacion'))
        ->toBe('Bodega A-1');
});
