<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Categoria>
 */
class CategoriaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->randomElement([
                'Cuadernos',
                'Libretas',
                'Lápices',
                'Lapiceros',
                'Marcadores',
                'Resaltadores',
                'Borradores',
                'Sacapuntas',
                'Reglas y Escuadras',
                'Pegamentos',
                'Tijeras',
                'Papel Bond',
                'Cartulinas',
                'Papel de Colores',
                'Folders y Carpetas',
                'Archivadores',
                'Sobres',
                'Notas Adhesivas',
                'Material de Dibujo',
                'Material de Pintura',
                'Pinceles',
                'Crayones y Lápices de Color',
                'Plastilina',
                'Material Didáctico',
                'Mochilas',
                'Estuches',
                'Accesorios de Oficina',
                'Calculadoras',
                'Tintas y Tóner',
                'Material para Manualidades',
            ]),
        ];
    }
}
