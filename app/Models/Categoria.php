<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = [
        'nombre',
    ];
    // Una categoría puede tener muchos registros de inventario.
    public function inventarios()
    {
        return $this->hasMany(Inventario::class);   
}
}