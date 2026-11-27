<?php

namespace App\Http\Controllers\Reportes;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteGeneral extends Controller
{
    public function index(Request $request)
    {
        // Validar semana seleccionada.
        $request->validate([
            'semana' => ['nullable', 'regex:/^\d{4}-W\d{2}$/'],
        ]);

        // Semana actual por defecto.
        $semana = $request->input('semana', now()->format('o-\WW'));

        [$anio, $numeroSemana] = sscanf($semana, '%d-W%d');

        $inicio = Carbon::now()
            ->setISODate($anio, $numeroSemana, 1)
            ->startOfDay();

        // Validar que la semana ISO exista.
        if ($inicio->format('o-\WW') !== $semana) {
            return back()->withErrors([
                'semana' => 'La semana seleccionada no es válida.',
            ]);
        }

        $fin = $inicio->copy()->addDays(6)->endOfDay();

        // Ventas netas por fecha de pago.
        $ventasPorDia = DB::table('venta_detalle_pagos')
            ->join('ventas', 'venta_detalle_pagos.venta_id', '=', 'ventas.id')
            ->whereBetween('venta_detalle_pagos.fecha_pago', [$inicio, $fin])
            ->whereIn('ventas.tipo_operacion', ['VENTA', 'CAMBIO', 'DEVOLUCION'])
            ->where('ventas.estado_venta', 'REGISTRADA')
            ->selectRaw('
                DATE(venta_detalle_pagos.fecha_pago) as fecha,
                COALESCE(SUM(venta_detalle_pagos.monto), 0) as ventas_netas
            ')
            ->groupByRaw('DATE(venta_detalle_pagos.fecha_pago)')
            ->get()
            ->keyBy('fecha');

        // Utilidad bruta por fecha de operación.
        $utilidadPorDia = DB::table('venta_detalles')
            ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
            ->whereBetween('ventas.created_at', [$inicio, $fin])
            ->whereIn('ventas.tipo_operacion', ['VENTA', 'CAMBIO', 'DEVOLUCION'])
            ->where('ventas.estado_venta', 'REGISTRADA')
            ->selectRaw('
                DATE(ventas.created_at) as fecha,
                COALESCE(SUM(
                    CASE
                        WHEN venta_detalles.tipo_movimiento = "DEVOLUCION"
                            THEN -1
                        ELSE 1
                    END
                    *
                    CASE
                        WHEN venta_detalles.tipo_item = "SERVICIO"
                            THEN venta_detalles.subtotal
                        ELSE venta_detalles.subtotal -
                            COALESCE(venta_detalles.subtotal_costo, 0)
                    END
                ), 0) as utilidad
            ')
            ->groupByRaw('DATE(ventas.created_at)')
            ->get()
            ->keyBy('fecha');

        // Construir los siete días de la semana.
        $nombresDias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

        $resumenDiario = collect(range(0, 6))->map(function ($dia) use ($inicio, $ventasPorDia, $utilidadPorDia, $nombresDias) {
            $fecha = $inicio->copy()->addDays($dia)->toDateString();

            return [
                'fecha' => $fecha,
                'dia' => $nombresDias[$dia],
                'ventas' => (float) ($ventasPorDia->get($fecha)->ventas_netas ?? 0),
                'utilidad' => (float) ($utilidadPorDia->get($fecha)->utilidad ?? 0),
            ];
        });

        // Productos más vendidos de la semana.
        $productosMasVendidos = DB::table('venta_detalles')
            ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
            ->join('presentaciones', 'venta_detalles.presentacion_id', '=', 'presentaciones.id')
            ->join('inventario', 'presentaciones.inventario_id', '=', 'inventario.id')
            ->leftJoin('marcas', 'inventario.marca_id', '=', 'marcas.id')
            ->join('presentaciones as presentacion_base', function ($join) {
                $join->on('presentacion_base.inventario_id', '=', 'inventario.id')
                    ->where('presentacion_base.base', true);
            })
            ->whereBetween('ventas.created_at', [$inicio, $fin])
            ->whereIn('ventas.tipo_operacion', ['VENTA', 'CAMBIO', 'DEVOLUCION'])
            ->where('ventas.estado_venta', 'REGISTRADA')
            ->where('inventario.tipo', 'PRODUCTO')
            ->where('venta_detalles.tipo_item', 'PRODUCTO')
            ->whereIn('venta_detalles.tipo_movimiento', [
                'VENTA',
                'DEVOLUCION',
                'ENTREGA_CAMBIO',
            ])
            ->select(
                'inventario.id',
                'presentacion_base.nombre as producto'
            )
            ->selectRaw("
                CONCAT(
                    presentacion_base.nombre,
                    CASE
                        WHEN marcas.nombre IS NOT NULL AND marcas.nombre != ''
                            THEN CONCAT(' ', marcas.nombre)
                        ELSE ''
                    END
                ) as nombre_completo
            ")
            ->selectRaw('
                SUM(
                    CASE
                        WHEN venta_detalles.tipo_movimiento = "DEVOLUCION"
                            THEN -venta_detalles.cantidad_base
                        ELSE venta_detalles.cantidad_base
                    END
                ) as cantidad_neta
            ')
            ->selectRaw('
                SUM(
                    CASE
                        WHEN venta_detalles.tipo_movimiento = "DEVOLUCION"
                    THEN -venta_detalles.subtotal
                        ELSE venta_detalles.subtotal
                    END
                ) as total_vendido
            ')
            ->groupBy('inventario.id', 'presentacion_base.nombre')
            ->havingRaw('cantidad_neta > 0')
            ->orderByDesc('cantidad_neta')
            ->limit(12)
            ->get();

        return view('reportes.resumen-general', [
            'semana' => $semana,
            'inicio' => $inicio,
            'fin' => $fin,
            'resumenDiario' => $resumenDiario,
            'ventasSemana' => $resumenDiario->sum('ventas'),
            'utilidadSemana' => $resumenDiario->sum('utilidad'),
            'productosMasVendidos' => $productosMasVendidos,
        ]);
    }
}