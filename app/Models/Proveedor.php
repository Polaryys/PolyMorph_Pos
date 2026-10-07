<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    protected $fillable = [
        'nit',
        'nombre',
        'telefono',
        'nombre_contacto',
        'telefono_contacto',
        'correo',
        'direccion',
        'notas'
    ];
}
