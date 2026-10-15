<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'numero_venta',
        'vendedor_id',
        'cliente_id',
        'tipo_transaccion',
        'estado_venta',
        'estado_pago',
        'venta_referencia_id',
        'total_descuentos',
        'costo_total',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'hora' => 'datetime:H:i:s',
            'total_descuentos' => 'decimal:2',
            'costo_total' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    // Usuario que realizó la venta
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    // Cliente asociado a la venta
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    // Venta original relacionada con esta operación
    public function ventaReferencia(): BelongsTo
    {
        return $this->belongsTo(
            Venta::class,
            'venta_referencia_id'
        );
    }

    // Operaciones derivadas de esta venta
    public function operacionesRelacionadas(): HasMany
    {
        return $this->hasMany(
            Venta::class,
            'venta_referencia_id'
        );
    }
}
