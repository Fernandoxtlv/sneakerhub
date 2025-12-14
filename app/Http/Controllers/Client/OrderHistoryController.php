<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class OrderHistoryController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items', 'payment'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('client.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'payment', 'invoice']);

        return view('client.orders.show', compact('order'));
    }

    public function downloadBoleta(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->payment_status !== Order::PAYMENT_PAID) {
            return back()->with('error', 'La boleta estará disponible después de confirmar el pago.');
        }

        $invoice = $this->invoiceService->getOrGenerate($order);

        // Force regeneration to apply new design
        $this->invoiceService->regeneratePdf($invoice);

        return $this->invoiceService->download($invoice);
    }
}
