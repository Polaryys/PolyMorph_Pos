<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaDetallePago extends Model
{
    protected $table = 'venta_detalle_pagos';

    protected $fillable = [
        'venta_id',
        'metodo_pago_id',
        'monto',
        'monto_recibido',
        'cambio',
        'referencia',
        'fecha_pago',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'monto_recibido' => 'decimal:2',
            'cambio' => 'decimal:2',
            'fecha_pago' => 'datetime',
        ];
    }

    // Venta a la que pertenece el pago
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    // Método utilizado para el pago
    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }
}
