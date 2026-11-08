<?php

namespace Database\Factories;

use App\Models\Marca;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Marca>
 */
class MarcaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->randomElement([
                'Norma',
                'Pelikan',
                'BIC',
                'Faber-Castell',
                'Stabilo',
                'Maped',
                'Paper Mate',
                'Sharpie',
                'Crayola',
                'Prismacolor',
                'Scribe',
                'Pilot',
                'Dixon',
                'Artesco',
                'Barrilito',
            ]),
        ];
    }
}
