<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Marca;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Inventario;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Usuarios iniciales
        $this->call([
            IndominorUserSeeder::class,
        ]);

        // Datos para las relaciones del inventario
        Marca::factory()->count(10)->create();
        Categoria::factory()->count(10)->create();

        // Datos generales
        Cliente::factory()->count(40)->create();
        Proveedor::factory()->count(15)->create();

        // Artículos y presentaciones
        $this->call([
            ArticuloSeeder::class,
        ]);
    }
}
