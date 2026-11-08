<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudIdempotencia extends Model
{
    protected $table = 'solicitudes_idempotencia';

    protected $fillable = [
        'usuario_id',
        'accion',
        'clave',
        'hash_solicitud',
        'venta_id',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
