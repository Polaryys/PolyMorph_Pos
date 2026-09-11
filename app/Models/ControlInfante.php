<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlInfante extends Model
{
     protected $table = 'control_infantes';

    protected $fillable = [
        'ficha_infante_id',
        'numero_control',
        'anios_edad_control',
        'meses_edad_control',
        'dia_edad_control',
        'peso_actual_control',
        'peso_ideal_control',
        'indicador_pe_control',
        'talla_actual_control',
        'talla_ideal_control',
        'indicador_te_control',
        'indicador_pt_control',
        'evolucion_control',
        'producto_control',
    ];

    protected $casts = [
        'peso_actual_control' => 'decimal:2',
        'peso_ideal_control' => 'decimal:2',
        'talla_actual_control' => 'decimal:2',
        'talla_ideal_control' => 'decimal:2',
        'anios_edad_control' => 'integer',
        'meses_edad_control' => 'integer',
        'dia_edad_control' => 'integer',
        'numero_control' => 'integer',
    ];

    /**
     * Relación con ficha del infante
     */


    public function infante(){
        return $this->belongsTo(FichaInfante::class, 'ficha_infante_id', 'id');
    }


    /**
     * Calcula la edad exacta del infante
     */
    public static function calcularEdad($fechaNacimiento, $fechaControl = null): array
    {
        $nacimiento = Carbon::parse($fechaNacimiento);

        $control = $fechaControl
            ? Carbon::parse($fechaControl)
            : Carbon::now();

        $diferencia = $nacimiento->diff($control);

        return [
            'anios' => $diferencia->y,
            'meses' => $diferencia->m,
            'dias' => $diferencia->d,
        ];
    }
}
