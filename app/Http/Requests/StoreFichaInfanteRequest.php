<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFichaInfanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_infante' => 'required|string|max:255',
            'sexo_infante' => 'required|in:Masculino,Femenino',
            'fecha_nacimiento_infante' => 'required|date|before_or_equal:today',
            'fecha_inscripcion_infante' => 'nullable|date',
            'peso_infante' => 'nullable|numeric|min:0|max:200',
            'talla_infante' => 'nullable|numeric|min:0|max:250',
            'prueba_apetito_infante' => 'nullable|integer|between:0,100',
            'examen_cognitivo_infante' => 'nullable|integer|between:0,100',
            'departamento_residencia_encargado' => 'required|string|max:100',
            'municipio_residencia_encargado' => 'required|string|max:100',
            'direccion_residencia_encargado' => 'required|string',
            'parentesco_encargado' => 'required|in:madre,padre,abuelo,tio,encargado',
            'nombre_encargado' => 'required|string|max:255',
            'dpi_encargado' => 'nullable|digits:13', // 13 dígitos exactos
            'telefono_encargado' => 'required|digits:8', // 8 dígitos exactos
        ];
    }
}
