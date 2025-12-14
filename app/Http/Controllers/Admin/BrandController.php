<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $brands = Brand::withCount('products')
            ->ordered()
            ->paginate(20);

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'position' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->imageService->uploadCategoryImage(
                $request->file('logo'),
                'brand'
            );
        }

        Brand::create($validated);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Marca creada exitosamente.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('brands')->ignore($brand->id)],
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'position' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                $this->imageService->deleteImage($brand->logo);
            }
            $validated['logo'] = $this->imageService->uploadCategoryImage(
                $request->file('logo'),
                'brand'
            );
        }

        $brand->update($validated);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Marca actualizada exitosamente.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una marca con productos.');
        }

        if ($brand->logo) {
            $this->imageService->deleteImage($brand->logo);
        }

        $brand->delete();

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Marca eliminada exitosamente.');
    }
}
