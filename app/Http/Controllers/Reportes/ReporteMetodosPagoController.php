<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteMetodosPagoController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'fecha' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $fecha = $request->input('fecha', now()->toDateString());

        $ventasDia = DB::table('venta_detalle_pagos')
            ->join('ventas', 'venta_detalle_pagos.venta_id', '=', 'ventas.id')
            ->whereDate('venta_detalle_pagos.fecha_pago', $fecha)
            ->whereIn('ventas.tipo_operacion', ['VENTA', 'CAMBIO', 'DEVOLUCION'])
            ->where('ventas.estado_venta', 'REGISTRADA')
            ->sum('venta_detalle_pagos.monto');

        $utilidadBruta = DB::table('venta_detalles')
            ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
            ->whereDate('ventas.created_at', $fecha)
            ->whereIn('ventas.tipo_operacion', ['VENTA', 'CAMBIO', 'DEVOLUCION'])
            ->where('ventas.estado_venta', 'REGISTRADA')
            ->selectRaw('
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
            ->value('utilidad');

        $pagosPorMetodo = DB::table('venta_detalle_pagos as pagos')
            ->join('ventas', 'pagos.venta_id', '=', 'ventas.id')
            ->whereDate('pagos.fecha_pago', $fecha)
            ->whereIn('ventas.tipo_operacion', ['VENTA', 'CAMBIO', 'DEVOLUCION'])
            ->where('ventas.estado_venta', 'REGISTRADA')
            ->groupBy('pagos.metodo_pago_id')
            ->select('pagos.metodo_pago_id')
            ->selectRaw('
                SUM(CASE
                    WHEN pagos.monto > 0 THEN pagos.monto
                    ELSE 0
                END) as cobrado
            ')
                    ->selectRaw('
                SUM(CASE
                    WHEN pagos.monto < 0 THEN ABS(pagos.monto)
                    ELSE 0
                END) as reembolsado
            ');

        $resumenMetodosPago = DB::table('metodos_pago')
            ->leftJoinSub($pagosPorMetodo, 'resumen', function ($join) {
                $join->on('metodos_pago.id', '=', 'resumen.metodo_pago_id');
            })
            ->select(
                'metodos_pago.nombre',
                DB::raw('COALESCE(resumen.cobrado, 0) as cobrado'),
                DB::raw('COALESCE(resumen.reembolsado, 0) as reembolsado'),
                DB::raw('COALESCE(resumen.cobrado, 0) - COALESCE(resumen.reembolsado, 0) as neto')
            )
            ->orderBy('metodos_pago.nombre')
            ->get();


        return view('reportes.pagos', [
            'fecha' => $fecha,
            'ventasDia' => $ventasDia,
            'utilidadBruta' => $utilidadBruta,
            'resumenMetodosPago' => $resumenMetodosPago,
        ]);
    }
}