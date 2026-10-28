<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    protected $table = 'compras';

    protected $fillable = [
        'numero_compra',
        'usuario_id',
        'proveedor_id',
        'numero_factura',
        'costo_total',
        'cantidad_total',
        'observaciones',
        'estado',
        'fecha_compra',
    ];

    protected function casts(): array
    {
        return [
            'costo_total' => 'decimal:2',
            'cantidad_total' => 'integer',
            'estado' => 'string',
            'fecha_compra' => 'date',
        ];
    }

    // Usuario que registró la compra.
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Proveedor asociado a la compra.
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    // Detalles que conforman la compra.
    public function detalles(): HasMany
    {
        return $this->hasMany(CompraDetalle::class, 'compra_id');
    }
}
