<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Running',
                'slug' => 'running',
                'description' => 'Zapatillas diseñadas para correr con máxima comodidad y rendimiento.',
                'position' => 1,
            ],
            [
                'name' => 'Casual',
                'slug' => 'casual',
                'description' => 'Zapatillas versátiles para el día a día con estilo urbano.',
                'position' => 2,
            ],
            [
                'name' => 'Basketball',
                'slug' => 'basketball',
                'description' => 'Zapatillas de alto rendimiento para la cancha de baloncesto.',
                'position' => 3,
            ],
            [
                'name' => 'Skateboarding',
                'slug' => 'skateboarding',
                'description' => 'Zapatillas resistentes con agarre superior para skateboarding.',
                'position' => 4,
            ],
            [
                'name' => 'Training',
                'slug' => 'training',
                'description' => 'Zapatillas de entrenamiento versátiles para el gimnasio.',
                'position' => 5,
            ],
            [
                'name' => 'Lifestyle',
                'slug' => 'lifestyle',
                'description' => 'Zapatillas con diseños exclusivos que marcan tendencia.',
                'position' => 6,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
