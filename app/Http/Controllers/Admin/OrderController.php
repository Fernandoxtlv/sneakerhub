<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InvoiceService;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected InvoiceService $invoiceService;
    protected PaymentGatewayService $paymentService;

    public function __construct(InvoiceService $invoiceService, PaymentGatewayService $paymentService)
    {
        $this->invoiceService = $invoiceService;
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'items', 'payment']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->orderByDesc('created_at')
            ->paginate(config('sneakerhub.pagination.orders', 15));

        $statuses = Order::getStatusOptions();
        $paymentStatuses = Order::getPaymentStatusOptions();
        $paymentMethods = Order::getPaymentMethodOptions();

        return view('admin.orders.index', compact('orders', 'statuses', 'paymentStatuses', 'paymentMethods'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'payment', 'invoice']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatusOptions())),
        ]);

        $order->updateStatus($request->status);

        // If order is marked as paid, process stock reduction
        if ($request->status === Order::STATUS_PAID && $order->payment_status !== Order::PAYMENT_PAID) {
            $this->processOrderPaid($order);
        }

        return back()->with('success', 'Estado del pedido actualizado.');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:' . implode(',', array_keys(Order::getPaymentStatusOptions())),
        ]);

        $previousStatus = $order->payment_status;
        $order->payment_status = $request->payment_status;

        if ($request->payment_status === Order::PAYMENT_PAID && $previousStatus !== Order::PAYMENT_PAID) {
            $order->paid_at = now();
            $this->processOrderPaid($order);
        }

        $order->save();

        // Also update payment record if exists
        if ($order->payment && $request->payment_status === Order::PAYMENT_PAID) {
            $order->payment->markAsCompleted('MANUAL-' . time());
        }

        return back()->with('success', 'Estado de pago actualizado.');
    }

    public function generateInvoice(Order $order)
    {
        if ($order->invoice) {
            return back()->with('info', 'La boleta ya fue generada.');
        }

        $this->invoiceService->generate($order, 'boleta');

        return back()->with('success', 'Boleta generada exitosamente.');
    }

    public function downloadInvoice(Order $order)
    {
        $invoice = $this->invoiceService->getOrGenerate($order);

        return $this->invoiceService->download($invoice);
    }

    protected function processOrderPaid(Order $order): void
    {
        // Decrease stock for each item
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->decreaseStock(
                    $item->quantity,
                    'sale',
                    Order::class,
                    $order->id,
                    auth()->id()
                );
                $item->product->incrementSales($item->quantity);
            }
        }

        // Generate invoice
        if (!$order->invoice) {
            $this->invoiceService->generate($order, 'boleta');
        }
    }
}
