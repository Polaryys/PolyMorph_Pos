<?php

namespace App\Models;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'nit',
        'nombres',
        'apellidos',
        'telefono',
        'clasificacion',
        'notas',
        'correo'
    ];

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }
}


