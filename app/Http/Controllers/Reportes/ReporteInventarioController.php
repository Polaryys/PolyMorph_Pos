<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use Illuminate\Http\Request;

class ReporteInventarioController extends Controller
{

    public function inventarioReporte(Request $request)
    {
        // 1. Resumen del inventario
        $resumen = Inventario::query()
            ->where('tipo', 'PRODUCTO')
            ->where('estado', true)
            ->whereHas('presentacionBase', function ($query) {
                $query->where('estado', true);
            })
            ->selectRaw("
            COUNT(*) AS productos,
            COALESCE(SUM(cantidad * costoPromedioUnitario), 0) AS valorInventario
        ")
            ->first();

        // 2. Consulta base de productos
        $consultaBase = Inventario::query()
            ->where('tipo', 'PRODUCTO')
            ->where('estado', true)
            ->where('umbral', '>', 0)
            ->with([
                'marca',
                'presentacionBase'
            ]);

        // 3. Productos agotados
        $agotados = (clone $consultaBase)
            ->where('cantidad', 0)
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'pagina_agotados')
            ->withQueryString();

        // 4. Productos con stock bajo
        $consultaBajo = (clone $consultaBase)
            ->where('cantidad', '>', 0)
            ->whereColumn('cantidad', '<', 'umbral');

        // Aplicar filtro de stock
        switch ($request->input('estado')) {

            case 'critico':
                $consultaBajo->whereRaw('cantidad <= umbral * 0.25');
                break;

            case 'muy_bajo':
                $consultaBajo->whereRaw('cantidad > umbral * 0.25')
                    ->whereRaw('cantidad <= umbral * 0.50');
                break;

            case 'bajo':
                $consultaBajo->whereRaw('cantidad > umbral * 0.50');
                break;
        }

        // 5. Paginar productos con stock bajo
        $productos = $consultaBajo
            ->orderBy('cantidad')
            ->paginate(15, ['*'], 'pagina_bajo')
            ->withQueryString();

        return view('reportes.inventario', compact(
            'resumen',
            'productos',
            'agotados'
        ));
    }
}