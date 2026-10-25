<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompraDetalle extends Model
{
    protected $table = 'compra_detalles';

    protected $fillable = [
        'compra_id',
        'presentacion_id',
        'cantidad',
        'costo_unitario',
        'costo_promedio_anterior',
        'costo_promedio_nuevo',
        'precio_unitario_anterior',
        'precio_unitario_nuevo',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'costo_unitario' => 'decimal:2',
            'costo_promedio_anterior' => 'decimal:2',
            'costo_promedio_nuevo' => 'decimal:2',
            'precio_unitario_anterior' => 'decimal:2',
            'precio_unitario_nuevo' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    // Compra a la que pertenece el detalle.
    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    // Presentación base comprada.
    public function presentacion(): BelongsTo
    {
        return $this->belongsTo(
            Presentacion::class,
            'presentacion_id'
        );
    }
}
