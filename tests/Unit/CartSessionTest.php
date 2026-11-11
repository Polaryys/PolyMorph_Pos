<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

it('clears the session cart when an empty cart is saved', function () {
    $this->withoutMiddleware(Authenticate::class)
        ->withSession([
            'ventas' => [
                'carrito' => [
                    'cliente_id' => null,
                    'detalles' => [],
                ],
            ],
        ])
        ->postJson(route('ventas.carrito.guardar'), [
            'cliente_id' => null,
            'detalles' => [],
        ])
        ->assertOk()
        ->assertJson(['guardado' => true])
        ->assertSessionMissing('ventas.carrito');
});

it('stores only the selected items and sale inputs in the session', function () {
    Schema::create('presentaciones', function (Blueprint $table) {
        $table->id();
    });
    DB::table('presentaciones')->insert(['id' => 7]);

    $this->withoutMiddleware(Authenticate::class)
        ->postJson(route('ventas.carrito.guardar'), [
            'cliente_id' => null,
            'detalles' => [[
                'presentacion_id' => 7,
                'cantidad' => 2,
                'tipo_precio' => 'docena',
                'descuento' => '1.25',
            ]],
        ])
        ->assertOk()
        ->assertSessionHas('ventas.carrito', [
            'cliente_id' => null,
            'detalles' => [[
                'presentacion_id' => 7,
                'cantidad' => 2,
                'tipo_precio' => 'docena',
                'descuento' => '1.25',
            ]],
        ]);
});
