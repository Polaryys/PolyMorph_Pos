<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaReembolso extends Model
{
    protected $table = 'venta_reembolsos';

    protected $fillable = [
        'venta_id',
        'metodo_pago_id',
        'usuario_id',
        'monto',
        'estado',
        'referencia',
        'fecha_reembolso',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha_reembolso' => 'datetime',
        ];
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
