<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'mainImage']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by brand
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'low_stock') {
                $query->lowStock();
            } elseif ($request->status === 'out_of_stock') {
                $query->outOfStock();
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $products = $query->paginate(config('sneakerhub.pagination.products', 15));
        $categories = Category::active()->ordered()->get();
        $brands = Brand::active()->ordered()->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = Category::active()->ordered()->get();
        $brands = Brand::active()->ordered()->get();
        $sizes = range(35, 48);

        return view('admin.products.create', compact('categories', 'brands', 'sizes'));
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());

        // Handle image uploads
        if ($request->hasFile('images')) {
            $this->handleImageUploads($product, $request->file('images'));
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product)
    {
        $product->load(['brand', 'category', 'images', 'stockMovements.user']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('images');
        $categories = Category::active()->ordered()->get();
        $brands = Brand::active()->ordered()->get();
        $sizes = range(35, 48);

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'sizes'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        // Handle image uploads
        if ($request->hasFile('images')) {
            $this->handleImageUploads($product, $request->file('images'));
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        // Delete images
        $this->imageService->deleteProductImages($product->id);

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    public function uploadImages(Request $request, Product $product)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,webp|max:10240',
        ]);

        $this->handleImageUploads($product, $request->file('images'));

        return back()->with('success', 'Imágenes subidas exitosamente.');
    }

    public function deleteImage(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            abort(403);
        }

        // Delete files
        $this->imageService->deleteImage($image->path);
        if ($image->path_thumb) {
            $this->imageService->deleteImage($image->path_thumb);
        }
        if ($image->path_medium) {
            $this->imageService->deleteImage($image->path_medium);
        }

        $image->delete();

        return back()->with('success', 'Imagen eliminada exitosamente.');
    }

    public function setMainImage(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            abort(403);
        }

        $image->setAsMain();

        return back()->with('success', 'Imagen principal establecida.');
    }

    public function toggleActive(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        $status = $product->is_active ? 'activado' : 'desactivado';

        return back()->with('success', "Producto {$status} exitosamente.");
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:add,subtract,set',
            'reason' => 'nullable|string|max:255',
        ]);

        $quantity = (int) $request->quantity;
        $reason = $request->reason ?? 'Ajuste manual';

        if ($request->type === 'add') {
            $product->increaseStock($quantity, 'adjustment', null, null, auth()->id());
        } elseif ($request->type === 'subtract') {
            $product->decreaseStock($quantity, 'adjustment', null, null, auth()->id());
        } else {
            // Set to specific value
            $diff = $quantity - $product->stock;
            if ($diff > 0) {
                $product->increaseStock($diff, 'adjustment', null, null, auth()->id());
            } elseif ($diff < 0) {
                $product->decreaseStock(abs($diff), 'adjustment', null, null, auth()->id());
            }
        }

        return back()->with('success', 'Stock actualizado exitosamente.');
    }

    protected function handleImageUploads(Product $product, array $files): void
    {
        $hasMainImage = $product->images()->where('is_main', true)->exists();
        $position = $product->images()->max('position') ?? 0;

        foreach ($files as $index => $file) {
            if (!$this->imageService->validate($file)) {
                continue;
            }

            $imageData = $this->imageService->uploadProductImage($file, $product->id);
            $position++;

            $product->images()->create([
                'filename' => $imageData['filename'],
                'path' => $imageData['path'],
                'path_thumb' => $imageData['path_thumb'],
                'path_medium' => $imageData['path_medium'],
                'mime_type' => $imageData['mime_type'],
                'file_size' => $imageData['file_size'],
                'is_main' => !$hasMainImage && $index === 0,
                'position' => $position,
            ]);

            if (!$hasMainImage && $index === 0) {
                $hasMainImage = true;
            }
        }
    }
}
