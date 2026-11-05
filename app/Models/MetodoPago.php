<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetodoPago extends Model
{
    protected $table = 'metodos_pago';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function pagosVenta(): HasMany
    {
        return $this->hasMany(VentaDetallePago::class, 'metodo_pago_id');
    }

    public function reembolsos(): HasMany
    {
        return $this->hasMany(VentaReembolso::class, 'metodo_pago_id');
    }
}