<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    protected $table = 'imagenes';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nombre',
        'nombre_archivo'
    ];
}
