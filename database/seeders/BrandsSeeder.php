<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandsSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'description' => 'Just Do It. La marca deportiva más icónica del mundo.',
                'website' => 'https://www.nike.com',
                'position' => 1,
            ],
            [
                'name' => 'Adidas',
                'slug' => 'adidas',
                'description' => 'Impossible is Nothing. Innovación alemana en calzado deportivo.',
                'website' => 'https://www.adidas.com',
                'position' => 2,
            ],
            [
                'name' => 'Puma',
                'slug' => 'puma',
                'description' => 'Forever Faster. Estilo y rendimiento combinados.',
                'website' => 'https://www.puma.com',
                'position' => 3,
            ],
            [
                'name' => 'New Balance',
                'slug' => 'new-balance',
                'description' => 'Fearlessly Independent. Confort y calidad americana.',
                'website' => 'https://www.newbalance.com',
                'position' => 4,
            ],
            [
                'name' => 'Converse',
                'slug' => 'converse',
                'description' => 'Shoes Are Boring. Wear Sneakers. El clásico que nunca pasa de moda.',
                'website' => 'https://www.converse.com',
                'position' => 5,
            ],
            [
                'name' => 'Vans',
                'slug' => 'vans',
                'description' => 'Off The Wall. La marca preferida de los skaters.',
                'website' => 'https://www.vans.com',
                'position' => 6,
            ],
            [
                'name' => 'Reebok',
                'slug' => 'reebok',
                'description' => 'Be More Human. Fitness y estilo de vida activo.',
                'website' => 'https://www.reebok.com',
                'position' => 7,
            ],
            [
                'name' => 'Goodyear',
                'slug' => 'goodyear',
                'description' => 'Footwear for the driven. Calzado duradero y resistente.',
                'website' => 'https://www.goodyearfootwear.com',
                'position' => 8,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['slug' => $brand['slug']],
                $brand
            );
        }
    }
}
