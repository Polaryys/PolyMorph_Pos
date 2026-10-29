<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialPrecio extends Model
{
    protected $table = 'historial_precios';

    protected $fillable = [
        'presentacion_id',
        'precio_unitario_anterior',
        'precio_docena_anterior',
        'precio_ciento_anterior',
        'precio_unitario_nuevo',
        'precio_docena_nuevo',
        'precio_ciento_nuevo',
    ];

    protected function casts(): array
    {
        return [
            'precio_anterior' => 'decimal:2',
            'precio_nuevo' => 'decimal:2',
        ];
    }

    public function presentacion(): BelongsTo
    {
        return $this->belongsTo(
            Presentacion::class,
            'presentacion_id'
        );
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
