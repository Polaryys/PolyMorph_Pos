<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidaModoCapturaPagos;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreVentaRequest extends FormRequest
{
    use ValidaModoCapturaPagos;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idempotency_key' => ['required', 'uuid'],
            'tipo_operacion' => ['sometimes', 'required', 'string', 'in:VENTA,DEVOLUCION,CAMBIO'],
            'venta_credito' => ['sometimes', 'boolean'],
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'venta_referencia_id' => ['required_if:tipo_operacion,DEVOLUCION,CAMBIO', 'nullable', 'integer', 'exists:ventas,id'],
            'motivo_operacion' => ['required_if:tipo_operacion,DEVOLUCION,CAMBIO', 'nullable', 'string', 'max:1000'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.presentacion_id' => ['nullable', 'integer', 'exists:presentaciones,id'],
            'detalles.*.tipo_movimiento' => ['sometimes', 'string', 'in:VENTA,DEVOLUCION,ENTREGA_CAMBIO'],
            'detalles.*.detalle_referencia_id' => ['nullable', 'integer', 'distinct', 'exists:venta_detalles,id'],
            'detalles.*.reingresa_inventario' => ['nullable', 'boolean'],
            'detalles.*.cantidad' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'detalles.*.tipo_precio' => ['sometimes', 'string', 'in:unitario,docena,ciento'],
            'detalles.*.descuento' => ['sometimes', 'numeric', 'min:0', 'decimal:0,2'],
            'pagos' => ['sometimes', 'array'],
            'pagos.*.metodo_pago_id' => [
                'required',
                'integer',
                Rule::exists('metodos_pago', 'id')->where('estado', true),
            ],
            'pagos.*.monto' => ['required', 'numeric', 'decimal:0,2'],
            'pagos.*.monto_recibido' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'pagos.*.referencia' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $datos = $validator->getData();
                $this->validarModoCapturaPagos($validator, $datos['pagos'] ?? []);

                $tipoOperacion = $datos['tipo_operacion'] ?? 'VENTA';
                $movimientosDetalle = collect($datos['detalles'])->map(
                    fn (array $detalle) => $detalle['tipo_movimiento']
                        ?? ($tipoOperacion === 'VENTA' ? 'VENTA' : null)
                );

                if (
                    ($tipoOperacion === 'VENTA' && $movimientosDetalle->contains(fn ($tipo) => $tipo !== 'VENTA'))
                    || ($tipoOperacion === 'DEVOLUCION' && $movimientosDetalle->contains(fn ($tipo) => $tipo !== 'DEVOLUCION'))
                    || ($tipoOperacion === 'CAMBIO' && (
                        ! $movimientosDetalle->contains('DEVOLUCION')
                        || ! $movimientosDetalle->contains('ENTREGA_CAMBIO')
                        || $movimientosDetalle->contains(fn ($tipo) => ! in_array($tipo, ['DEVOLUCION', 'ENTREGA_CAMBIO'], true))
                    ))
                ) {
                    $validator->errors()->add('detalles', 'Los artículos seleccionados no corresponden al tipo de operación.');
                }

                if ($tipoOperacion === 'VENTA' && ! empty($datos['venta_referencia_id'])) {
                    $validator->errors()->add('tipo_operacion', 'Una venta normal no puede tener una venta de referencia.');
                }
            },
        ];
    }
}
