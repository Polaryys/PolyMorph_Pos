<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FichaInfante extends Model
{
     use HasFactory;

    protected $table = 'ficha_infantes';

    protected $fillable = [
        'codigo_infante',
        'nombre_infante',
        'sexo_infante',
        'fecha_nacimiento_infante',
        'fecha_inscripcion_infante',
        'peso_infante',
        'talla_infante',
        'prueba_apetito_infante',
        'examen_cognitivo_infante',
        'departamento_residencia_encargado',
        'municipio_residencia_encargado',
        'direccion_residencia_encargado',
        'parentesco_encargado',
        'nombre_encargado',
        'dpi_encargado',
        'telefono_encargado',
    ];

    // Generar el código automáticamente antes de crear el registro
    protected static function booted()
    {
        static::creating(function ($infante) {
            // Ejemplo de código autogenerado: INF-2026-XXXXX
            $infante->codigo_infante = 'INF-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        });
    }

  

    public function controles(){
         return $this->hasMany(ControlInfante::class);
    }

}
