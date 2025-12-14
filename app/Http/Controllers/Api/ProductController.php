<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'mainImage'])
            ->active()
            ->inStock();

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Brand filter
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Price range
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Size filter
        if ($request->filled('size')) {
            $query->hasSize($request->size);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderByDesc('sales_count');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        $products = $query->paginate($request->get('per_page', 12));

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->load(['brand', 'category', 'images']);

        return response()->json($product);
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $products = Product::with(['brand', 'mainImage'])
            ->active()
            ->inStock()
            ->search($query)
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    public function categories()
    {
        $categories = Category::active()
            ->ordered()
            ->withCount([
                'products' => function ($q) {
                    $q->active()->inStock();
                }
            ])
            ->get();

        return response()->json($categories);
    }

    public function brands()
    {
        $brands = Brand::active()
            ->ordered()
            ->withCount([
                'products' => function ($q) {
                    $q->active()->inStock();
                }
            ])
            ->get();

        return response()->json($brands);
    }

    public function filters()
    {
        $priceRange = Product::active()->inStock()->selectRaw('MIN(price) as min, MAX(price) as max')->first();

        $sizes = collect(range(35, 48));

        $colors = Product::active()->inStock()
            ->whereNotNull('color')
            ->distinct()
            ->pluck('color');

        return response()->json([
            'price_range' => [
                'min' => $priceRange->min ?? 0,
                'max' => $priceRange->max ?? 1000,
            ],
            'sizes' => $sizes,
            'colors' => $colors,
            'genders' => ['men', 'women', 'unisex', 'kids'],
        ]);
    }
}
