<?php

namespace App\Http\Requests\Concerns;

use App\Models\MetodoPago;
use Illuminate\Validation\Validator;

trait ValidaModoCapturaPagos
{
    protected function validarModoCapturaPagos(Validator $validator, array $pagos): void
    {
        if ($pagos === []) {
            return;
        }

        $metodosPago = MetodoPago::query()
            ->whereIn('id', array_column($pagos, 'metodo_pago_id'))
            ->get()
            ->keyBy('id');

        foreach ($pagos as $indice => $pago) {
            $metodoPago = $metodosPago->get($pago['metodo_pago_id']);
            $montoRecibido = $pago['monto_recibido'] ?? null;

            if ($metodoPago?->tipo_captura === MetodoPago::TIPO_CAPTURA_MONTOS) {
                if ((float) $pago['monto'] < 0 && $montoRecibido !== null) {
                    $validator->errors()->add(
                        "pagos.{$indice}.monto_recibido",
                        'No se registra monto recibido al realizar un reembolso.'
                    );
                } elseif ((float) $pago['monto'] > 0 && ($montoRecibido === null || (float) $montoRecibido < (float) $pago['monto'])) {
                    $validator->errors()->add(
                        "pagos.{$indice}.monto_recibido",
                        'El monto recibido debe cubrir el monto aplicado.'
                    );
                }

                continue;
            }

            if ($montoRecibido !== null) {
                $validator->errors()->add(
                    "pagos.{$indice}.monto_recibido",
                    'Este método de pago no permite registrar monto recibido ni cambio.'
                );
            }
        }
    }
}
