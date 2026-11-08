<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetodoPago extends Model
{
    public const TIPO_CAPTURA_MONTOS = 'PERMITIR MONTOS';

    public const TIPO_CAPTURA_REFERENCIA = 'PERMITIR REFERENCIA';

    protected $table = 'metodos_pago';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo_captura',
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

}
