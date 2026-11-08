<?php

namespace Database\Factories;

use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proveedor>
 */
class ProveedorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nit' => fake()->numerify('########-#'),

            'nombre' => fake()->unique()->randomElement([
                'Distribuidora Escolar del Norte',
                'Comercializadora El Estudiante',
                'Distribuidora Papelera Central',
                'Suministros y Papelería Maya',
                'Comercial La Económica',
                'Distribuidora San José',
                'Papeles de Guatemala',
                'Suministros Escolares GT',
                'Distribuidora El Maestro',
                'Comercializadora Moderna',
                'Importadora Los Pinos',
                'Distribuidora Universal',
                'Papelería Continental',
                'Suministros del Pacífico',
                'Comercializadora La Unión',
                'Distribuidora Metropolitana',
                'Importadora El Progreso',
                'Suministros Educativos del Sur',
                'Distribuidora La Estrella',
                'Comercializadora Nacional',
                'Papelería y Suministros Rivera',
                'Distribuidora El Roble',
                'Importadora San Miguel',
                'Comercial La Esperanza',
                'Suministros Escolares del Valle',
                'Distribuidora Las Américas',
                'Comercializadora El Faro',
                'Papeles y Cartones del Sur',
                'Distribuidora El Buen Precio',
                'Suministros Integrales Maya',
            ]),

            'telefono' => fake()->numerify('####-####'),

            'nombre_contacto' => fake()->optional(0.8)->name(),

            'telefono_contacto' => fake()->numerify('####-####'),

            'correo' => fake()->boolean(80)
                ? fake()->unique()->safeEmail()
                : null,

            'direccion' => fake()->optional(0.85)->address(),

            'notas' => fake()->optional(0.3)->sentence(8),
        ];
    }
}
