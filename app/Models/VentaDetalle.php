<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VentaDetalle extends Model
{
    protected $table = 'venta_detalles';

    protected $fillable = [
        'venta_id',
        'presentacion_id',
        'tipo_movimiento',
        'detalle_referencia_id',
        'reingresa_inventario',
        'tipo_item',
        'cantidad',
        'factor',
        'cantidad_base',
        'costo_unitario',
        'precio_unitario',
        'precio_aplicado',
        'descuento',
        'subtotal_costo',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:2',
            'factor' => 'integer',
            'cantidad_base' => 'decimal:2',
            'reingresa_inventario' => 'boolean',
            'costo_unitario' => 'decimal:2',
            'precio_unitario' => 'decimal:2',
            'precio_aplicado' => 'decimal:2',
            'descuento' => 'decimal:2',
            'subtotal_costo' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    // Venta a la que pertenece el detalle
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    // Presentación vendida
    public function presentacion(): BelongsTo
    {
        return $this->belongsTo(Presentacion::class, 'presentacion_id');
    }

    public function detalleReferencia(): BelongsTo
    {
        return $this->belongsTo(self::class, 'detalle_referencia_id');
    }

    public function operacionesRelacionadas(): HasMany
    {
        return $this->hasMany(self::class, 'detalle_referencia_id');
    }
}
