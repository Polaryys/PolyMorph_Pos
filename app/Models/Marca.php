<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
    ];

    // Una marca puede tener muchos registros de inventario.
    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }
}
