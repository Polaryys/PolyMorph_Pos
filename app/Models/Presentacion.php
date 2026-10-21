<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presentacion extends Model
{
    protected $table = 'presentaciones';

    protected $fillable = [
        'inventario_id',
        'codigoSKU',
        'codigoBarras',
        'nombre',
        'detalle',
        'factor',
        'ubicacion',
        'base',
        'precioUnitario',
        'precioUnitarioDocena',
        'precioUnitarioCiento',
        'imagen_id',
        'fecha_vencimiento',
        'estado'
    ];

    protected function casts(): array
    {
        return [
            'factor' => 'integer',
            'base' => 'boolean',
            'precioUnitario' => 'decimal:2',
            'precioUnitarioDocena' => 'decimal:2',
            'precioUnitarioCiento' => 'decimal:2',
            'fecha_vencimiento' => 'date',
            'estado' => 'boolean',
        ];
    }

    /**
     * Una presentación pertenece a un inventario.
     */
    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class);
    }

    /**
     * Una presentación puede tener una imagen.
     */
    public function imagen(): BelongsTo
    {
        return $this->belongsTo(Imagen::class);
    }
}
