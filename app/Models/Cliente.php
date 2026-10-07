<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nit',
        'nombres',
        'apellidos',
        'telefono',
        'clasificacion',
        'notas',
        'correo'
    ];
}
