<?php

namespace Database\Seeders;

use App\Models\Inventario;
use App\Models\Presentacion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticuloSeeder extends Seeder
{
    public function run(): void
    {
        $articulos = [
            [
                'tipo' => 'PRODUCTO',
                'sku' => 'DEMO-PROD-001',
                'nombre' => 'Cable USB-C de 1 metro',
                'detalle' => 'Cable de carga y transferencia USB-C.',
                'precio_unitario' => 45.00,
                'precio_docena' => 40.00,
                'precio_ciento' => 35.00,
                'umbral' => 5,
            ],
            [
                'tipo' => 'PRODUCTO',
                'sku' => 'DEMO-PROD-002',
                'nombre' => 'Cargador USB-C de 20 W',
                'detalle' => 'Adaptador de corriente USB-C.',
                'precio_unitario' => 95.00,
                'precio_docena' => 88.00,
                'precio_ciento' => 80.00,
                'umbral' => 5,
            ],
            [
                'tipo' => 'PRODUCTO',
                'sku' => 'DEMO-PROD-003',
                'nombre' => 'Mouse inalámbrico',
                'detalle' => 'Mouse óptico inalámbrico.',
                'precio_unitario' => 75.00,
                'precio_docena' => 68.00,
                'precio_ciento' => 60.00,
                'umbral' => 3,
            ],
            [
                'tipo' => 'PRODUCTO',
                'sku' => 'DEMO-PROD-004',
                'nombre' => 'Teclado USB',
                'detalle' => 'Teclado alámbrico de conexión USB.',
                'precio_unitario' => 130.00,
                'precio_docena' => 120.00,
                'precio_ciento' => 110.00,
                'umbral' => 3,
            ],
            [
                'tipo' => 'PRODUCTO',
                'sku' => 'DEMO-PROD-005',
                'nombre' => 'Audífonos estéreo',
                'detalle' => 'Audífonos estéreo con conexión alámbrica.',
                'precio_unitario' => 85.00,
                'precio_docena' => 78.00,
                'precio_ciento' => 70.00,
                'umbral' => 4,
            ],
            [
                'tipo' => 'SERVICIO',
                'sku' => 'DEMO-SERV-001',
                'nombre' => 'Diagnóstico técnico',
                'detalle' => 'Evaluación inicial del equipo.',
                'precio_unitario' => 75.00,
                'umbral' => 0,
            ],
            [
                'tipo' => 'SERVICIO',
                'sku' => 'DEMO-SERV-002',
                'nombre' => 'Mantenimiento preventivo',
                'detalle' => 'Limpieza y revisión preventiva del equipo.',
                'precio_unitario' => 150.00,
                'umbral' => 0,
            ],
            [
                'tipo' => 'SERVICIO',
                'sku' => 'DEMO-SERV-003',
                'nombre' => 'Instalación básica',
                'detalle' => 'Instalación de un componente o periférico.',
                'precio_unitario' => 100.00,
                'umbral' => 0,
            ],
            [
                'tipo' => 'SERVICIO',
                'sku' => 'DEMO-SERV-004',
                'nombre' => 'Configuración de software',
                'detalle' => 'Configuración inicial de software en el equipo.',
                'precio_unitario' => 80.00,
                'umbral' => 0,
            ],
            [
                'tipo' => 'SERVICIO',
                'sku' => 'DEMO-SERV-005',
                'nombre' => 'Limpieza interna',
                'detalle' => 'Limpieza interna de componentes del equipo.',
                'precio_unitario' => 120.00,
                'umbral' => 0,
            ],
        ];

        DB::transaction(function () use ($articulos): void {
            foreach ($articulos as $articulo) {
                $presentacion = Presentacion::query()
                    ->where('codigoSKU', $articulo['sku'])
                    ->first();

                $inventario = $presentacion?->inventario;

                if ($inventario === null) {
                    $inventario = Inventario::create([
                        'tipo' => $articulo['tipo'],
                        'cantidad' => 0,
                        'costoPromedioUnitario' => 0,
                        'umbral' => $articulo['umbral'],
                        'estado' => true,
                    ]);
                } else {
                    $inventario->update([
                        'tipo' => $articulo['tipo'],
                        'umbral' => $articulo['umbral'],
                        'estado' => true,
                    ]);
                }

                $presentacion ??= new Presentacion;
                $presentacion->fill([
                    'codigoSKU' => $articulo['sku'],
                    'nombre' => $articulo['nombre'],
                    'detalle' => $articulo['detalle'],
                    'factor' => 1,
                    'base' => true,
                    'precioUnitario' => $articulo['precio_unitario'],
                    'precioUnitarioDocena' => $articulo['precio_docena'] ?? null,
                    'precioUnitarioCiento' => $articulo['precio_ciento'] ?? null,
                    'estado' => true,
                ]);
                $inventario->presentaciones()->save($presentacion);
            }
        });
    }
}
