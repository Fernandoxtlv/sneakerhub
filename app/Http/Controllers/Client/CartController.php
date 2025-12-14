<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        $cart->load('items.product.mainImage');

        return view('client.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
            'size' => 'nullable|string|max:10',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_active || !$product->is_in_stock) {
            return back()->with('error', 'Este producto no está disponible.');
        }

        if ($request->quantity > $product->stock) {
            return back()->with('error', 'No hay suficiente stock disponible.');
        }

        $cart = $this->getCart();
        $cart->addItem($product, $request->quantity, $request->size);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'cart_count' => $cart->items_count,
            ]);
        }

        return back()->with('success', 'Producto agregado al carrito.');
    }

    public function update(Request $request, CartItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:10',
        ]);

        $cart = $this->getCart();

        if ($item->cart_id !== $cart->id) {
            abort(403);
        }

        if ($request->quantity > $item->product->stock) {
            return back()->with('error', 'No hay suficiente stock disponible.');
        }

        $cart->updateItemQuantity($item, $request->quantity);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Carrito actualizado',
                'cart_count' => $cart->fresh()->items_count,
                'subtotal' => $cart->fresh()->formatted_subtotal,
            ]);
        }

        return back()->with('success', 'Carrito actualizado.');
    }

    public function remove(CartItem $item)
    {
        $cart = $this->getCart();

        if ($item->cart_id !== $cart->id) {
            abort(403);
        }

        $cart->removeItem($item);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito',
                'cart_count' => $cart->fresh()->items_count,
            ]);
        }

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $cart = $this->getCart();
        $coupon = Coupon::findByCode($request->coupon_code);

        if (!$coupon || !$coupon->canApplyTo($cart->subtotal)) {
            return back()->with('error', 'El cupón no es válido o no se puede aplicar a este pedido.');
        }

        $discount = $coupon->calculateDiscount($cart->subtotal);

        $cart->coupon_code = $coupon->code;
        $cart->discount_amount = $discount;
        $cart->save();

        return back()->with('success', "Cupón aplicado. Descuento: S/ " . number_format($discount, 2));
    }

    public function removeCoupon()
    {
        $cart = $this->getCart();
        $cart->coupon_code = null;
        $cart->discount_amount = 0;
        $cart->save();

        return back()->with('success', 'Cupón eliminado.');
    }

    public function count()
    {
        $cart = $this->getCart();

        return response()->json([
            'count' => $cart->items_count,
        ]);
    }

    protected function getCart(): Cart
    {
        if (auth()->check()) {
            return Cart::getOrCreate(auth()->id());
        }

        $sessionId = session()->getId();
        return Cart::getOrCreate(null, $sessionId);
    }
}
