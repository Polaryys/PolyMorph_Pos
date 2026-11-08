<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nit' => fake()->boolean(35)
                ? 'CF'
                : fake()->numerify('########-#'),

            'nombres' => fake()->firstName(),

            'apellidos' => fake()->boolean(90)
                ? fake()->lastName()
                : null,

            'telefono' => fake()->boolean(85)
                ? fake()->numerify('########')
                : null,

            'clasificacion' => fake()->randomElement([
                'Maroyista',
                'Minorista',
            ]),

            'notas' => fake()->boolean(20)
                ? fake()->sentence(8)
                : null,

            'correo' => fake()->boolean(70)
                ? fake()->unique()->safeEmail()
                : null,
        ];
    }
}
