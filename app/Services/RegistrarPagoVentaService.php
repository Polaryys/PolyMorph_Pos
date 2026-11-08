<?php

namespace App\Services;

use App\Models\Venta;
use Illuminate\Validation\ValidationException;

class RegistrarPagoVentaService
{
    public function __construct(private IdempotencyService $idempotencyService) {}

    public function registrar(array $datos, Venta $venta, int $userId): void
    {
        $idempotencyKey = $datos['idempotency_key'];
        unset($datos['idempotency_key']);

        $this->idempotencyService->execute(
            (int) $userId,
            'registrar_pago',
            $idempotencyKey,
            ['venta_id' => $venta->id, 'pagos' => $datos['pagos']],
            function () use ($datos, $venta) {
                $venta = Venta::query()
                    ->whereKey($venta->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($venta->estado_venta !== 'REGISTRADA') {
                    throw ValidationException::withMessages([
                        'pagos' => 'Solo se pueden registrar movimientos para operaciones registradas.',
                    ]);
                }

                $pagos = $datos['pagos'];
                $montoNuevo = round(array_sum(array_column($pagos, 'monto')), 2);
                $montoPagado = round((float) $venta->detallesPago()->sum('monto'), 2);
                $saldoPendiente = round((float) $venta->total - $montoPagado, 2);

                if ($saldoPendiente === 0.0) {
                    throw ValidationException::withMessages([
                        'pagos' => 'Esta operación ya no tiene saldo pendiente.',
                    ]);
                }

                $signoIncorrecto = $saldoPendiente > 0
                    ? collect($pagos)->contains(fn (array $pago) => (float) $pago['monto'] <= 0)
                    : collect($pagos)->contains(fn (array $pago) => (float) $pago['monto'] >= 0);

                if ($signoIncorrecto || ($montoNuevo > 0) !== ($saldoPendiente > 0)) {
                    throw ValidationException::withMessages([
                        'pagos' => 'Los cobros deben ser positivos y los reembolsos negativos, según el saldo pendiente.',
                    ]);
                }

                if (abs($montoNuevo) > abs($saldoPendiente)) {
                    throw ValidationException::withMessages([
                        'pagos' => 'La suma de los movimientos no puede superar el saldo pendiente.',
                    ]);
                }

                foreach ($pagos as $pago) {
                    $montoRecibido = $pago['monto_recibido'] ?? null;

                    if ($montoRecibido !== null && (float) $pago['monto'] > 0 && (float) $montoRecibido < (float) $pago['monto']) {
                        throw ValidationException::withMessages([
                            'pagos' => 'El monto recibido no puede ser menor al monto aplicado.',
                        ]);
                    }

                    $venta->detallesPago()->create([
                        'metodo_pago_id' => $pago['metodo_pago_id'],
                        'monto' => $pago['monto'],
                        'monto_recibido' => $montoRecibido,
                        'cambio' => $montoRecibido === null || (float) $pago['monto'] < 0
                            ? null
                            : round((float) $montoRecibido - (float) $pago['monto'], 2),
                        'referencia' => $pago['referencia'] ?? null,
                        'fecha_pago' => now(),
                    ]);
                }

                $montoPagadoNuevo = round($montoPagado + $montoNuevo, 2);
                $venta->estado_pago = $this->calcularEstadoPago((float) $venta->total, $montoPagadoNuevo);
                $venta->save();

                return $venta;
            }
        );
    }

    private function calcularEstadoPago(float $total, float $montoPagado): string
    {
        $total = round($total, 2);
        $montoPagado = round($montoPagado, 2);

        if ($total === 0.0) {
            return 'PAGADO';
        }

        if ($total < 0) {
            if (abs($montoPagado) >= abs($total)) {
                return 'REEMBOLSADO';
            }

            return $montoPagado === 0.0 ? 'PENDIENTE' : 'PARCIAL';
        }

        if ($montoPagado >= $total) {
            return 'PAGADO';
        }

        return $montoPagado === 0.0 ? 'PENDIENTE' : 'PARCIAL';
    }
}
