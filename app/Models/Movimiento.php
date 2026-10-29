<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movimiento extends Model
{
    protected $table = 'movimientos';

    protected $fillable = [
        'usuario_id',
        'inventario_id',
        'tipo_movimiento',
        'cantidad_anterior',
        'cantidad',
        'cantidad_posterior',
        'motivo',
    ];

    protected $casts = [
        'cantidad_anterior' => 'integer',
        'cantidad' => 'integer',
        'cantidad_posterior' => 'integer',
    ];

    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
