<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Services\PaymentGatewayService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected PaymentGatewayService $paymentService;
    protected InvoiceService $invoiceService;

    public function __construct(PaymentGatewayService $paymentService, InvoiceService $invoiceService)
    {
        $this->paymentService = $paymentService;
        $this->invoiceService = $invoiceService;
    }

    public function index()
    {
        $cart = $this->getCart();
        $cart->load('items.product.mainImage');

        if ($cart->is_empty) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $user = auth()->user();
        $taxRate = config('sneakerhub.tax_rate', 18);
        $deliveryFee = config('sneakerhub.delivery_fee', 15);

        $subtotal = $cart->subtotal;
        $discount = $cart->discount_amount;
        $tax = ($subtotal - $discount) * ($taxRate / 100);
        $total = $subtotal - $discount + $tax + $deliveryFee;

        return view('client.checkout.index', compact(
            'cart',
            'user',
            'subtotal',
            'discount',
            'tax',
            'taxRate',
            'deliveryFee',
            'total'
        ));
    }

    public function process(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'reference' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,yape',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = $this->getCart();
        $cart->load('items.product');

        if ($cart->is_empty) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        // Validate stock
        foreach ($cart->items as $item) {
            if (!$item->product->is_active || $item->quantity > $item->product->stock) {
                return back()->with('error', "El producto \"{$item->product->name}\" no tiene suficiente stock.");
            }
        }

        $shippingAddress = [
            'name' => $request->shipping_name,
            'phone' => $request->shipping_phone,
            'address' => $request->shipping_address,
            'city' => $request->shipping_city,
            'reference' => $request->reference,
        ];

        // Create order
        $order = Order::createFromCart($cart, $shippingAddress, $request->payment_method, auth()->id());

        if ($request->notes) {
            $order->notes = $request->notes;
            $order->save();
        }

        // Process payment
        $payment = $this->paymentService->processPayment($order);

        // Clear cart
        $cart->clear();

        // Redirect based on payment method
        if ($request->payment_method === Order::PAYMENT_YAPE) {
            return redirect()->route('checkout.yape', $order);
        }

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items', 'payment']);

        return view('client.checkout.success', compact('order'));
    }

    public function yapePayment(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('payment');

        if (!$order->payment || $order->payment->payment_method !== 'yape') {
            return redirect()->route('checkout.success', $order);
        }

        $yapeData = $this->paymentService->getYapeQRData($order->payment);

        return view('client.checkout.yape', compact('order', 'yapeData'));
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
