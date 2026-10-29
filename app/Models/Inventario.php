<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventario extends Model
{
    protected $table = 'inventario';
    protected $fillable = [
        'tipo',
        'costoPromedioUnitario',
        'cantidad',
        'umbral',
        'marca_id',
        'categoria_id',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'costoPromedioUnitario' => 'decimal:2',
            'cantidad' => 'integer',
            'umbral' => 'integer',
            'estado' => 'boolean',
        ];
    }

    // Un registro de inventario pertenece a una marca.
    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    // Un registro de inventario pertenece a una categoría.
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
    // Un registro de inventario puede tener muchas presentaciones.
    public function presentaciones(): HasMany
    {
        return $this->hasMany(Presentacion::class, 'inventario_id');
    }
    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }
}



