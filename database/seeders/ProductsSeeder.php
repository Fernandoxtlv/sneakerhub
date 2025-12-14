<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Nike Products
            [
                'name' => 'Nike Air Max 270',
                'brand' => 'Nike',
                'category' => 'Casual',
                'price' => 549.00,
                'cost_price' => 280.00,
                'description' => 'Las Nike Air Max 270 ofrecen la unidad Air más grande jamás creada para Nike Sportswear, proporcionando una amortiguación increíble para todo el día.',
                'short_description' => 'Comodidad Air Max durante todo el día.',
                'stock' => 25,
                'sizes' => [38, 39, 40, 41, 42, 43, 44],
                'color' => 'Negro/Blanco',
                'gender' => 'unisex',
                'featured' => true,
            ],
            [
                'name' => 'Nike Air Force 1 07',
                'brand' => 'Nike',
                'category' => 'Lifestyle',
                'price' => 449.00,
                'cost_price' => 220.00,
                'description' => 'Las Nike Air Force 1 07 son un clásico reinventado. Presentan detalles de costura y acabados de primera calidad.',
                'short_description' => 'El clásico que nunca pasa de moda.',
                'stock' => 40,
                'sizes' => [36, 37, 38, 39, 40, 41, 42, 43, 44, 45],
                'color' => 'Blanco',
                'gender' => 'unisex',
                'featured' => true,
            ],
            [
                'name' => 'Nike ZoomX Vaporfly NEXT% 2',
                'brand' => 'Nike',
                'category' => 'Running',
                'price' => 899.00,
                'cost_price' => 450.00,
                'description' => 'Diseñadas para competir, las Nike ZoomX Vaporfly NEXT% 2 te ayudan a perseguir tus tiempos más rápidos.',
                'short_description' => 'Velocidad de élite para competición.',
                'stock' => 15,
                'sizes' => [40, 41, 42, 43, 44],
                'color' => 'Verde Volt',
                'gender' => 'men',
                'featured' => true,
            ],

            // Adidas Products
            [
                'name' => 'Adidas Ultraboost 22',
                'brand' => 'Adidas',
                'category' => 'Running',
                'price' => 699.00,
                'cost_price' => 350.00,
                'discount' => 15,
                'description' => 'Las Adidas Ultraboost 22 combinan la mejor tecnología de amortiguación BOOST con un upper Primeknit adaptable.',
                'short_description' => 'Máxima amortiguación BOOST.',
                'stock' => 30,
                'sizes' => [39, 40, 41, 42, 43, 44, 45],
                'color' => 'Core Black',
                'gender' => 'unisex',
                'featured' => true,
            ],
            [
                'name' => 'Adidas Stan Smith',
                'brand' => 'Adidas',
                'category' => 'Casual',
                'price' => 399.00,
                'cost_price' => 180.00,
                'description' => 'Las Adidas Stan Smith son el epítome del estilo minimalista. Upper de cuero premium con la icónica lengüeta verde.',
                'short_description' => 'El icono del tenis reinventado.',
                'stock' => 50,
                'sizes' => [36, 37, 38, 39, 40, 41, 42, 43, 44],
                'color' => 'Blanco/Verde',
                'gender' => 'unisex',
                'featured' => false,
            ],
            [
                'name' => 'Adidas Superstar',
                'brand' => 'Adidas',
                'category' => 'Lifestyle',
                'price' => 429.00,
                'cost_price' => 200.00,
                'description' => 'Las Adidas Superstar con su icónica puntera de goma han definido el estilo streetwear desde los años 70.',
                'short_description' => 'El clásico de la cultura sneaker.',
                'stock' => 35,
                'sizes' => [36, 37, 38, 39, 40, 41, 42, 43, 44, 45],
                'color' => 'Blanco/Negro',
                'gender' => 'unisex',
                'featured' => false,
            ],

            // Puma Products
            [
                'name' => 'Puma RS-X',
                'brand' => 'Puma',
                'category' => 'Lifestyle',
                'price' => 459.00,
                'cost_price' => 220.00,
                'description' => 'Las Puma RS-X reinventan el look de los 80s con colores vibrantes y una silueta chunky que está de moda.',
                'short_description' => 'Retro futurista con actitud.',
                'stock' => 20,
                'sizes' => [39, 40, 41, 42, 43, 44],
                'color' => 'Multi',
                'gender' => 'unisex',
                'featured' => true,
            ],
            [
                'name' => 'Puma Suede Classic XXI',
                'brand' => 'Puma',
                'category' => 'Casual',
                'price' => 349.00,
                'cost_price' => 160.00,
                'description' => 'Las Puma Suede Classic son un ícono del streetwear desde 1968. Gamuza premium y suela de goma vulcanizada.',
                'short_description' => 'El clásico de gamuza.',
                'stock' => 28,
                'sizes' => [38, 39, 40, 41, 42, 43, 44],
                'color' => 'Negro',
                'gender' => 'unisex',
                'featured' => false,
            ],

            // New Balance Products
            [
                'name' => 'New Balance 574',
                'brand' => 'New Balance',
                'category' => 'Casual',
                'price' => 399.00,
                'cost_price' => 190.00,
                'description' => 'Las New Balance 574 son un ícono de la marca. Combinan estilo retro con comodidad moderna.',
                'short_description' => 'El clásico New Balance.',
                'stock' => 32,
                'sizes' => [38, 39, 40, 41, 42, 43, 44, 45],
                'color' => 'Gris',
                'gender' => 'unisex',
                'featured' => false,
            ],
            [
                'name' => 'New Balance Fresh Foam 1080v12',
                'brand' => 'New Balance',
                'category' => 'Running',
                'price' => 649.00,
                'cost_price' => 320.00,
                'description' => 'Las New Balance Fresh Foam 1080v12 ofrecen la mejor amortiguación Fresh Foam para tus carreras más largas.',
                'short_description' => 'Máximo confort Fresh Foam.',
                'stock' => 18,
                'sizes' => [40, 41, 42, 43, 44, 45],
                'color' => 'Azul',
                'gender' => 'men',
                'featured' => true,
            ],

            // Converse Products
            [
                'name' => 'Converse Chuck Taylor All Star',
                'brand' => 'Converse',
                'category' => 'Casual',
                'price' => 249.00,
                'cost_price' => 100.00,
                'description' => 'Las Converse Chuck Taylor All Star son el sneaker más icónico de la historia. Canvas duradero y suela de goma.',
                'short_description' => 'El original desde 1917.',
                'stock' => 60,
                'sizes' => [35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45],
                'color' => 'Negro',
                'gender' => 'unisex',
                'featured' => true,
            ],
            [
                'name' => 'Converse Chuck 70',
                'brand' => 'Converse',
                'category' => 'Lifestyle',
                'price' => 349.00,
                'cost_price' => 150.00,
                'description' => 'Las Converse Chuck 70 son una versión premium del clásico, con mejor amortiguación y materiales superiores.',
                'short_description' => 'El clásico elevado.',
                'stock' => 25,
                'sizes' => [36, 37, 38, 39, 40, 41, 42, 43, 44],
                'color' => 'Parchment',
                'gender' => 'unisex',
                'featured' => false,
            ],

            // Vans Products
            [
                'name' => 'Vans Old Skool',
                'brand' => 'Vans',
                'category' => 'Skateboarding',
                'price' => 299.00,
                'cost_price' => 130.00,
                'description' => 'Las Vans Old Skool son el primer modelo de Vans con la icónica franja lateral. Un clásico del skate.',
                'short_description' => 'El ícono del skate.',
                'stock' => 45,
                'sizes' => [36, 37, 38, 39, 40, 41, 42, 43, 44, 45],
                'color' => 'Negro/Blanco',
                'gender' => 'unisex',
                'featured' => true,
            ],
            [
                'name' => 'Vans Sk8-Hi',
                'brand' => 'Vans',
                'category' => 'Skateboarding',
                'price' => 349.00,
                'cost_price' => 150.00,
                'description' => 'Las Vans Sk8-Hi son el primer sneaker de bota alta para skate. Protección de tobillo y estilo inconfundible.',
                'short_description' => 'High top legendario.',
                'stock' => 22,
                'sizes' => [38, 39, 40, 41, 42, 43, 44],
                'color' => 'Negro',
                'gender' => 'unisex',
                'featured' => false,
            ],

            // Reebok Products
            [
                'name' => 'Reebok Classic Leather',
                'brand' => 'Reebok',
                'category' => 'Casual',
                'price' => 349.00,
                'cost_price' => 160.00,
                'description' => 'Las Reebok Classic Leather son un ícono del estilo retro desde 1983. Cuero suave y amortiguación EVA.',
                'short_description' => 'Estilo retro auténtico.',
                'stock' => 28,
                'sizes' => [38, 39, 40, 41, 42, 43, 44],
                'color' => 'Blanco',
                'gender' => 'unisex',
                'featured' => false,
            ],
            [
                'name' => 'Reebok Nano X2',
                'brand' => 'Reebok',
                'category' => 'Training',
                'price' => 549.00,
                'cost_price' => 270.00,
                'description' => 'Las Reebok Nano X2 son las zapatillas de training más versátiles. Perfectas para CrossFit y entrenamiento funcional.',
                'short_description' => 'Diseñadas para el CrossFit.',
                'stock' => 15,
                'sizes' => [40, 41, 42, 43, 44, 45],
                'color' => 'Core Black',
                'gender' => 'men',
                'featured' => true,
            ],

            // Additional products with discount
            [
                'name' => 'Nike Dunk Low Retro',
                'brand' => 'Nike',
                'category' => 'Lifestyle',
                'price' => 499.00,
                'cost_price' => 240.00,
                'discount' => 20,
                'description' => 'Las Nike Dunk Low Retro traen de vuelta el estilo icónico del basketball de los 80s.',
                'short_description' => 'El retorno del ícono.',
                'stock' => 12,
                'sizes' => [39, 40, 41, 42, 43],
                'color' => 'Panda',
                'gender' => 'unisex',
                'featured' => true,
            ],
            [
                'name' => 'Adidas Forum Low',
                'brand' => 'Adidas',
                'category' => 'Basketball',
                'price' => 449.00,
                'cost_price' => 210.00,
                'discount' => 10,
                'description' => 'Las Adidas Forum Low reviven la era dorada del basketball con su diseño retro y la emblemática correa del tobillo.',
                'short_description' => 'Heritage del basketball.',
                'stock' => 20,
                'sizes' => [39, 40, 41, 42, 43, 44],
                'color' => 'Blanco/Azul',
                'gender' => 'unisex',
                'featured' => false,
            ],
            [
                'name' => 'Nike Air Jordan 1 Low',
                'brand' => 'Nike',
                'category' => 'Basketball',
                'price' => 549.00,
                'cost_price' => 280.00,
                'description' => 'Las Air Jordan 1 Low traen el legado de Michael Jordan a un perfil bajo más versátil para el día a día.',
                'short_description' => 'El legado de Jordan.',
                'stock' => 8,
                'sizes' => [40, 41, 42, 43, 44],
                'color' => 'Bred',
                'gender' => 'men',
                'featured' => true,
            ],
        ];

        foreach ($products as $productData) {
            $brand = Brand::where('name', $productData['brand'])->first();
            $category = Category::where('name', $productData['category'])->first();

            if (!$brand || !$category) {
                continue;
            }

            $product = Product::create([
                'name' => $productData['name'],
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'price' => $productData['price'],
                'cost_price' => $productData['cost_price'],
                'discount' => $productData['discount'] ?? 0,
                'description' => $productData['description'],
                'short_description' => $productData['short_description'],
                'stock' => $productData['stock'],
                'sizes_available' => $productData['sizes'],
                'color' => $productData['color'],
                'gender' => $productData['gender'],
                'featured' => $productData['featured'],
                'is_active' => true,
                'is_new' => true,
            ]);

            // Image Mapping for variety (using available generated images)
            $imageMap = [
                // Black/Sporty (Dark Theme)
                'Nike Air Max 270' => 'products/nike-air-max-270.png',
                'Adidas Ultraboost 22' => 'products/nike-air-max-270.png',
                'Puma Suede Classic XXI' => 'products/nike-air-max-270.png', // Correcting: Suede is Black
                'Converse Chuck Taylor All Star' => 'products/nike-air-max-270.png', // Correcting: Chuck is Black
                'Vans Old Skool' => 'products/nike-air-max-270.png',
                'Reebok Nano X2' => 'products/nike-air-max-270.png',

                // Colorful/Blue/HighContrast (Basketball Theme)
                'Nike Air Jordan 1 Low' => 'products/basketball-placeholder.png',
                'Puma RS-X' => 'products/basketball-placeholder.png',
                'Vans Sk8-Hi' => 'products/basketball-placeholder.png',
                'Adidas Forum Low' => 'products/basketball-placeholder.png', // Adding color
                'New Balance Fresh Foam 1080v12' => 'products/basketball-placeholder.png', // Blue

                // Grey/Sporty (Running Theme)
                'Nike ZoomX Vaporfly NEXT% 2' => 'products/running-placeholder.png',
                'New Balance 574' => 'products/running-placeholder.png',

                // White (Limited to actual white shoes)
                'Nike Air Force 1 07' => 'products/nike-air-force-1.png',
                'Adidas Superstar' => 'products/nike-air-force-1.png',
                'Adidas Stan Smith' => 'products/casual-placeholder.png',
                'Reebok Classic Leather' => 'products/casual-placeholder.png',
                'Nike Dunk Low Retro' => 'products/casual-placeholder.png',
                'Converse Chuck 70' => 'products/casual-placeholder.png',
            ];

            // Default fallback based on category
            $imagePath = 'products/casual-placeholder.png';
            if ($category->name === 'Running' || $category->name === 'Training') {
                $imagePath = 'products/running-placeholder.png';
            } elseif ($category->name === 'Basketball' || $category->name === 'Skateboarding') {
                $imagePath = 'products/basketball-placeholder.png';
            }

            // Override if specific mapping exists
            if (isset($imageMap[$productData['name']])) {
                $imagePath = $imageMap[$productData['name']];
            }

            ProductImage::create([
                'product_id' => $product->id,
                'filename' => basename($imagePath),
                'path' => $imagePath,
                'path_medium' => $imagePath,
                'path_thumb' => $imagePath,
                'is_main' => true,
                'position' => 1
            ]);
        }
    }
}
