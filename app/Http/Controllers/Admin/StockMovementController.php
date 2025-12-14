<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'user']);

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->orderByDesc('created_at')
            ->paginate(30);

        $products = Product::orderBy('name')->get(['id', 'name', 'sku']);
        $types = StockMovement::getTypeOptions();

        return view('admin.stock-movements.index', compact('movements', 'products', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|in:purchase,return,adjustment,damage',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Calculate new stock
        $stockBefore = $product->stock;
        $stockAfter = $validated['type'] === 'in'
            ? $stockBefore + $validated['quantity']
            : $stockBefore - $validated['quantity'];

        // Prevent negative stock
        if ($stockAfter < 0) {
            return back()->with('error', 'No hay suficiente stock para realizar esta operación.');
        }

        // Create movement
        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update product stock
        $product->update(['stock' => $stockAfter]);

        return back()->with('success', 'Movimiento de stock registrado correctamente.');
    }
}
