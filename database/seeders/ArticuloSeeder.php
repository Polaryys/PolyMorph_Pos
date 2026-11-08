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
                'nombre' => 'Cuaderno profesional de 100 hojas',
                'detalle' => 'Cuaderno rayado tamaño carta, pasta resistente.',
                'precio_unitario' => 28.00,
                'precio_docena' => 25.00,
                'precio_ciento' => 22.00,
                'umbral' => 10,
            ],
            [
                'tipo' => 'PRODUCTO',
                'sku' => 'DEMO-PROD-002',
                'nombre' => 'Bolígrafo azul punto mediano',
                'detalle' => 'Bolígrafo de tinta azul para uso escolar y oficina.',
                'precio_unitario' => 4.00,
                'precio_docena' => 3.50,
                'precio_ciento' => 3.00,
                'umbral' => 20,
            ],
            [
                'tipo' => 'PRODUCTO',
                'sku' => 'DEMO-PROD-003',
                'nombre' => 'Lápiz de grafito HB',
                'detalle' => 'Lápiz de grafito HB con borrador integrado.',
                'precio_unitario' => 3.00,
                'precio_docena' => 2.50,
                'precio_ciento' => 2.00,
                'umbral' => 20,
            ],
            [
                'tipo' => 'PRODUCTO',
                'sku' => 'DEMO-PROD-004',
                'nombre' => 'Borrador blanco para lápiz',
                'detalle' => 'Borrador suave que no daña el papel.',
                'precio_unitario' => 2.50,
                'precio_docena' => 2.00,
                'precio_ciento' => 1.50,
                'umbral' => 15,
            ],
            [
                'tipo' => 'PRODUCTO',
                'sku' => 'DEMO-PROD-005',
                'nombre' => 'Resma de papel bond tamaño carta',
                'detalle' => 'Resma de 500 hojas de papel bond blanco.',
                'precio_unitario' => 42.00,
                'precio_docena' => 39.00,
                'precio_ciento' => 35.00,
                'umbral' => 8,
            ],
            [
                'tipo' => 'SERVICIO',
                'sku' => 'DEMO-SERV-001',
                'nombre' => 'Fotocopia en blanco y negro',
                'detalle' => 'Servicio de fotocopiado por página en tamaño carta.',
                'precio_unitario' => 0.50,
                'umbral' => 0,
            ],
            [
                'tipo' => 'SERVICIO',
                'sku' => 'DEMO-SERV-002',
                'nombre' => 'Impresión en blanco y negro',
                'detalle' => 'Impresión láser por página en tamaño carta.',
                'precio_unitario' => 1.00,
                'umbral' => 0,
            ],
            [
                'tipo' => 'SERVICIO',
                'sku' => 'DEMO-SERV-003',
                'nombre' => 'Impresión a color',
                'detalle' => 'Impresión a color por página en tamaño carta.',
                'precio_unitario' => 3.50,
                'umbral' => 0,
            ],
            [
                'tipo' => 'SERVICIO',
                'sku' => 'DEMO-SERV-004',
                'nombre' => 'Engargolado',
                'detalle' => 'Servicio de engargolado de documentos.',
                'precio_unitario' => 18.00,
                'umbral' => 0,
            ],
            [
                'tipo' => 'SERVICIO',
                'sku' => 'DEMO-SERV-005',
                'nombre' => 'Plastificado tamaño carta',
                'detalle' => 'Plastificado de documentos tamaño carta.',
                'precio_unitario' => 12.00,
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
