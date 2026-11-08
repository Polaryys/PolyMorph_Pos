<?php

namespace App\Services;

use App\Models\SolicitudIdempotencia;
use App\Models\Venta;
use Closure;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use LogicException;

class IdempotencyService
{
    public function execute(
        int $userId,
        string $action,
        string $key,
        array $payload,
        Closure $operation
    ): Venta {
        $requestHash = hash(
            'sha256',
            json_encode($this->canonicalize($payload), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
        );

        return DB::transaction(function () use ($userId, $action, $key, $requestHash, $operation) {
            try {
                $request = SolicitudIdempotencia::create([
                    'usuario_id' => $userId,
                    'accion' => $action,
                    'clave' => $key,
                    'hash_solicitud' => $requestHash,
                ]);
            } catch (UniqueConstraintViolationException $exception) {
                $request = SolicitudIdempotencia::query()
                    ->where('usuario_id', $userId)
                    ->where('accion', $action)
                    ->where('clave', $key)
                    ->lockForUpdate()
                    ->first();

                if (! $request) {
                    throw $exception;
                }

                if (! hash_equals($request->hash_solicitud, $requestHash)) {
                    abort(409, 'Esta clave ya fue utilizada con otros datos. Inicia una operación nueva.');
                }

                if (! $request->venta_id) {
                    throw new LogicException('La solicitud idempotente no tiene una venta asociada.');
                }

                return Venta::query()->findOrFail($request->venta_id);
            }

            $venta = $operation();
            $request->venta()->associate($venta);
            $request->save();

            return $venta;
        });
    }

    private function canonicalize(array $payload): array
    {
        if (! array_is_list($payload)) {
            ksort($payload);
        }

        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->canonicalize($value);
            }
        }

        return $payload;
    }
}
