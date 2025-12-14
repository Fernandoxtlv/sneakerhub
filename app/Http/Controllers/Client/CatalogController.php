<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
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

        // Price range filter
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

        // Color filter
        if ($request->filled('color')) {
            $query->where('color', 'like', "%{$request->color}%");
        }

        // Gender filter
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Search
        if ($request->filled('q')) {
            $query->search($request->q);
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
            case 'rating':
                $query->orderByDesc('rating_avg');
                break;
            default: // newest
                $query->orderByDesc('created_at');
        }

        $products = $query->paginate(config('sneakerhub.pagination.products', 12));

        // Get filters data
        $categories = Category::active()->ordered()->get();
        $brands = Brand::active()->ordered()->get();
        $sizes = range(35, 48);

        // Featured products for sidebar/carousel
        $featuredProducts = Product::with(['mainImage'])
            ->active()
            ->inStock()
            ->featured()
            ->limit(6)
            ->get();

        return view('client.catalog', compact(
            'products',
            'categories',
            'brands',
            'sizes',
            'featuredProducts'
        ));
    }

    public function category(Category $category)
    {
        $products = Product::with(['brand', 'category', 'mainImage'])
            ->active()
            ->inStock()
            ->where('category_id', $category->id)
            ->orderByDesc('created_at')
            ->paginate(config('sneakerhub.pagination.products', 12));

        $categories = Category::active()->ordered()->get();
        $brands = Brand::active()->ordered()->get();
        $sizes = range(35, 48);

        return view('client.catalog', compact(
            'products',
            'categories',
            'brands',
            'sizes',
            'category'
        ));
    }

    public function brand(Brand $brand)
    {
        $products = Product::with(['brand', 'category', 'mainImage'])
            ->active()
            ->inStock()
            ->where('brand_id', $brand->id)
            ->orderByDesc('created_at')
            ->paginate(config('sneakerhub.pagination.products', 12));

        $categories = Category::active()->ordered()->get();
        $brands = Brand::active()->ordered()->get();
        $sizes = range(35, 48);

        return view('client.catalog', compact(
            'products',
            'categories',
            'brands',
            'sizes',
            'brand'
        ));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->load(['brand', 'category', 'images']);
        $product->incrementViews();

        // Related products (same category)
        $relatedProducts = Product::with(['mainImage'])
            ->active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('client.product-show', compact('product', 'relatedProducts'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $products = Product::with(['brand', 'category', 'mainImage'])
            ->active()
            ->inStock()
            ->search($query)
            ->paginate(config('sneakerhub.pagination.products', 12));

        $categories = Category::active()->ordered()->get();
        $brands = Brand::active()->ordered()->get();
        $sizes = range(35, 48);

        return view('client.catalog', compact(
            'products',
            'categories',
            'brands',
            'sizes',
            'query'
        ));
    }
}
